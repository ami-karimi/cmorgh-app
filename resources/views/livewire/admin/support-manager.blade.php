@php
    $statuses = \App\Services\SupportService::statuses();

    $panel = 'rounded-3xl border border-zinc-800/80 bg-zinc-900/70 shadow-xl shadow-black/5';
    $input = 'w-full rounded-2xl border border-zinc-700/80 bg-zinc-950/60 px-4 py-3 text-sm text-zinc-200 placeholder:text-zinc-600 transition focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10';

    $tabBase = 'inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-bold transition-all';
    $tabActive = 'bg-brand-500 text-white shadow-lg shadow-brand-500/20';
    $tabInactive = 'text-zinc-400 hover:bg-zinc-800 hover:text-white';

    $ticketStatusColors = [
        'open' => 'bg-sky-500/10 text-sky-300 border-sky-500/20',
        'pending' => 'bg-amber-500/10 text-amber-300 border-amber-500/20',
        'answered' => 'bg-violet-500/10 text-violet-300 border-violet-500/20',
        'closed' => 'bg-zinc-800 text-zinc-400 border-zinc-700',
    ];
@endphp

<div
    dir="rtl"
    x-data="{ faqOpen: @entangle('faqModal'), categoryOpen: @entangle('categoryModal') }"
    @keydown.escape.window="faqOpen = false; categoryOpen = false"
    class="min-h-full space-y-6 pb-10"
