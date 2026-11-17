<?php

namespace App\Livewire\Admin;

use App\Models\SupportCategory;
use App\Models\SupportFaq;
use App\Models\SupportTicket;
use App\Services\SupportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('مرکز مدیریت پشتیبانی')]
class SupportManager extends Component
{
    use WithPagination;

    // Navigation
    public string $tab = 'tickets';

    // Ticket management
    public ?int $selectedTicket = null;
    public string $reply = '';
    public string $statusFilter = '';
    public string $search = '';

    // FAQ management
    public ?int $editingFaqId = null;
    public string $faqQuestion = '';
    public string $faqAnswer = '';
    public string $faqCategory = '';
    public bool $faqModal = false;
    public string $faqSearch = '';

    // Category management
    public bool $categoryModal = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categoryDescription = '';
    public string $categoryIcon = '';
    public string $categoryAudience = 'all';
    public bool $categoryActive = true;

    public function boot(): void
    {
        abort_unless(
            auth()->check() &&
            in_array(auth()->user()->role, ['admin', 'manager'], true),
            403
        );
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingFaqSearch(): void
    {
        $this->resetPage('faqsPage');
    }

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function openTicket(int $id): void
    {
        $ticket = SupportTicket::findOrFail($id);

        $this->selectedTicket = $ticket->id;
        $this->reply = '';
    }

    public function sendReply(): void
    {
        $this->validate([
            'selectedTicket' => 'required|exists:support_tickets,id',
            'reply' => 'required|string|max:20000',
        ], [
            'reply.required' => 'متن پاسخ را وارد کنید.',
            'reply.max' => 'متن پاسخ بیش از حد طولانی است.',
        ]);

        DB::transaction(function () {
            $ticket = SupportTicket::lockForUpdate()
                ->findOrFail($this->selectedTicket);

            abort_if($ticket->status === 'closed', 422);

            $ticket->messages()->create([
                'user_id' => auth()->id(),
                'message' => trim($this->reply),
            ]);

            $ticket->update([
                'status' => 'answered',
                'last_reply_at' => now(),
                'closed_at' => null,
            ]);
        });

        $this->reply = '';

        session()->flash('success', 'پاسخ با موفقیت ارسال شد.');
    }

    public function setStatus(string $status): void
    {
        abort_unless(
            array_key_exists($status, SupportService::statuses()),
            422
        );

        $ticket = SupportTicket::findOrFail($this->selectedTicket);

        $ticket->update([
            'status' => $status,
            'closed_at' => $status === 'closed' ? now() : null,
        ]);

        session()->flash('success', 'وضعیت تیکت تغییر کرد.');
    }

    // FAQ

    public function openFaq(?int $id = null): void
    {
        $this->resetValidation();

        $this->reset([
            'editingFaqId',
            'faqQuestion',
            'faqAnswer',
            'faqCategory',
        ]);

        if ($id) {
            $faq = SupportFaq::findOrFail($id);

            $this->editingFaqId = $faq->id;
            $this->faqQuestion = $faq->question;
            $this->faqAnswer = $faq->answer;
            $this->faqCategory = (string) ($faq->category_id ?? '');
        }

        $this->faqModal = true;
    }

    public function saveFaq(): void
    {
        $this->validate([
            'faqQuestion' => 'required|string|max:500',
            'faqAnswer' => 'required|string|max:30000',
            'faqCategory' => 'nullable|exists:support_categories,id',
        ], [
            'faqQuestion.required' => 'عنوان سؤال را وارد کنید.',
            'faqAnswer.required' => 'پاسخ سؤال را وارد کنید.',
        ]);

        $data = [
            'question' => trim($this->faqQuestion),
            'answer' => trim($this->faqAnswer),
            'category_id' => $this->faqCategory ?: null,
        ];

        if ($this->editingFaqId) {
            SupportFaq::findOrFail($this->editingFaqId)->update($data);
        } else {
            SupportFaq::create($data + ['is_published' => true]);
        }

        $this->faqModal = false;

        $this->reset([
            'editingFaqId',
            'faqQuestion',
            'faqAnswer',
            'faqCategory',
        ]);

        session()->flash('success', 'سؤال متداول ذخیره شد.');
    }

    public function toggleFaq(int $id): void
    {
        $faq = SupportFaq::findOrFail($id);

        $faq->update([
            'is_published' => !$faq->is_published,
        ]);

        session()->flash('success', 'وضعیت انتشار FAQ تغییر کرد.');
    }

    public function deleteFaq(int $id): void
    {
        SupportFaq::findOrFail($id)->delete();

        session()->flash('success', 'سؤال متداول حذف شد.');
    }

    // Categories

    public function openCategory(?int $id = null): void
    {
        $this->resetValidation();

        $this->reset([
            'editingCategoryId',
            'categoryName',
            'categoryDescription',
            'categoryIcon',
            'categoryAudience',
        ]);

        $this->categoryActive = true;

        if ($id) {
            $category = SupportCategory::findOrFail($id);

            $this->editingCategoryId = $id;
            $this->categoryName = $category->name;
            $this->categoryDescription = $category->description ?? '';
            $this->categoryIcon = $category->icon ?? '';
            $this->categoryAudience = $category->audience ?? 'all';
            $this->categoryActive = (bool) $category->is_active;
        }

        $this->categoryModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate([
            'categoryName' => 'required|string|max:120',
            'categoryDescription' => 'nullable|string|max:500',
            'categoryIcon' => 'nullable|string|max:30',
            'categoryAudience' => 'required|in:all,customers,agents',
        ], [
            'categoryName.required' => 'نام دسته‌بندی الزامی است.',
        ]);

        $data = [
            'name' => trim($this->categoryName),
            'description' => $this->categoryDescription,
            'icon' => $this->categoryIcon,
            'audience' => $this->categoryAudience,
            'is_active' => $this->categoryActive,
        ];

        if ($this->editingCategoryId) {
            SupportCategory::findOrFail($this->editingCategoryId)
                ->update($data);
        } else {
            SupportCategory::create($data + [
                    'slug' => Str::slug($this->categoryName)
                        . '-' . Str::lower(Str::random(6)),
                ]);
        }

        $this->categoryModal = false;

        session()->flash('success', 'دسته‌بندی ذخیره شد.');
    }

    public function toggleCategory(int $id): void
    {
        $category = SupportCategory::findOrFail($id);

        $category->update([
            'is_active' => !$category->is_active,
        ]);
    }

    public function deleteCategory(int $id): void
    {
        $category = SupportCategory::findOrFail($id);

        if (
            $category->tickets()->exists() ||
            $category->faqs()->exists()
        ) {
            session()->flash(
                'error',
                'این دسته‌بندی دارای تیکت یا FAQ است و قابل حذف نیست.'
            );

            return;
        }

        $category->delete();

        session()->flash('success', 'دسته‌بندی حذف شد.');
    }

    public function render()
    {
        $tickets = SupportTicket::with([
            'user',
            'category',
            'account',
        ])
            ->when($this->statusFilter, fn ($q) =>
            $q->where('status', $this->statusFilter)
            )
            ->when(trim($this->search), function ($q) {
                $term = trim($this->search);

                $q->where(function ($query) use ($term) {
                    $query->where('subject', 'like', "%{$term}%")
                        ->orWhere('number', 'like', "%{$term}%")
                        ->orWhereHas('user', fn ($user) =>
                        $user->where('name', 'like', "%{$term}%")
                        );
                });
            })
            ->latest()
            ->paginate(15);

        $selected = $this->selectedTicket
            ? SupportTicket::with([
                'messages.user',
                'user',
                'account',
                'category',
            ])->find($this->selectedTicket)
            : null;

        $categories = SupportCategory::withCount([
            'faqs',
            'tickets',
        ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $activeCategories = $categories->where('is_active', true);

        $faqs = SupportFaq::with('category')
            ->when(trim($this->faqSearch), fn ($q) =>
            $q->where('question', 'like', '%' . trim($this->faqSearch) . '%')
            )
            ->latest()
            ->paginate(12, ['*'], 'faqsPage');

        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'answered' => SupportTicket::where('status', 'answered')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
        ];

        return view('livewire.admin.support-manager', compact(
            'tickets',
            'selected',
            'categories',
            'activeCategories',
            'faqs',
            'stats'
        ));
    }
}
