<div class="space-y-6 pb-12">
    {{-- ============================================ --}}
    {{-- 1. PAGE HEADER                               --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#F8FAFC] tracking-tight">مدیریت مشتریان</h1>
            <p class="text-sm text-[#94A3B8] mt-1">مدیریت مشتریان، سرویس‌ها و دسترسی‌های متصل</p>
        </div>

        <button wire:click="openModal"
                class="px-5 py-3 rounded-xl bg-[#6366F1] hover:bg-[#4F46E5] text-white font-bold text-sm shadow-lg shadow-[#6366F1]/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            ثبت مشتری جدید
        </button>
    </div>

    {{-- ============================================ --}}
    {{-- 2. FLASH MESSAGES                            --}}
    {{-- ============================================ --}}
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 3. STATISTICS CARDS                          --}}
    {{-- ============================================ --}}
    @php
        $totalCustomers = $customers->total();
        $activeCustomers = $customers->where('is_active', 1)->count();
        $blockedCustomers = $customers->where('is_active', 0)->count();
        $withService = $customers->filter(fn($c) => $c->vpnAccounts->count() > 0)->count();
        $withoutService = $totalCustomers - $withService;
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">کل مشتریان</span>
                <span class="text-xl">👥</span>
            </div>
            <span class="text-2xl font-bold text-[#F8FAFC] mt-1 block">{{ $totalCustomers }}</span>
            <span class="text-[10px] text-[#94A3B8]">کل مشتریان ثبت‌شده</span>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">فعال</span>
                <span class="text-xl text-[#10B981]">●</span>
            </div>
            <span class="text-2xl font-bold text-[#10B981] mt-1 block">{{ $activeCustomers }}</span>
            <span class="text-[10px] text-[#94A3B8]">دسترسی فعال</span>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">مسدود</span>
                <span class="text-xl text-[#EF4444]">✕</span>
            </div>
            <span class="text-2xl font-bold text-[#EF4444] mt-1 block">{{ $blockedCustomers }}</span>
            <span class="text-[10px] text-[#94A3B8]">دسترسی غیرفعال</span>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">دارای سرویس</span>
                <span class="text-xl text-[#F59E0B]">⚡</span>
            </div>
            <span class="text-2xl font-bold text-[#F59E0B] mt-1 block">{{ $withService }}</span>
            <span class="text-[10px] text-[#94A3B8]">حداقل یک سرویس فعال</span>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">بدون سرویس</span>
                <span class="text-xl text-[#94A3B8]">📭</span>
            </div>
            <span class="text-2xl font-bold text-[#94A3B8] mt-1 block">{{ $withoutService }}</span>
            <span class="text-[10px] text-[#94A3B8]">بدون سرویس فعال</span>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 4. TOOLBAR (Search + Filters)                --}}
    {{-- ============================================ --}}
    <div class="bg-[#111722] border border-[#202938] rounded-xl p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            {{-- Search --}}
            <div class="relative md:col-span-2">
                <svg class="w-4 h-4 absolute right-3 top-3 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="جستجوی نام، شماره تماس یا ایمیل..."
                       class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl py-2.5 pr-10 pl-4 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
            </div>

            {{-- Status Filter --}}
            <div>
                <select wire:model.live="statusFilter"
                        class="w-full bg-[#080B12] border border-[#202938] text-[#94A3B8] text-sm rounded-xl py-2.5 px-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                    <option value="all">وضعیت: همه</option>
                    <option value="active">فعال</option>
                    <option value="blocked">مسدود</option>
                </select>
            </div>

            {{-- Service Filter --}}
            <div>
                <select wire:model.live="serviceFilter"
                        class="w-full bg-[#080B12] border border-[#202938] text-[#94A3B8] text-sm rounded-xl py-2.5 px-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                    <option value="all">سرویس: همه</option>
                    <option value="has">دارای سرویس</option>
                    <option value="none">بدون سرویس</option>
                </select>
            </div>

            {{-- Sort --}}
            <div>
                <select wire:model.live="sortBy"
                        class="w-full bg-[#080B12] border border-[#202938] text-[#94A3B8] text-sm rounded-xl py-2.5 px-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                    <option value="newest">جدیدترین</option>
                    <option value="oldest">قدیمی‌ترین</option>
                    <option value="name">نام مشتری</option>
                    <option value="most_services">بیشترین سرویس</option>
                    <option value="least_services">کمترین سرویس</option>
                </select>
            </div>
        </div>

        {{-- Active Filters --}}
        @if($search || $statusFilter != 'all' || $serviceFilter != 'all' || $sortBy != 'newest')
            <div class="flex flex-wrap items-center gap-3 pt-3 border-t border-[#202938]">
                <span class="text-xs text-[#94A3B8] font-bold">فیلترهای فعال:</span>
                @if($search)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#202938] text-[#F8FAFC] text-xs">
                        جستجو: {{ $search }}
                        <button wire:click="$set('search', '')" class="text-[#94A3B8] hover:text-[#F8FAFC]">✕</button>
                    </span>
                @endif
                @if($statusFilter != 'all')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#202938] text-[#F8FAFC] text-xs">
                        {{ $statusFilter == 'active' ? 'فعال' : 'مسدود' }}
                        <button wire:click="$set('statusFilter', 'all')" class="text-[#94A3B8] hover:text-[#F8FAFC]">✕</button>
                    </span>
                @endif
                @if($serviceFilter != 'all')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#202938] text-[#F8FAFC] text-xs">
                        {{ $serviceFilter == 'has' ? 'دارای سرویس' : 'بدون سرویس' }}
                        <button wire:click="$set('serviceFilter', 'all')" class="text-[#94A3B8] hover:text-[#F8FAFC]">✕</button>
                    </span>
                @endif
                <button wire:click="resetFilters" class="text-xs text-[#6366F1] hover:text-[#4F46E5] font-bold transition">
                    پاک کردن همه
                </button>
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- 5. CUSTOMERS TABLE                           --}}
    {{-- ============================================ --}}
    <div class="bg-[#111722] border border-[#202938] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="border-b border-[#202938] text-[#94A3B8] text-xs font-bold uppercase tracking-wider bg-[#080B12]">
                    <th class="p-5">مشخصات مشتری</th>
                    <th class="p-5">اطلاعات تماس</th>
                    <th class="p-5">سرویس‌های VPN</th>
                    <th class="p-5">آخرین فعالیت</th>
                    <th class="p-5">وضعیت</th>
                    <th class="p-5 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-[#202938] text-sm">
                @forelse($customers as $customer)
                    <tr class="hover:bg-[#171E2B]/40 transition-colors">
                        {{-- Customer Info --}}
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#6366F1]/10 border border-[#6366F1]/20 flex items-center justify-center text-[#6366F1] font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-[#F8FAFC] text-sm">{{ $customer->name }}</div>
                                    <div class="flex items-center gap-2 text-xs text-[#94A3B8] mt-0.5">
                                        <span class="font-mono">ID: #{{ $customer->id }}</span>
                                        <span>|</span>
                                        <span>{{ \Morilog\Jalali\Jalalian::fromCarbon($customer->created_at)->format('Y/m/d') }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Contact --}}
                        <td class="p-5">
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-2 text-[#F8FAFC] font-mono" dir="ltr">
                                    <svg class="w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $customer->phone ?? 'بدون شماره' }}
                                </div>
                                <div class="flex items-center gap-2 text-[#94A3B8] font-mono" dir="ltr">
                                    <svg class="w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ $customer->email ?? 'ثبت نشده' }}
                                </div>
                            </div>
                        </td>

                        {{-- VPN Services --}}
                        <td class="p-5">
                            @php $serviceCount = $customer->vpnAccounts->count(); @endphp
                            @if($serviceCount > 0)
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open"
                                            class="inline-flex items-center px-3 py-1.5 bg-[#6366F1]/10 text-[#6366F1] border border-[#6366F1]/20 text-xs font-bold rounded-lg hover:bg-[#6366F1]/20 transition cursor-pointer">
                                            <span class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
                                                {{ $serviceCount }} سرویس
                                            </span>
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>

                                    {{-- Popover --}}
                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="absolute right-0 mt-2 w-72 bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl z-50 p-4"
                                         style="display: none;">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-xs font-bold text-[#F8FAFC]">سرویس‌های {{ $customer->name }}</h4>
                                            <button @click="open = false" class="text-[#94A3B8] hover:text-[#F8FAFC]">✕</button>
                                        </div>
                                        <div class="space-y-2 max-h-60 overflow-y-auto">
                                            @foreach($customer->vpnAccounts as $acc)
                                                <div class="flex items-center justify-between p-2 bg-[#080B12] border border-[#202938] rounded-xl">
                                                    <div>
                                                        <a href="{{route('reseller.accounts.show',['id' => $acc->id])}}" class="font-mono text-xs font-bold text-[#F8FAFC]" dir="ltr">{{ $acc->username }}</a>
                                                        <span class="text-[10px] text-[#94A3B8] block">{{ $acc->group->name ?? 'نامشخص' }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full {{ $acc->is_enabled ? 'bg-[#10B981]' : ($acc->expire_date && \Carbon\Carbon::parse($acc->expire_date)->isPast() ? 'bg-[#EF4444]' : 'bg-[#94A3B8]') }}"></span>
                                                        <span class="text-[10px] {{ $acc->is_enabled ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">
                                                                {{ $acc->is_enabled ? 'فعال' : 'غیرفعال' }}
                                                            </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-[#94A3B8] bg-[#202938] px-2.5 py-1 rounded-md">بدون سرویس</span>
                            @endif
                        </td>

                        {{-- Last Activity --}}
                        <td class="p-5 text-xs text-[#94A3B8]">
                            {{-- در صورت وجود فیلد last_activity می‌توان نمایش داد --}}
                            <span class="font-mono">{{ $customer->updated_at ? \Morilog\Jalali\Jalalian::fromCarbon($customer->updated_at)->format('Y/m/d H:i') : '—' }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="p-5">
                            <div class="flex items-center gap-2">
                                <button wire:click="toggleStatus({{ $customer->id }})"
                                        wire:loading.attr="disabled"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $customer->is_active ? 'bg-[#10B981]' : 'bg-[#94A3B8]' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $customer->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                <span class="text-xs font-bold {{ $customer->is_active ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">
                                        {{ $customer->is_active ? 'فعال' : 'مسدود' }}
                                    </span>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="p-5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('reseller.users.show', $customer->id) }}"
                                   wire:navigate
                                   class="p-2.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:bg-[#10B981] hover:text-white transition-all"
                                   title="مشاهده پرونده"
                                   aria-label="مشاهده پرونده مشتری">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <button wire:click="edit({{ $customer->id }})"
                                        class="p-2.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:bg-[#6366F1] hover:text-white transition-all"
                                        title="ویرایش مشتری"
                                        aria-label="ویرایش مشتری">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                {{-- More Actions Dropdown --}}
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open"
                                            class="p-2.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:bg-[#202938] hover:text-[#F8FAFC] transition-all"
                                            title="عملیات بیشتر"
                                            aria-label="عملیات بیشتر برای مشتری">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                    </button>

                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="absolute left-0 mt-2 w-48 bg-[#111722] border border-[#202938] rounded-xl shadow-2xl z-50 py-1"
                                         style="display: none;">

                                        {{-- تغییر رمز عبور → باز کردن مودال ویرایش (همان دکمه ویرایش) --}}
                                        <button wire:click="edit({{ $customer->id }})"
                                                @click="open = false"
                                                class="w-full text-right px-4 py-2 text-xs text-[#F8FAFC] hover:bg-[#202938] transition flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 7a2 2 0 011.5.5l1.2 1.2a2 2 0 010 2.8l-2 2-1.2-1.2 2-2L15 8zM9 15l-2 2 1.2 1.2 2-2 1.2 1.2-2 2L4 16v-2h2.5l-2 2L9 15z"/></svg>
                                            تغییر رمز عبور
                                        </button>

                                        {{-- مشاهده سرویس‌ها → صفحه جزئیات مشتری --}}
                                        <a href="{{ route('reseller.users.show', $customer->id) }}"
                                           wire:navigate
                                           @click="open = false"
                                           class="w-full text-right px-4 py-2 text-xs text-[#F8FAFC] hover:bg-[#202938] transition flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            مشاهده سرویس‌ها
                                        </a>

                                        {{-- مشاهده تراکنش‌ها → صفحه مالی با پارامتر مشتری (اختیاری) --}}
                                        <a href="{{ route('reseller.financial') }}?customer_id={{ $customer->id }}"
                                           wire:navigate
                                           @click="open = false"
                                           class="w-full text-right px-4 py-2 text-xs text-[#F8FAFC] hover:bg-[#202938] transition flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            مشاهده تراکنش‌ها
                                        </a>

                                        <div class="border-t border-[#202938] my-1"></div>

                                        {{-- مسدود/فعال --}}
                                        <button wire:click="toggleStatus({{ $customer->id }})"
                                                @click="open = false"
                                                class="w-full text-right px-4 py-2 text-xs {{ $customer->is_active ? 'text-[#EF4444] hover:bg-[#EF4444]/10' : 'text-[#10B981] hover:bg-[#10B981]/10' }} transition flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            {{ $customer->is_active ? 'مسدود کردن' : 'فعال کردن' }}
                                        </button>

                                        {{-- حذف مشتری (با تأیید) --}}
                                        <button wire:click="confirmDelete({{ $customer->id }})"
                                                @click="open = false"
                                                class="w-full text-right px-4 py-2 text-xs text-[#EF4444] hover:bg-[#EF4444]/10 transition flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            حذف مشتری
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-[#202938] text-[#94A3B8] flex items-center justify-center mb-4 text-3xl">👥</div>
                                <h4 class="text-sm font-bold text-[#F8FAFC] mb-1">
                                    @if($search || $statusFilter != 'all' || $serviceFilter != 'all')
                                        مشتری موردنظر پیدا نشد
                                    @else
                                        هنوز مشتری‌ای ثبت نشده است
                                    @endif
                                </h4>
                                <p class="text-xs text-[#94A3B8] mb-4">
                                    @if($search || $statusFilter != 'all' || $serviceFilter != 'all')
                                        با فیلترهای فعلی هیچ مشتری‌ای وجود ندارد.
                                    @else
                                        اولین مشتری خود را ثبت کنید.
                                    @endif
                                </p>
                                @if($search || $statusFilter != 'all' || $serviceFilter != 'all')
                                    <button wire:click="resetFilters" class="text-xs text-[#6366F1] hover:text-[#4F46E5] font-bold transition">
                                        پاک کردن فیلترها
                                    </button>
                                @else
                                    <button wire:click="openModal" class="text-xs text-[#6366F1] hover:text-[#4F46E5] font-bold transition flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        ثبت اولین مشتری
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($customers->hasPages())
            <div class="p-4 border-t border-[#202938] bg-[#080B12]">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- 6. MODAL (Create / Edit Customer)            --}}
    {{-- ============================================ --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('isModalOpen', false)"></div>
            <div class="relative w-full max-w-lg bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-5 border-b border-[#202938]">
                    <h3 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#6366F1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        {{ $customerId ? 'ویرایش مشخصات مشتری' : 'ثبت مشتری جدید' }}
                    </h3>
                    <button wire:click="$set('isModalOpen', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">نام و نام خانوادگی <span class="text-[#EF4444]">*</span></label>
                        <input wire:model="name" type="text" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                        @error('name') <span class="text-[#EF4444] text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">شماره تماس</label>
                        <input wire:model="phone" type="text" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition" dir="ltr">
                        @error('phone') <span class="text-[#EF4444] text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">ایمیل (اختیاری)</label>
                        <input wire:model="email" type="email" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition" dir="ltr">
                        @error('email') <span class="text-[#EF4444] text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">
                            رمز عبور
                            @if(!$customerId) <span class="text-[#EF4444]">*</span> @endif
                        </label>
                        <div class="relative">
                            <input wire:model="password"
                                   type="password"
                                   placeholder="{{ $customerId ? 'در صورت عدم تغییر خالی بگذارید' : '' }}"
                                   class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition"
                                   dir="ltr">
                            <button type="button"
                                    x-data="{ show: false }"
                                    @click="show = !show; $el.previousElementSibling.type = show ? 'text' : 'password'"
                                    class="absolute left-3 top-3 text-[#94A3B8] hover:text-[#F8FAFC] transition">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @error('password') <span class="text-[#EF4444] text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input wire:model="is_active" type="checkbox" id="is_active" class="w-4 h-4 rounded border-[#202938] bg-[#080B12] text-[#6366F1] focus:ring-[#6366F1]/30 focus:ring-offset-0">
                        <label for="is_active" class="text-sm font-medium text-[#F8FAFC] cursor-pointer">حساب کاربری فعال باشد</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-[#202938]">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-5 py-2.5 rounded-xl bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] text-sm font-bold transition">
                            انصراف
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-[#6366F1] hover:bg-[#4F46E5] text-white text-sm font-bold shadow-lg shadow-[#6366F1]/25 transition flex items-center gap-2">
                            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                            <span wire:loading.remove wire:target="save">ذخیره اطلاعات</span>
                            <span wire:loading wire:target="save">در حال ذخیره...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- 7. DELETE CONFIRMATION MODAL                 --}}
    {{-- ============================================ --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('showDeleteModal', false)"></div>
            <div class="relative w-full max-w-sm bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl p-6">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-[#EF4444]/10 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#F8FAFC] mb-2">حذف مشتری</h3>
                    <p class="text-sm text-[#94A3B8] mb-6">آیا از حذف این مشتری اطمینان دارید؟ این عملیات غیرقابل بازگشت است.</p>
                    <div class="flex items-center justify-center gap-3">
                        <button wire:click="$set('showDeleteModal', false)" class="px-5 py-2.5 rounded-xl bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] text-sm font-bold transition">
                            انصراف
                        </button>
                        <button wire:click="deleteCustomer" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-xl bg-[#EF4444] hover:bg-[#DC2626] text-white text-sm font-bold shadow-lg shadow-[#EF4444]/20 transition flex items-center gap-2">
                            <svg wire:loading wire:target="deleteCustomer" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                            <span wire:loading.remove wire:target="deleteCustomer">تأیید حذف</span>
                            <span wire:loading wire:target="deleteCustomer">در حال حذف...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
