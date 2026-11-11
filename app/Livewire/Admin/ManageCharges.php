<?php

namespace App\Livewire\Admin;

use App\Models\TopupRequest;
use App\Models\BankMessage;
use App\Services\BankPaymentService;
use App\Traits\ConvertsDates;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\ValidationException;

#[Title('تایید خودکار کارت به کارت | همراه سیمرغ')]
#[Layout('layouts.admin')]
class ManageCharges extends Component
{
    use WithPagination, ConvertsDates;

    public $activeTab = 'requests';
    public $perPage = 15;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $filters = [
        'search' => '', 'status' => '', 'message_status' => '', 'match_status' => '',
        'date_from' => '', 'date_to' => '', 'today' => false,
        'unmatched_only' => false, 'amount_min' => '', 'amount_max' => '',
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

    protected $queryString = ['activeTab' => ['except' => 'requests'], 'filters' => ['except' => []]];

    public function boot(): void
    {
        // Also protect Livewire actions; route middleware alone is not enough.
        abort_unless(auth()->check() && in_array(auth()->user()->role, ['admin', 'manager'], true), 403);
    }

    public function updatedFilters(): void
    {
        foreach (['date_from', 'date_to', 'amount_min', 'amount_max'] as $key) {
            $value = $this->normalizeDigits((string) ($this->filters[$key] ?? ''));
            $this->filters[$key] = $value;
            $this->resetValidation('filters.'.$key);
            if ($value !== '') {
                if (str_starts_with($key, 'date_') && !$this->filterDate($value)) {
                    $this->addError('filters.'.$key, 'تاریخ شمسی معتبر با قالب ۱۴۰۵/۰۶/۱۲ وارد کنید.');
                } elseif (str_starts_with($key, 'amount_') && (!ctype_digit($value) || (int) $value > 100000000)) {
                    $this->addError('filters.'.$key, 'مبلغ باید عدد صحیح نامنفی برحسب تومان باشد.');
                }
            }
        }
        if (is_numeric($this->filters['amount_min']) && is_numeric($this->filters['amount_max'])
            && $this->filters['amount_min'] > $this->filters['amount_max']) {
            $this->addError('filters.amount_max', 'حداکثر مبلغ نباید کمتر از حداقل باشد.');
        }
        $this->resetPage('requestsPage');
        $this->resetPage('messagesPage');
    }

    public function updatedPerPage(): void { $this->updatedFilters(); }
    public function updatedActiveTab(): void { $this->updatedFilters(); }

    public function resetFilters(): void
    {
        $this->reset('filters');
        $this->resetValidation();
        $this->updatedFilters();
    }

    public function sortBy($field): void
    {
        if (!in_array($field, ['id', 'user_id', 'requested_amount', 'payable_amount', 'created_at'], true)) {
            return;
        }
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
        $this->updatedFilters();
    }

    private function normalizeDigits(string $value): string
    {
        return strtr(trim($value), array_combine(
            preg_split('//u', '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', -1, PREG_SPLIT_NO_EMPTY), str_split('01234567890123456789')
        ));
    }

    private function filterDate(string $value): ?string
    {
        $value = str_replace('-', '/', $this->normalizeDigits($value));
        if (!preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', $value)) { return null; }
        try {
            $date = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $value)->toCarbon();
            return \Morilog\Jalali\Jalalian::fromCarbon($date)->format('Y/m/d') === $value ? $date->toDateString() : null;
        } catch (\Throwable) { return null; }
    }

    private function applyCommonFilters($query, bool $messages = false)
    {
        $filters = array_merge((new self)->filters, $this->filters);
        $search = $this->normalizeDigits((string) $filters['search']);
        if ($search !== '') {
            $query->where(function ($q) use ($search, $messages) {
                $q->where('id', $search);
                if ($messages) {
                    $q->orWhere('account_number', 'like', "%{$search}%")->orWhere('raw_message', 'like', "%{$search}%");
                } else {
                    $phone = (new \App\Models\User(['phone' => $search]))->phone;
                    $q->orWhereHas('user', function ($u) use ($search, $phone) {
                        $u->where('name', 'like', "%{$search}%")->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                        if ($phone) { $u->orWhere('phone', $phone); }
                    });
                }
                if (is_numeric($search)) {
                    if ($messages) {
                        $q->orWhere('deposit_amount', (int) $search * 10);
                    } else {
                        $q->orWhere('requested_amount', (int) $search)->orWhere('payable_amount', (int) $search * 10);
                    }
                }
            });
        }
        foreach (['date_from' => '>=', 'date_to' => '<='] as $key => $operator) {
            if ($filters[$key] && ($date = $this->filterDate((string) $filters[$key]))) {
                $query->whereDate('created_at', $operator, $date);
            }
        }
        if ($filters['today']) { $query->whereDate('created_at', today()); }
        foreach (['amount_min' => '>=', 'amount_max' => '<='] as $key => $operator) {
            if ($filters[$key] !== '' && is_numeric($filters[$key])) {
                $query->where($messages ? 'deposit_amount' : 'requested_amount', $operator, (int) $filters[$key] * ($messages ? 10 : 1));
            }
        }
        $match = $filters['unmatched_only'] ? 'unmatched' : $filters['match_status'];
        if ($messages) {
            $messageStatus = $filters['message_status'];
            if ($messageStatus === 'pending') { $query->where('processed', false); }
            if ($messageStatus === 'checked') { $query->where('processed', true)->whereDoesntHave('matchedRequest'); }
            if ($messageStatus === 'matched') { $query->whereHas('matchedRequest'); }
            if (in_array($match, ['matched', 'unmatched'], true)) {
                $match === 'matched' ? $query->whereHas('matchedRequest') : $query->whereDoesntHave('matchedRequest');
            } elseif (in_array($match, ['manual', 'automatic'], true)) {
                $query->whereHas('matchedRequest', fn ($q) => $q->where('match_type', $match));
            }
        } else {
            if ($match === 'matched') { $query->whereNotNull('matched_bank_message_id'); }
            if ($match === 'unmatched') { $query->whereNull('matched_bank_message_id'); }
            if (in_array($match, ['manual', 'automatic'], true)) { $query->where('match_type', $match); }
        }
        return $query;
    }

    public function getRequestsQuery()
    {
        $query = $this->applyCommonFilters(TopupRequest::with(['user', 'matchedBankMessage']));
        $status = $this->filters['status'] ?? '';
        if (in_array($status, ['approved', 'completed'], true)) {
            $query->whereIn('status', ['completed', 'approved']);
        } elseif (in_array($status, ['pending', 'paid', 'rejected', 'expired'], true)) {
            $query->where('status', $status);
        }
        $sort = in_array($this->sortField, ['id', 'user_id', 'requested_amount', 'payable_amount', 'created_at'], true) ? $this->sortField : 'created_at';
        return $query->orderBy($sort, $this->sortDirection === 'asc' ? 'asc' : 'desc')->orderBy('id', 'desc');
    }

    public function getStats(): array
    {
        $dateColumn = \Illuminate\Support\Facades\DB::raw('COALESCE(settled_at, updated_at)');
        return [
            'pending_count' => TopupRequest::whereIn('status', ['pending', 'paid'])->count(),
            'approved_today' => TopupRequest::whereIn('status', ['completed', 'approved'])->whereDate($dateColumn, today())->count(),
            'rejected_today' => TopupRequest::where('status', 'rejected')->whereDate('updated_at', today())->count(),
            'unprocessed_messages' => BankMessage::where('processed', false)->count(),
            'unmatched_messages' => BankMessage::whereDoesntHave('matchedRequest')->count(),
            'total_today_amount' => TopupRequest::whereIn('status', ['completed', 'approved'])->whereDate($dateColumn, today())->sum('requested_amount'),
        ];
    }

    private function perform(callable $action, string $success): bool
    {
        $this->resetValidation();
        try {
            $action();
            $this->dispatch('toast', type: 'success', message: $success);
            $this->refreshData();
            return true;
        } catch (\Throwable $e) {
            if (!$e instanceof ValidationException) { report($e); }
            $message = $e instanceof ValidationException ? collect($e->errors())->flatten()->first() : 'عملیات انجام نشد؛ خطا در گزارش سیستم ثبت شد.';
            $this->addError('operation', $message);
            $this->dispatch('toast', type: 'error', message: $message);
            return false;
        }
    }

    public function approve($requestId): void
    {
        $this->perform(fn () => app(BankPaymentService::class)->approve((int) $requestId), 'درخواست تأیید و کیف پول شارژ شد.');
    }

    public function openRejectModal($requestId): void
    {
        $this->resetValidation();
        $this->rejectRequestId = $requestId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function confirmReject(): void
    {
        $this->validate(['rejectReason' => 'required|string|min:3|max:1000']);
        if ($this->perform(fn () => app(BankPaymentService::class)->reject((int) $this->rejectRequestId, $this->rejectReason), 'درخواست رد شد.')) {
            $this->reset('showRejectModal', 'rejectRequestId', 'rejectReason');
        }
    }

    public function openMatchModal($requestId): void
    {
        $this->resetValidation();
        $request = TopupRequest::findOrFail($requestId);
        if (!in_array($request->status, ['pending', 'paid'], true) || $request->matched_bank_message_id) {
            $this->addError('operation', 'این درخواست قابل تطبیق نیست.');
            return;
        }
        $this->matchRequestId = $requestId;
        $this->matchMessageId = null;
        $service = app(BankPaymentService::class);
        $this->matchableMessages = BankMessage::whereDoesntHave('matchedRequest')
            ->where('deposit_amount', $request->payable_amount)->orderByDesc('transaction_datetime')->get()
            ->filter(fn ($message) => $service->withinPaymentWindow($request, $message))->values();
        $this->showMatchModal = true;
    }

    public function confirmMatch(): void
    {
        $this->validate(['matchMessageId' => 'required|integer|exists:bank_messages,id']);
        if ($this->perform(fn () => app(BankPaymentService::class)->match((int) $this->matchRequestId, (int) $this->matchMessageId), 'تطبیق انجام شد و کیف پول شارژ شد.')) {
            $this->reset('showMatchModal', 'matchRequestId', 'matchMessageId', 'matchableMessages');
        }
    }

    public function reprocessMessage($messageId): void
    {
        $message = BankMessage::findOrFail($messageId);
        if ($message->matchedRequest()->exists()) {
            $this->addError('operation', 'این پیام قبلاً مصرف شده است؛ شارژ مجدد مجاز نیست.');
            return;
        }
        $this->perform(function () use ($message) {
            if (!app(BankPaymentService::class)->processMessage($message->id)) {
                throw ValidationException::withMessages(['payment' => $message->fresh()->processing_error ?? 'تطبیق جدیدی یافت نشد.']);
            }
        }, 'پیام پردازش شد و کیف پول شارژ شد.');
    }

    public function processPendingMessages(): void
    {
        $this->perform(function () {
            $result = app(BankPaymentService::class)->processPending();
            $this->dispatch('toast', type: 'info', message: "{$result['processed']} پیام بررسی و {$result['matched']} درخواست تأیید شد.");
        }, 'بررسی پیام‌های در انتظار پایان یافت.');
    }

    public function showRequestDetail($id): void
    {
        $this->selectedRequestId = $id; $this->selectedMessageId = null; $this->showDrawer = true;
    }
    public function showMessageDetail($id): void
    {
        $this->selectedMessageId = $id; $this->selectedRequestId = null; $this->showDrawer = true;
    }
    public function closeDrawer(): void { $this->reset('showDrawer', 'selectedRequestId', 'selectedMessageId'); }
    public function refreshData(): void { $this->updatedFilters(); }

    public function render()
    {
        $perPage = in_array((int) $this->perPage, [15, 25, 50, 100], true) ? (int) $this->perPage : 15;
        return view('livewire.admin.manage-charges', [
            'requests' => $this->getRequestsQuery()->paginate($perPage, ['*'], 'requestsPage'),
            'messages' => $this->applyCommonFilters(BankMessage::with('matchedRequest.user'), true)->orderByDesc('id')->paginate($perPage, ['*'], 'messagesPage'),
            'stats' => $this->getStats(),
            'selectedRequest' => $this->selectedRequestId ? TopupRequest::with(['user', 'matchedBankMessage', 'auditLogs.user'])->find($this->selectedRequestId) : null,
            'selectedMessage' => $this->selectedMessageId ? BankMessage::with('matchedRequest.user')->find($this->selectedMessageId) : null,
        ]);
    }
}