>
    {{-- Header --}}
    <section class="relative overflow-hidden rounded-3xl border border-zinc-800/80 bg-gradient-to-br from-zinc-900 via-zinc-900 to-brand-950/30 p-5 sm:p-7">
        <div class="absolute -left-20 -top-20 h-56 w-56 rounded-full bg-brand-500/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <div class="mb-3 flex items-center gap-2 text-xs font-bold text-brand-300">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-brand-500/10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m7-2a8 8 0 11-16 0 8 8 0 0116 0z"/>
                        </svg>
                    </span>
                    مرکز پاسخ‌گویی
                </div>

                <h1 class="text-2xl font-black tracking-tight text-white sm:text-3xl">
                    مدیریت پشتیبانی
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                    مدیریت تیکت‌ها، پاسخ‌گویی به کاربران، FAQها و دسته‌بندی‌های پشتیبانی
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="button"
                    wire:click="$set('faqModal', true)"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-brand-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/20 transition hover:bg-brand-600"
                >
                    <span class="text-lg leading-none">+</span>
                    FAQ جدید
                </button>

                <button
                    type="button"
                    wire:click="openCategory"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-zinc-700 bg-zinc-800/80 px-4 py-3 text-sm font-bold text-zinc-200 transition hover:border-brand-500/50 hover:bg-zinc-800 hover:text-white"
                >
                    <span class="text-lg leading-none">+</span>
                    دسته‌بندی جدید
                </button>
            </div>
        </div>
    </section>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-300">
            <span class="mt-0.5 text-lg">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4 text-sm text-rose-300">
            <span class="mt-0.5 text-lg">!</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Quick stats --}}
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="{{ $panel }} p-4 sm:p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs text-zinc-500">کل تیکت‌ها</span>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-500/10 text-sky-300">◌</span>
            </div>
            <p class="mt-4 text-2xl font-black text-white">{{ $tickets->count() }}</p>
            <p class="mt-1 text-[11px] text-zinc-600">نمایش آخرین ۵۰ تیکت</p>
        </div>

        <div class="{{ $panel }} p-4 sm:p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs text-zinc-500">باز / در انتظار</span>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/10 text-amber-300">◷</span>
            </div>
            <p class="mt-4 text-2xl font-black text-white">
                {{ $tickets->whereIn('status', ['open', 'pending'])->count() }}
            </p>
            <p class="mt-1 text-[11px] text-zinc-600">نیازمند پیگیری</p>
        </div>

        <div class="{{ $panel }} p-4 sm:p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs text-zinc-500">سؤالات متداول</span>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-500/10 text-violet-300">?</span>
            </div>
            <p class="mt-4 text-2xl font-black text-white">{{ $faqs->count() }}</p>
            <p class="mt-1 text-[11px] text-zinc-600">محتوای منتشرشده</p>
        </div>

        <div class="{{ $panel }} p-4 sm:p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs text-zinc-500">دسته‌بندی‌ها</span>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-300">◈</span>
            </div>
            <p class="mt-4 text-2xl font-black text-white">
                {{ $categories->where('is_active', true)->count() }}
            </p>
            <p class="mt-1 text-[11px] text-zinc-600">دسته‌بندی فعال</p>
        </div>
    </section>

    {{-- Tabs --}}
    <div class="flex flex-wrap items-center gap-2 border-b border-zinc-800 pb-3">
        <button
            type="button"
            wire:click="$set('tab', 'tickets')"
            class="{{ $tabBase }} {{ $tab === 'tickets' ? $tabActive : $tabInactive }}"
        >
            <span>تیکت‌ها</span>
            <span class="rounded-lg bg-black/15 px-2 py-0.5 text-xs">{{ $tickets->count() }}</span>
        </button>

        <button
            type="button"
            wire:click="$set('tab', 'faqs')"
            class="{{ $tabBase }} {{ $tab === 'faqs' ? $tabActive : $tabInactive }}"
        >
            <span>سؤالات متداول</span>
            <span class="rounded-lg bg-black/15 px-2 py-0.5 text-xs">{{ $faqs->count() }}</span>
        </button>

        <button
            type="button"
            wire:click="$set('tab', 'categories')"
            class="{{ $tabBase }} {{ $tab === 'categories' ? $tabActive : $tabInactive }}"
        >
            <span>دسته‌بندی‌ها</span>
            <span class="rounded-lg bg-black/15 px-2 py-0.5 text-xs">{{ $categories->count() }}</span>
        </button>
    </div>

    {{-- Tickets --}}
    @if($tab === 'tickets')
        <section class="grid gap-5 xl:grid-cols-12">

            {{-- Ticket list --}}
            <div class="space-y-4 xl:col-span-5">
                <div class="{{ $panel }} p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-white">صندوق تیکت‌ها</h2>
                            <p class="mt-1 text-xs text-zinc-500">برای مشاهده جزئیات، یک تیکت را انتخاب کنید</p>
                        </div>

                        <span wire:loading wire:target="statusFilter" class="text-xs text-brand-300">
                            در حال بارگذاری...
                        </span>
                    </div>

                    <select wire:model.live="statusFilter" class="{{ $input }}">
                        <option value="">همه وضعیت‌ها</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    @forelse($tickets as $ticket)
                        @php
                            $statusClass = $ticketStatusColors[$ticket->status] ?? 'bg-zinc-800 text-zinc-400 border-zinc-700';
                        @endphp

                        <button
                            type="button"
                            wire:click="openTicket({{ $ticket->id }})"
                            class="group w-full rounded-2xl border p-4 text-right transition-all
                            {{ $selectedTicket === $ticket->id
                                ? 'border-brand-500/70 bg-brand-500/10 shadow-lg shadow-brand-500/5'
                                : 'border-zinc-800 bg-zinc-900/60 hover:border-zinc-700 hover:bg-zinc-900' }}"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black text-brand-300">
                                            #{{ $ticket->number }}
                                        </span>

                                        <span class="truncate text-sm font-bold text-white">
                                            {{ $ticket->subject }}
                                        </span>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-zinc-500">
                                        <span>{{ $ticket->user->name ?? 'کاربر' }}</span>
                                        <span>{{ $ticket->category->name ?? 'عمومی' }}</span>
                                    </div>
                                </div>

                                <span class="shrink-0 rounded-lg border px-2 py-1 text-[10px] font-bold {{ $statusClass }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </div>
                        </button>
                    @empty
                        <div class="{{ $panel }} p-10 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-800 text-2xl text-zinc-500">
                                ◌
                            </div>
                            <h3 class="mt-4 font-bold text-zinc-300">تیکتی پیدا نشد</h3>
                            <p class="mt-2 text-xs text-zinc-600">در این وضعیت تیکتی برای نمایش وجود ندارد.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Ticket details --}}
            <div class="xl:col-span-7">
                <div class="{{ $panel }} flex min-h-[620px] flex-col overflow-hidden">
                    @if($selected)
                        <div class="border-b border-zinc-800 p-5 sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="mb-2 flex items-center gap-2">
                                        <span class="text-xs font-black text-brand-300">
                                            #{{ $selected->number }}
                                        </span>

                                        <span class="rounded-lg bg-zinc-800 px-2 py-1 text-[10px] text-zinc-400">
                                            {{ $selected->category->name ?? 'عمومی' }}
                                        </span>
                                    </div>

                                    <h2 class="truncate text-lg font-black text-white">
                                        {{ $selected->subject }}
                                    </h2>

                                    <p class="mt-2 text-xs text-zinc-500">
                                        ارسال‌شده توسط
                                        <span class="text-zinc-300">{{ $selected->user->name ?? 'کاربر' }}</span>
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @foreach($statuses as $key => $label)
                                        <button
                                            type="button"
                                            wire:click="setStatus('{{ $key }}')"
                                            wire:loading.attr="disabled"
                                            class="rounded-xl border px-3 py-2 text-[11px] font-bold transition
                                            {{ $selected->status === $key
                                                ? 'border-brand-500 bg-brand-500 text-white'
                                                : 'border-zinc-700 bg-zinc-800/70 text-zinc-400 hover:border-zinc-600 hover:text-white' }}"
                                        >
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 space-y-4 overflow-y-auto bg-zinc-950/30 p-4 sm:p-6">
                            @forelse($selected->messages as $message)
                                @php
                                    $isMine = $message->user_id === auth()->id();
                                @endphp

                                <div class="flex {{ $isMine ? 'justify-start' : 'justify-end' }}">
                                    <div class="max-w-[92%] rounded-2xl border p-4 sm:max-w-[78%]
                                        {{ $isMine
                                            ? 'border-brand-500/20 bg-brand-500/10'
                                            : 'border-zinc-800 bg-zinc-900' }}"
                                    >
                                        <div class="mb-2 flex items-center justify-between gap-4">
                                            <span class="text-xs font-bold {{ $isMine ? 'text-brand-300' : 'text-zinc-300' }}">
                                                {{ $message->user->name ?? 'سیستم' }}
                                            </span>

                                            @if($isMine)
                                                <span class="text-[10px] text-brand-400">پاسخ شما</span>
                                            @endif
                                        </div>

                                        <p class="whitespace-pre-line text-sm leading-7 text-zinc-300">
                                            {{ $message->message }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-16 text-center text-sm text-zinc-600">
                                    هنوز پیامی برای این تیکت ثبت نشده است.
                                </div>
                            @endforelse
                        </div>

                        @if($selected->status !== 'closed')
                            <div class="border-t border-zinc-800 bg-zinc-900/80 p-4 sm:p-5">
                                <label class="mb-2 block text-xs font-bold text-zinc-400">
                                    پاسخ کارشناس
                                </label>

                                <textarea
                                    wire:model="reply"
                                    rows="4"
                                    class="{{ $input }}"
                                    placeholder="پاسخ خود را برای کاربر بنویسید..."
                                ></textarea>

                                @error('reply')
                                <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                                @enderror

                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <span class="text-[11px] text-zinc-600">
                                        پاسخ شما برای کاربر ثبت و ارسال می‌شود.
                                    </span>

                                    <button
                                        type="button"
                                        wire:click="sendReply"
                                        wire:loading.attr="disabled"
                                        wire:target="sendReply"
                                        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/20 transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        <span wire:loading.remove wire:target="sendReply">ارسال پاسخ</span>
                                        <span wire:loading wire:target="sendReply">در حال ارسال...</span>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="border-t border-zinc-800 bg-zinc-900/80 p-4 text-center text-sm text-zinc-500">
                                این تیکت بسته شده است و امکان ارسال پاسخ جدید ندارد.
                            </div>
                        @endif
                    @else
                        <div class="flex flex-1 flex-col items-center justify-center p-8 text-center">
                            <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-brand-500/10 text-3xl text-brand-300">
                                ◌
                            </div>

                            <h2 class="mt-5 text-lg font-bold text-zinc-300">
                                تیکتی انتخاب نشده است
                            </h2>

                            <p class="mt-2 max-w-sm text-sm leading-6 text-zinc-600">
                                از فهرست سمت راست یک تیکت را انتخاب کنید تا پیام‌ها و جزئیات آن نمایش داده شود.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- FAQs --}}
    @if($tab === 'faqs')
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($faqs as $faq)
                <article class="{{ $panel }} group p-5 transition hover:-translate-y-1 hover:border-brand-500/30">
                    <div class="flex items-start justify-between gap-3">
                        <span class="rounded-lg bg-brand-500/10 px-2.5 py-1 text-[10px] font-bold text-brand-300">
                            {{ $faq->category->name ?? 'عمومی' }}
                        </span>

                        <span class="text-xs text-emerald-400">منتشر شده</span>
                    </div>

                    <h2 class="mt-5 text-base font-black leading-7 text-white">
                        {{ $faq->question }}
                    </h2>

                    <div class="my-4 h-px bg-zinc-800"></div>

                    <p class="line-clamp-5 whitespace-pre-line text-sm leading-7 text-zinc-400">
                        {{ $faq->answer }}
                    </p>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-zinc-700 p-14 text-center">
                    <div class="text-3xl text-zinc-600">?</div>
                    <p class="mt-4 text-sm text-zinc-500">هنوز FAQ ثبت نشده است.</p>
                </div>
            @endforelse
        </section>
    @endif

    {{-- Categories --}}
    @if($tab === 'categories')
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($categories as $category)
                <article class="{{ $panel }} group p-5 transition hover:-translate-y-1 hover:border-brand-500/30">
                    <div class="flex items-start justify-between">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500/10 text-2xl text-brand-300">
                            {{ $category->icon ?: '◈' }}
                        </span>

                        <span class="rounded-lg border px-2.5 py-1 text-[10px] font-bold
                            {{ $category->is_active
                                ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-300'
                                : 'border-zinc-700 bg-zinc-800 text-zinc-500' }}"
                        >
                            {{ $category->is_active ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </div>

                    <h2 class="mt-5 text-lg font-black text-white">
                        {{ $category->name }}
                    </h2>

                    <p class="mt-2 min-h-12 text-sm leading-6 text-zinc-500">
                        {{ $category->description ?: 'برای این دسته‌بندی توضیحی ثبت نشده است.' }}
                    </p>

                    <div class="mt-5 grid grid-cols-2 gap-2">
                        <div class="rounded-xl bg-zinc-950/60 p-3">
                            <p class="text-[10px] text-zinc-600">FAQ</p>
                            <p class="mt-1 font-black text-zinc-200">{{ $category->faqs_count }}</p>
                        </div>

                        <div class="rounded-xl bg-zinc-950/60 p-3">
                            <p class="text-[10px] text-zinc-600">تیکت</p>
                            <p class="mt-1 font-black text-zinc-200">{{ $category->tickets_count }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button
                            type="button"
                            wire:click="openCategory({{ $category->id }})"
                            class="flex-1 rounded-xl bg-zinc-800 py-2.5 text-xs font-bold text-brand-300 transition hover:bg-zinc-700"
                        >
                            ویرایش
                        </button>

                        <button
                            type="button"
                            wire:click="toggleCategory({{ $category->id }})"
                            class="rounded-xl bg-zinc-800 px-3 py-2.5 text-xs font-bold text-amber-300 transition hover:bg-zinc-700"
                        >
                            {{ $category->is_active ? 'غیرفعال' : 'فعال' }}
                        </button>

                        <button
                            type="button"
                            wire:click="deleteCategory({{ $category->id }})"
                            wire:confirm="آیا از حذف این دسته‌بندی مطمئن هستید؟"
                            class="rounded-xl bg-rose-500/10 px-3 py-2.5 text-xs font-bold text-rose-300 transition hover:bg-rose-500/20"
                        >
                            حذف
                        </button>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-zinc-700 p-14 text-center">
                    <div class="text-3xl text-zinc-600">◈</div>
                    <p class="mt-4 text-sm text-zinc-500">هنوز دسته‌بندی پشتیبانی ایجاد نشده است.</p>
                </div>
            @endforelse
        </section>
    @endif

    {{-- FAQ modal --}}
    @if($faqModal)
        <div
            x-show="faqOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
        >
            <div
                @click.stop
                x-transition.scale
                class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-3xl border border-zinc-700 bg-zinc-900 p-5 shadow-2xl sm:p-7"
            >
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-white">افزودن FAQ جدید</h2>
                        <p class="mt-2 text-xs text-zinc-500">یک پاسخ آماده برای سؤالات پرتکرار کاربران ایجاد کنید.</p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('faqModal', false)"
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-800 text-zinc-400 transition hover:bg-zinc-700 hover:text-white"
                    >
                        ×
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">سؤال</label>
                        <input wire:model="faqQuestion" class="{{ $input }}" placeholder="مثلاً: چگونه سرویس خود را تمدید کنم؟">
                        @error('faqQuestion') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">پاسخ کامل</label>
                        <textarea wire:model="faqAnswer" rows="8" class="{{ $input }}" placeholder="پاسخ دقیق و قابل فهم را وارد کنید..."></textarea>
                        @error('faqAnswer') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">دسته‌بندی</label>
                        <select wire:model="faqCategory" class="{{ $input }}">
                            <option value="">دسته‌بندی عمومی</option>
                            @foreach($activeCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" wire:click="$set('faqModal', false)" class="flex-1 rounded-2xl bg-zinc-800 py-3 text-sm font-bold text-zinc-300 transition hover:bg-zinc-700">
                        انصراف
                    </button>

                    <button type="button" wire:click="saveFaq" wire:loading.attr="disabled" class="flex-1 rounded-2xl bg-brand-500 py-3 text-sm font-bold text-white transition hover:bg-brand-600 disabled:opacity-60">
                        ذخیره FAQ
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Category modal --}}
    @if($categoryModal)
        <div
            x-show="categoryOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
        >
            <div
                @click.stop
                x-transition.scale
                class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl border border-zinc-700 bg-zinc-900 p-5 shadow-2xl sm:p-7"
            >
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-white">
                            {{ $editingCategoryId ? 'ویرایش دسته‌بندی' : 'دسته‌بندی جدید' }}
                        </h2>
                        <p class="mt-2 text-xs text-zinc-500">دسته‌بندی‌ها برای مرتب‌سازی تیکت‌ها و FAQها استفاده می‌شوند.</p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('categoryModal', false)"
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-800 text-zinc-400 transition hover:bg-zinc-700 hover:text-white"
                    >
                        ×
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">نام دسته‌بندی</label>
                        <input wire:model="categoryName" class="{{ $input }}" placeholder="مثلاً: مشکلات اتصال">
                        @error('categoryName') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">آیکون یا Emoji</label>
                        <input wire:model="categoryIcon" class="{{ $input }}" placeholder="مثلاً: 🌐">
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">توضیح</label>
                        <textarea wire:model="categoryDescription" rows="3" class="{{ $input }}" placeholder="توضیح کوتاه دسته‌بندی..."></textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">مخاطب</label>
                        <select wire:model="categoryAudience" class="{{ $input }}">
                            <option value="all">همه</option>
                            <option value="customers">مشتریان</option>
                            <option value="agents">نمایندگان</option>
                        </select>
                    </div>

                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-zinc-800 bg-zinc-950/50 p-4">
                        <input wire:model="categoryActive" type="checkbox" class="h-4 w-4 rounded border-zinc-700 bg-zinc-900 text-brand-500 focus:ring-brand-500">
                        <span>
                            <span class="block text-sm font-bold text-zinc-200">دسته‌بندی فعال باشد</span>
                            <span class="mt-1 block text-xs text-zinc-600">در فرم ایجاد تیکت و FAQ نمایش داده شود.</span>
                        </span>
                    </label>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" wire:click="$set('categoryModal', false)" class="flex-1 rounded-2xl bg-zinc-800 py-3 text-sm font-bold text-zinc-300 transition hover:bg-zinc-700">
                        انصراف
                    </button>

                    <button type="button" wire:click="saveCategory" wire:loading.attr="disabled" class="flex-1 rounded-2xl bg-brand-500 py-3 text-sm font-bold text-white transition hover:bg-brand-600 disabled:opacity-60">
                        ذخیره دسته‌بندی
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
