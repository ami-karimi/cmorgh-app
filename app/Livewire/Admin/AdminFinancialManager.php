<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Financial;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AdminFinancialManager extends Component
{
    use WithPagination, WithFileUploads;

    // فیلترها و جستجو
    public $search = '';
    public $dateFilter = 'all';
    public $statusFilter = '';
    public $typeFilter = '';

    // متغیرهای مودال بررسی فیش
    public $isReceiptModalOpen = false;
    public $selectedTrx = null;

    // متغیرهای مودال ایجاد / ویرایش کامل تراکنش
    public $isFormOpen = false;
    public $trxId = null;
    public $creator;
    public $for;
    public $type = 'plus';
    public $price;
    public $approved = 1;
    public $description;
    public $attachment;
    public $existingAttachment;

    // 🔍 متغیرهای جدید برای جستجوی زنده در فرم
    public $searchForUser = '';
    public $searchCreatorUser = '';
    public $selectedForUserName = '';
    public $selectedCreatorUserName = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingDateFilter() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingTypeFilter() { $this->resetPage(); }

    // انتخاب کاربر هدف از لیست جستجو
    public function selectForUser($id, $name, $phone)
    {
        $this->for = $id;
        $this->selectedForUserName = $name . ($phone ? ' (' . $phone . ')' : '');
        $this->searchForUser = '';
    }

    // انتخاب ایجادکننده از لیست جستجو
    public function selectCreatorUser($id, $name, $role)
    {
        $this->creator = $id;
        $this->selectedCreatorUserName = $name . ' (' . $role . ')';
        $this->searchCreatorUser = '';
    }

    public function create()
    {
        $this->resetForm();
        $this->creator = auth()->id();
        $admin = auth()->user();
        $this->selectedCreatorUserName = $admin ? $admin->name . ' (' . $admin->role . ')' : 'مدیر کل';
        $this->isFormOpen = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $trx = Financial::findOrFail($id);

        $this->trxId = $trx->id;
        $this->creator = $trx->creator;
        $this->for = $trx->for;
        $this->type = $trx->type;
        $this->price = $trx->price;
        $this->approved = $trx->approved;
        $this->description = $trx->description;
        $this->existingAttachment = $trx->attachment;
        $this->attachment = null;

        // تنظیم اسامی کاربران برای نمایش در اینپوت
        $forUser = User::find($trx->for);
        $this->selectedForUserName = $forUser ? $forUser->name . ($forUser->phone ? ' (' . $forUser->phone . ')' : '') : '';

        $creatorUser = User::find($trx->creator);
        $this->selectedCreatorUserName = $creatorUser ? $creatorUser->name . ' (' . $creatorUser->role . ')' : 'مدیر کل / سیستم';

        $this->isFormOpen = true;
    }

    public function viewReceipt($id)
    {
        $this->selectedTrx = Financial::findOrFail($id);
        $this->isReceiptModalOpen = true;
    }

    public function approveTransaction($id)
    {
        $trx = Financial::findOrFail($id);
        $trx->update(['approved' => 1]);
        $this->isReceiptModalOpen = false;
        session()->flash('message', 'تراکنش تایید شد.');
    }

    public function rejectTransaction($id)
    {
        $trx = Financial::findOrFail($id);
        $trx->update(['approved' => 2]);
        $this->isReceiptModalOpen = false;
        session()->flash('error', 'تراکنش رد شد.');
    }

    public function save()
    {
        $this->validate([
            'creator' => 'required|numeric',
            'for' => 'required|numeric',
            'type' => 'required|in:plus,minus,plus_amn,minus_amn',
            'price' => 'required|numeric|min:0',
            'approved' => 'required|in:0,1,2',
            'description' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:5120',
        ], [
            'creator.required' => 'تعیین ایجادکننده الزامی است.',
            'for.required' => 'تعیین کاربر هدف (صاحب کیف پول) الزامی است.',
            'price.required' => 'وارد کردن مبلغ الزامی است.',
        ]);

        $attachmentPath = $this->existingAttachment;
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('attachments/financial', 'public');
        }

        $data = [
            'creator' => $this->creator,
            'for' => $this->for,
            'type' => $this->type,
            'price' => $this->price,
            'approved' => $this->approved,
            'description' => $this->description,
            'attachment' => $attachmentPath,
        ];

        Financial::updateOrCreate(['id' => $this->trxId], $data);

        session()->flash('message', $this->trxId ? 'اطلاعات تراکنش با موفقیت ویرایش شد.' : 'تراکنش جدید ثبت شد.');
        $this->resetForm();
    }

    public function delete($id)
    {
        $trx = Financial::findOrFail($id);
        if ($trx->attachment && Storage::disk('public')->exists($trx->attachment)) {
            Storage::disk('public')->delete($trx->attachment);
        }
        $trx->delete();
        session()->flash('message', 'تراکنش مورد نظر حذف گردید.');
    }

    public function resetForm()
    {
        $this->reset([
            'trxId', 'creator', 'for', 'type', 'price', 'approved', 'description',
            'attachment', 'existingAttachment', 'searchForUser', 'searchCreatorUser',
            'selectedForUserName', 'selectedCreatorUserName'
        ]);
        $this->isFormOpen = false;
        $this->resetValidation();
    }

    public function render()
    {
        $todayRevenue = Financial::where('approved', 1)
            ->whereIn('type', ['plus', 'plus_amn'])
            ->whereDate('created_at', now()->today())
            ->sum('price');

        $monthRevenue = Financial::where('approved', 1)
            ->whereIn('type', ['plus', 'plus_amn'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('price');

        $pendingCount = Financial::where('approved', 0)->count();
        $pendingAmount = Financial::where('approved', 0)->sum('price');

        $query = Financial::query();

        if ($this->dateFilter === 'today') {
            $query->whereDate('created_at', now()->today());
        } elseif ($this->dateFilter === 'yesterday') {
            $query->whereDate('created_at', now()->yesterday());
        } elseif ($this->dateFilter === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->dateFilter === 'this_month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        if ($this->statusFilter !== '') {
            $query->where('approved', $this->statusFilter);
        }

        if ($this->typeFilter !== '') {
            if ($this->typeFilter === 'plus') {
                $query->whereIn('type', ['plus', 'plus_amn']);
            } elseif ($this->typeFilter === 'minus') {
                $query->whereIn('type', ['minus', 'minus_amn']);
            } else {
                $query->where('type', $this->typeFilter);
            }
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('forUser', function ($u) {
                        $qUser = $this->search;
                        $u->where('name', 'like', '%' . $qUser . '%')
                            ->orWhere('phone', 'like', '%' . $qUser . '%');
                    });
            });
        }

        $transactions = $query->latest('id')->paginate(15);

        // 🔍 واکشی زنده نتایج جستجوی کاربر هدف
        $searchedForUsers = [];
        if (strlen($this->searchForUser) >= 2) {
            $searchedForUsers = User::where('name', 'like', '%' . $this->searchForUser . '%')
                ->orWhere('phone', 'like', '%' . $this->searchForUser . '%')
                ->orWhere('email', 'like', '%' . $this->searchForUser . '%')
                ->take(7)->get();
        }

        // 🔍 واکشی زنده نتایج جستجوی ایجاد کننده
        $searchedCreatorUsers = [];
        if (strlen($this->searchCreatorUser) >= 2) {
            $searchedCreatorUsers = User::where('name', 'like', '%' . $this->searchCreatorUser . '%')
                ->orWhere('phone', 'like', '%' . $this->searchCreatorUser . '%')
                ->take(7)->get();
        }

        return view('livewire.admin.admin-financial-manager', compact(
            'transactions', 'todayRevenue', 'monthRevenue', 'pendingCount', 'pendingAmount',
            'searchedForUsers', 'searchedCreatorUsers'
        ))->layout('layouts.admin')->title('مدیریت مالی و تسویه‌حساب‌ها');
    }
}
