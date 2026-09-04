<?php

namespace App\Livewire\Admin;

use App\Models\TopupRequest;
use App\Models\BankMessage;
use App\Models\User;
use App\Models\Financial;
use App\Models\AuditLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
#[Title('مدیریت تایید خوکار | همراه سیمرغ')]
#[Layout('layouts.admin')]
class ManageCharges extends Component
{
    use WithPagination;

    public $activeTab = 'requests'; // requests | messages
    public $perPage = 15;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public $filters = [
        'search' => '',
        'status' => '',
        'match_status' => '',
        'date_from' => '',
        'date_to' => '',
        'today' => false,
        'unmatched_only' => false,
        'amount_min' => '',
        'amount_max' => '',
    ];

    public $selectedRequestId = null;
    public $selectedMessageId = null;
    public $showDrawer = false;

    public $showMatchModal = false;
    public $matchRequestId = null;
    public $matchMessageId = null;
    public $matchableMessages = [];

    public $showRejectModal = false;
    public $rejectRequestId = null;
    public $rejectReason = '';

    protected $queryString = [
        'activeTab' => ['except' => 'requests'],
        'filters' => ['except' => []],
    ];

    // ========== متدهای فیلتر و سورت ==========

    public function resetFilters()
    {
        $this->filters = [
            'search' => '',
            'status' => '',
            'match_status' => '',
            'date_from' => '',
            'date_to' => '',
            'today' => false,
            'unmatched_only' => false,
            'amount_min' => '',
            'amount_max' => '',
        ];
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ========== کوئری اصلی درخواست‌ها ==========

    public function getRequestsQuery()
    {
        $query = TopupRequest::with(['user', 'matchedBankMessage']);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('payable_amount', $search)
                    ->orWhere('requested_amount', $search)
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['match_status'])) {
            $query->where('match_status', $this->filters['match_status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if ($this->filters['today']) {
            $query->whereDate('created_at', today());
        }

        if (!empty($this->filters['amount_min'])) {
            $query->where('requested_amount', '>=', (int) $this->filters['amount_min']);
        }
        if (!empty($this->filters['amount_max'])) {
            $query->where('requested_amount', '<=', (int) $this->filters['amount_max']);
        }

        if ($this->filters['unmatched_only']) {
            $query->whereNull('matched_bank_message_id');
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    // ========== آمار ==========

    public function getStats()
    {
        return [
            'pending_count' => TopupRequest::where('status', 'pending')->count(),
            'approved_today' => TopupRequest::where('status', 'approved')->whereDate('created_at', today())->count(),
            'rejected_today' => TopupRequest::where('status', 'rejected')->whereDate('created_at', today())->count(),
            'unprocessed_messages' => BankMessage::where('processed', false)->count(),
            'unmatched_messages' => BankMessage::whereDoesntHave('matchedRequest')->count(),
            'total_today_amount' => TopupRequest::where('status', 'approved')
                ->whereDate('created_at', today())
                ->sum('requested_amount'),
        ];
    }

    // ========== عملیات تأیید ==========

    public function approve($requestId)
    {
        $request = TopupRequest::findOrFail($requestId);

        try {
            DB::transaction(function () use ($request) {
                $request = TopupRequest::where('id', $request->id)->lockForUpdate()->first();

                if (!in_array($request->status, ['pending', 'paid'])) {
                    throw new \Exception('درخواست قابل تأیید نیست.');
                }

                if (Financial::where('top_up_request_id', $request->id)->exists()) {
                    throw new \Exception('این درخواست قبلاً پردازش شده است.');
                }

                $financial = Financial::create([
                    'creator' => $request->user_id,
                    'for' => $request->user_id,
                    'price' => $request->requested_amount,
                    'type' => 'plus',
                    'approved' => 1,
                    'description' => 'شارژ خودکار - درخواست #' . $request->id,
                    'top_up_request_id' => $request->id,
                ]);

                User::find($request->user_id)->increment('balance', $request->requested_amount);

                $oldStatus = $request->status;
                $request->update(['status' => 'approved']);

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'approve_top_up',
                    'entity' => 'topup_request',
                    'entity_id' => $request->id,
                    'old_value' => ['status' => $oldStatus],
                    'new_value' => ['status' => 'approved', 'financial_id' => $financial->id],
                    'ip' => request()->ip(),
                ]);
            });

            $this->dispatch('toast', type: 'success', message: 'درخواست با موفقیت تأیید شد.');
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    // ========== عملیات رد ==========

    public function openRejectModal($requestId)
    {
        $this->rejectRequestId = $requestId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function confirmReject()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:3',
        ]);

        $request = TopupRequest::findOrFail($this->rejectRequestId);

        try {
            DB::transaction(function () use ($request) {
                $request = TopupRequest::where('id', $request->id)->lockForUpdate()->first();

                if (!in_array($request->status, ['pending', 'paid'])) {
                    throw new \Exception('درخواست قابل رد نیست.');
                }

                $oldStatus = $request->status;
                $request->update(['status' => 'rejected']);

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'reject_top_up',
                    'entity' => 'topup_request',
                    'entity_id' => $request->id,
                    'old_value' => ['status' => $oldStatus],
                    'new_value' => ['status' => 'rejected', 'reason' => $this->rejectReason],
                    'ip' => request()->ip(),
                ]);
            });

            $this->showRejectModal = false;
            $this->rejectRequestId = null;
            $this->rejectReason = '';

            $this->dispatch('toast', type: 'success', message: 'درخواست رد شد.');
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    // ========== تطبیق دستی ==========

    public function openMatchModal($requestId)
    {
        $this->matchRequestId = $requestId;
        $this->matchMessageId = null;

        $this->matchableMessages = BankMessage::where('processed', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->showMatchModal = true;
    }

    public function confirmMatch()
    {
        if (!$this->matchMessageId) {
            $this->dispatch('toast', type: 'error', message: 'لطفاً یک پیام بانکی انتخاب کنید.');
            return;
        }

        $request = TopupRequest::findOrFail($this->matchRequestId);
        $message = BankMessage::findOrFail($this->matchMessageId);

        try {
            DB::transaction(function () use ($request, $message) {
                $request = TopupRequest::where('id', $request->id)->lockForUpdate()->first();
                $message = BankMessage::where('id', $message->id)->lockForUpdate()->first();

                if ($message->processed) {
                    throw new \Exception('این پیام بانکی قبلاً پردازش شده است.');
                }

                if ($request->matched_bank_message_id) {
                    throw new \Exception('این درخواست قبلاً تطبیق داده شده است.');
                }

                if ($request->payable_amount != $message->deposit_amount) {
                    throw new \Exception('مبلغ درخواست با مبلغ پیام بانکی مطابقت ندارد.');
                }

                $request->update([
                    'matched_bank_message_id' => $message->id,
                    'matched_at' => now(),
                    'match_status' => 'manual',
                    'status' => 'paid',
                ]);

                $message->update([
                    'processed' => true,
                    'processed_at' => now(),
                ]);

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'manual_match',
                    'entity' => 'topup_request',
                    'entity_id' => $request->id,
                    'old_value' => ['match_status' => $request->match_status],
                    'new_value' => ['match_status' => 'manual', 'message_id' => $message->id],
                    'ip' => request()->ip(),
                ]);
            });

            $this->showMatchModal = false;
            $this->matchRequestId = null;
            $this->matchMessageId = null;

            $this->dispatch('toast', type: 'success', message: 'تطبیق با موفقیت انجام شد.');
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    // ========== جزئیات Drawer ==========

    public function showRequestDetail($id)
    {
        $this->selectedRequestId = $id;
        $this->selectedMessageId = null;
        $this->showDrawer = true;
    }

    public function showMessageDetail($id)
    {
        $this->selectedMessageId = $id;
        $this->selectedRequestId = null;
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->selectedRequestId = null;
        $this->selectedMessageId = null;
    }

    // ========== پردازش مجدد پیام ==========

    public function reprocessMessage($messageId)
    {
        $message = BankMessage::findOrFail($messageId);
        $message->update(['processed' => false]);
        $this->dispatch('toast', type: 'success', message: 'پیام برای پردازش مجدد در صف قرار گرفت.');
    }

    // ========== Render ==========

    public function render()
    {
        $requests = $this->getRequestsQuery()->paginate($this->perPage);

        $messages = BankMessage::with(['matchedRequest.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $stats = $this->getStats();

        $selectedRequest = null;
        $selectedMessage = null;

        if ($this->selectedRequestId) {
            $selectedRequest = TopupRequest::with(['user', 'matchedBankMessage'])->find($this->selectedRequestId);
        }

        if ($this->selectedMessageId) {
            $selectedMessage = BankMessage::with(['matchedRequest.user'])->find($this->selectedMessageId);
        }

        return view('livewire.admin.manage-charges', [
            'requests' => $requests,
            'messages' => $messages,
            'stats' => $stats,
            'selectedRequest' => $selectedRequest,
            'selectedMessage' => $selectedMessage,
        ]);
    }
}
