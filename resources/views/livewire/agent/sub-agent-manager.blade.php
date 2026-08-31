<div class="space-y-6 pb-12 animate-fade-in font-sans">
    {{-- ============================================ --}}
    {{-- 1. PAGE HEADER                               --}}
    {{-- ============================================ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#F8FAFC] tracking-tight">مدیریت زیرنمایندگان</h1>
            <p class="text-xs text-[#94A3B8] mt-1">مدیریت نمایندگان زیرمجموعه، موجودی کیف پول و عملکرد فروش</p>
        </div>

        <button wire:click="openCreateModal"
                class="px-5 py-2.5 bg-[#6366F1] hover:bg-[#4F46E5] text-white rounded-xl font-bold text-sm transition shadow-lg shadow-[#6366F1]/20 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>ایجاد زیرنماینده</span>
        </button>
    </div>

    {{-- ============================================ --}}
    {{-- 2. STATISTICS CARDS                          --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm hover:border-[#6366F1]/30 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-[#6366F1]/10 flex items-center justify-center text-[#6366F1] shrink-0 group-hover:bg-[#6366F1]/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider block">کل زیرنمایندگان</span>
                    <span class="text-2xl font-black text-[#F8FAFC] font-mono tabular-nums">{{ $stats['totalCount'] }}</span>
                    <span class="text-xs text-[#94A3B8] mr-1">نفر</span>
                </div>
            </div>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm hover:border-[#10B981]/30 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-[#10B981]/10 flex items-center justify-center text-[#10B981] shrink-0 group-hover:bg-[#10B981]/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider block">نمایندگان فعال</span>
                    <span class="text-2xl font-black text-[#10B981] font-mono tabular-nums">{{ $stats['activeCount'] }}</span>
                    <span class="text-xs text-[#94A3B8] mr-1">نفر</span>
                </div>
            </div>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm hover:border-[#6366F1]/30 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-[#F59E0B]/10 flex items-center justify-center text-[#F59E0B] shrink-0 group-hover:bg-[#F59E0B]/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider block">مجموع موجودی</span>
                    <span class="text-2xl font-black text-[#F8FAFC] font-mono tabular-nums">{{ number_format($stats['totalBalance']) }}</span>
                    <span class="text-xs text-[#94A3B8] mr-1">تومان</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 3. ALERT / FLASH MESSAGES                    --}}
    {{-- ============================================ --}}
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 4. TOOLBAR (Search + Results)               --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative w-full sm:w-80">
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="جستجو در نام، شماره تماس یا نام کاربری..."
                   class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl py-2.5 pr-10 pl-4 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
        </div>
        <div class="text-xs text-[#94A3B8]">
            نمایش <span class="font-bold text-[#F8FAFC]">{{ $subAgents->total() }}</span> زیرنماینده
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 5. TABLE (Desktop) / CARD LIST (Mobile)     --}}
    {{-- ============================================ --}}
    <div class="bg-[#111722] border border-[#202938] rounded-2xl shadow-sm overflow-hidden">
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="bg-[#080B12] border-b border-[#202938] text-[#94A3B8] text-[10px] font-bold uppercase tracking-wider">
                    <th class="p-4">زیرنماینده</th>
                    <th class="p-4">اطلاعات تماس</th>
                    <th class="p-4">موجودی کیف پول</th>
                    <th class="p-4 text-center">اکانت‌ها</th>
                    <th class="p-4">وضعیت</th>
                    <th class="p-4 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-[#202938] text-sm">
                @forelse($subAgents as $agent)
                    <tr class="hover:bg-[#171E2B]/40 transition group">
                        {{-- Agent Info --}}
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#6366F1]/10 text-[#6366F1] font-bold flex items-center justify-center text-sm shrink-0">
                                    {{ mb_substr($agent->name, 0, 1) }}
                                </div>
                                <div>
                                    <strong class="block text-[#F8FAFC] font-bold text-sm">{{ $agent->name }}</strong>
                                    <span class="text-xs text-[#94A3B8] font-mono" dir="ltr">@ {{ $agent->username }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Contact --}}
                        <td class="p-4">
                            <span class="block text-xs font-mono text-[#F8FAFC]" dir="ltr">{{ $agent->phone ?? '-' }}</span>
                            <span class="block text-[10px] text-[#94A3B8] truncate max-w-[150px]">{{ $agent->email ?? '—' }}</span>
                        </td>

                        {{-- Balance --}}
                        <td class="p-4">
                            <span class="font-bold text-[#F8FAFC] font-mono tabular-nums text-base">{{ number_format($agent->balance) }}</span>
                            <span class="text-[10px] text-[#94A3B8] mr-1">تومان</span>
                        </td>

                        {{-- Accounts Count --}}
                        <td class="p-4 text-center">
                                <span class="inline-block px-3 py-1 bg-[#202938] rounded-lg text-xs font-bold text-[#94A3B8] font-mono">
                                    {{ $agent->accounts_count ?? 0 }} اکانت
                                </span>
                        </td>

                        {{-- Status --}}
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="toggleStatus({{ $agent->id }})"
                                        wire:loading.attr="disabled"
                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $agent->is_active ? 'bg-[#10B981]' : 'bg-[#94A3B8]' }}">
                                    <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $agent->is_active ? 'translate-x-1' : 'translate-x-5' }}"></span>
                                </button>
                                <span class="text-xs font-bold {{ $agent->is_active ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">
                                        {{ $agent->is_active ? 'فعال' : 'غیرفعال' }}
                                    </span>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button wire:click="openWalletModal({{ $agent->id }})"
                                        class="p-2 rounded-lg bg-[#6366F1]/10 text-[#6366F1] hover:bg-[#6366F1] hover:text-white transition shadow-sm"
                                        title="مدیریت کیف پول"
                                        aria-label="مدیریت کیف پول">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </button>

                                <button wire:click="openEditModal({{ $agent->id }})"
                                        class="p-2 rounded-lg bg-[#202938] text-[#94A3B8] hover:bg-[#6366F1] hover:text-white transition shadow-sm"
                                        title="ویرایش اطلاعات"
                                        aria-label="ویرایش اطلاعات">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-[#94A3B8] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <h4 class="text-sm font-bold text-[#F8FAFC]">
                                    @if($search)
                                        نتیجه‌ای یافت نشد
                                    @else
                                        هیچ زیرنماینده‌ای ایجاد نشده است
                                    @endif
                                </h4>
                                <p class="text-xs text-[#94A3B8] mt-1">
                                    @if($search)
                                        برای عبارت "{{ $search }}" نتیجه‌ای پیدا نشد.
                                    @else
                                        اولین زیرنماینده خود را ایجاد کنید.
                                    @endif
                                </p>
                                @if(!$search)
                                    <button wire:click="openCreateModal" class="mt-4 text-xs text-[#6366F1] hover:text-[#4F46E5] font-bold transition flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        ایجاد اولین زیرنماینده
                                    </button>
                                @else
                                    <button wire:click="$set('search', '')" class="mt-4 text-xs text-[#6366F1] hover:text-[#4F46E5] font-bold transition">
                                        پاک کردن جستجو
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List --}}
        <div class="md:hidden divide-y divide-[#202938]">
            @forelse($subAgents as $agent)
                <div class="p-4 space-y-3 hover:bg-[#171E2B]/40 transition">
                    {{-- Header: Avatar + Name + Status --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#6366F1]/10 text-[#6366F1] font-bold flex items-center justify-center text-sm shrink-0">
                                {{ mb_substr($agent->name, 0, 1) }}
                            </div>
                            <div>
                                <strong class="block text-[#F8FAFC] font-bold text-sm">{{ $agent->name }}</strong>
                                <span class="text-xs text-[#94A3B8] font-mono" dir="ltr">@ {{ $agent->username }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="toggleStatus({{ $agent->id }})"
                                    wire:loading.attr="disabled"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $agent->is_active ? 'bg-[#10B981]' : 'bg-[#94A3B8]' }}">
                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $agent->is_active ? 'translate-x-1' : 'translate-x-5' }}"></span>
                            </button>
                            <span class="text-xs font-bold {{ $agent->is_active ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">
                                {{ $agent->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[#94A3B8] block text-[10px]">شماره تماس</span>
                            <span class="text-[#F8FAFC] font-mono" dir="ltr">{{ $agent->phone ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[#94A3B8] block text-[10px]">ایمیل</span>
                            <span class="text-[#F8FAFC] truncate">{{ $agent->email ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-[#94A3B8] block text-[10px]">موجودی کیف پول</span>
                            <span class="text-[#F8FAFC] font-mono font-bold">{{ number_format($agent->balance) }} تومان</span>
                        </div>
                        <div>
                            <span class="text-[#94A3B8] block text-[10px]">اکانت‌ها</span>
                            <span class="text-[#F8FAFC] font-mono">{{ $agent->accounts_count ?? 0 }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-2 border-t border-[#202938]">
                        <button wire:click="openWalletModal({{ $agent->id }})"
                                class="flex-1 py-2 rounded-lg bg-[#6366F1]/10 text-[#6366F1] hover:bg-[#6366F1] hover:text-white text-xs font-bold transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            کیف پول
                        </button>
                        <button wire:click="openEditModal({{ $agent->id }})"
                                class="flex-1 py-2 rounded-lg bg-[#202938] text-[#94A3B8] hover:bg-[#6366F1] hover:text-white text-xs font-bold transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            ویرایش
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-[#94A3B8] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <h4 class="text-sm font-bold text-[#F8FAFC]">
                            @if($search)
                                نتیجه‌ای یافت نشد
                            @else
                                هیچ زیرنماینده‌ای ایجاد نشده است
                            @endif
                        </h4>
                        <p class="text-xs text-[#94A3B8] mt-1">
                            @if($search)
                                برای عبارت "{{ $search }}" نتیجه‌ای پیدا نشد.
                            @else
                                اولین زیرنماینده خود را ایجاد کنید.
                            @endif
                        </p>
                        @if(!$search)
                            <button wire:click="openCreateModal" class="mt-4 text-xs text-[#6366F1] hover:text-[#4F46E5] font-bold transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                ایجاد اولین زیرنماینده
                            </button>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($subAgents->hasPages())
            <div class="p-4 border-t border-[#202938] bg-[#080B12]">
                {{ $subAgents->links() }}
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- 6. CREATE / EDIT MODAL                      --}}
    {{-- ============================================ --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('isModalOpen', false)"></div>
            <div class="relative w-full max-w-md bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
                {{-- Header --}}
                <div class="sticky top-0 z-10 bg-[#111722] border-b border-[#202938] p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#6366F1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                {{ $editingAgentId ? 'ویرایش زیرنماینده' : 'ایجاد زیرنماینده جدید' }}
                            </h3>
                            <p class="text-[10px] text-[#94A3B8] mt-0.5">
                                {{ $editingAgentId ? 'اطلاعات و دسترسی حساب را مدیریت کنید.' : 'اطلاعات حساب کاربری زیرنماینده را وارد کنید.' }}
                            </p>
                        </div>
                        <button wire:click="$set('isModalOpen', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="saveAgent" class="p-5 space-y-4">
                    {{-- Personal Info --}}
                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">نام و نام خانوادگی <span class="text-[#EF4444]">*</span></label>
                        <input wire:model="name" type="text" placeholder="مثلاً: علی محمدی" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                        @error('name') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">شماره تماس <span class="text-[#EF4444]">*</span></label>
                            <input wire:model="phone" type="text" dir="ltr" placeholder="09123456789" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm font-mono rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                            @error('phone') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            <p class="text-[9px] text-[#94A3B8] mt-1">شماره تماس به عنوان نام کاربری ورود استفاده می‌شود.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">ایمیل (اختیاری)</label>
                            <input wire:model="email" type="email" dir="ltr" placeholder="example@mail.com" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm font-mono rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                            @error('email') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Security Section --}}
                    <div class="pt-2 border-t border-[#202938]">
                        <h4 class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider mb-3">اطلاعات امنیتی</h4>
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">
                                کلمه عبور {{ $editingAgentId ? '(در صورت عدم تغییر خالی بگذارید)' : '' }} <span class="text-[#EF4444]">@if(!$editingAgentId)*@endif</span>
                            </label>
                            <div class="relative">
                                <input wire:model="password"
                                       type="password"
                                       dir="ltr"
                                       placeholder="******"
                                       class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm font-mono rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                                <button type="button"
                                        x-data="{ show: false }"
                                        @click="show = !show; $el.previousElementSibling.type = show ? 'text' : 'password'"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8] hover:text-[#F8FAFC] transition">
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            @error('password') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Status Selection --}}
                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-2">وضعیت حساب کاربری</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button"
                                    wire:click="$set('is_active', 1)"
                                    class="py-2.5 rounded-xl font-bold text-xs border transition flex items-center justify-center gap-2 {{ $is_active == 1 ? 'bg-[#10B981]/20 text-[#10B981] border-[#10B981]/40' : 'bg-[#080B12] text-[#94A3B8] border-[#202938] hover:border-[#202938]' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $is_active == 1 ? 'bg-[#10B981]' : 'bg-[#94A3B8]' }}"></span>
                                فعال
                            </button>
                            <button type="button"
                                    wire:click="$set('is_active', 0)"
                                    class="py-2.5 rounded-xl font-bold text-xs border transition flex items-center justify-center gap-2 {{ $is_active == 0 ? 'bg-[#EF4444]/20 text-[#EF4444] border-[#EF4444]/40' : 'bg-[#080B12] text-[#94A3B8] border-[#202938] hover:border-[#202938]' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $is_active == 0 ? 'bg-[#EF4444]' : 'bg-[#94A3B8]' }}"></span>
                                غیرفعال
                            </button>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="sticky bottom-0 bg-[#111722] pt-4 border-t border-[#202938] flex items-center justify-end gap-3">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-5 py-2.5 rounded-xl bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] text-sm font-bold transition">
                            انصراف
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-[#6366F1] hover:bg-[#4F46E5] text-white text-sm font-bold shadow-lg shadow-[#6366F1]/25 transition flex items-center gap-2">
                            <svg wire:loading wire:target="saveAgent" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                            <span wire:loading.remove wire:target="saveAgent">{{ $editingAgentId ? 'ذخیره تغییرات' : 'ایجاد زیرنماینده' }}</span>
                            <span wire:loading wire:target="saveAgent">در حال ذخیره...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 7. WALLET MODAL                              --}}
    {{-- ============================================ --}}
    @if($isWalletModalOpen)
        @php
            $selectedAgent = $subAgents->firstWhere('id', $editingAgentId);
            $currentBalance = $selectedAgent ? (float)$selectedAgent->balance : 0;
            $amount = (float)$walletAmount;
            $newBalance = $walletType === 'plus' ? $currentBalance + $amount : $currentBalance - $amount;
            $isValid = $walletType === 'minus' ? ($amount > 0 && $amount <= $currentBalance) : ($amount > 0);
            $hasError = !$isValid || !$walletAmount || $amount <= 0;
        @endphp

        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('isWalletModalOpen', false)"></div>
            <div class="relative w-full max-w-sm bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
                {{-- Header --}}
                <div class="sticky top-0 z-10 bg-[#111722] border-b border-[#202938] p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                مدیریت کیف پول
                            </h3>
                            <p class="text-[10px] text-[#94A3B8] mt-0.5">انتقال و مدیریت موجودی زیرنماینده</p>
                        </div>
                        <button wire:click="$set('isWalletModalOpen', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-5 space-y-4">
                    {{-- Agent Info --}}
                    @if($selectedAgent)
                        <div class="flex items-center gap-3 p-3 bg-[#080B12] border border-[#202938] rounded-xl">
                            <div class="w-10 h-10 rounded-xl bg-[#6366F1]/10 text-[#6366F1] font-bold flex items-center justify-center text-sm shrink-0">
                                {{ mb_substr($selectedAgent->name, 0, 1) }}
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-[#F8FAFC]">{{ $selectedAgent->name }}</span>
                                <span class="text-xs text-[#94A3B8] font-mono" dir="ltr">@ {{ $selectedAgent->username }}</span>
                            </div>
                            <div class="mr-auto text-left">
                                <span class="text-[10px] text-[#94A3B8] block">موجودی فعلی</span>
                                <span class="text-sm font-bold text-[#F8FAFC] font-mono">{{ number_format($currentBalance) }} تومان</span>
                            </div>
                        </div>
                    @endif

                    {{-- Transaction Type --}}
                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-2">نوع تراکنش</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button"
                                    wire:click="$set('walletType', 'plus')"
                                    class="py-3 rounded-xl font-bold text-xs border transition flex flex-col items-center gap-1 {{ $walletType === 'plus' ? 'bg-[#10B981]/20 text-[#10B981] border-[#10B981]/40' : 'bg-[#080B12] text-[#94A3B8] border-[#202938] hover:border-[#202938]' }}">
                                <span class="text-lg">+</span>
                                <span>افزایش موجودی</span>
                                <span class="text-[8px] text-[#94A3B8]">شارژ کیف پول</span>
                            </button>
                            <button type="button"
                                    wire:click="$set('walletType', 'minus')"
                                    class="py-3 rounded-xl font-bold text-xs border transition flex flex-col items-center gap-1 {{ $walletType === 'minus' ? 'bg-[#EF4444]/20 text-[#EF4444] border-[#EF4444]/40' : 'bg-[#080B12] text-[#94A3B8] border-[#202938] hover:border-[#202938]' }}">
                                <span class="text-lg">−</span>
                                <span>کسر از کیف پول</span>
                                <span class="text-[8px] text-[#94A3B8]">برداشت موجودی</span>
                            </button>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">مبلغ تراکنش <span class="text-[#EF4444]">*</span></label>
                        <div class="relative">
                            <input wire:model.live="walletAmount"
                                   type="number"
                                   dir="ltr"
                                   placeholder="مثال: 500000"
                                   class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm font-mono rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-[#94A3B8] font-bold">تومان</span>
                        </div>
                        @error('walletAmount') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        @if($walletAmount && (float)$walletAmount > 0)
                            <div class="text-[10px] text-[#94A3B8] mt-1.5">
                                مبلغ انتخاب شده: <span class="text-[#F8FAFC] font-mono font-bold">{{ number_format((float)$walletAmount) }}</span> تومان
                            </div>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">توضیحات (اختیاری)</label>
                        <input wire:model="walletDescription"
                               type="text"
                               placeholder="علت انتقال یا کسر..."
                               class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                    </div>

                    {{-- Transaction Summary --}}
                    @if($walletAmount && (float)$walletAmount > 0 && $selectedAgent)
                        <div class="p-3 bg-[#080B12] border border-[#202938] rounded-xl space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">نوع عملیات</span>
                                <span class="font-bold {{ $walletType === 'plus' ? 'text-[#10B981]' : 'text-[#EF4444]' }}">
                                {{ $walletType === 'plus' ? 'شارژ کیف پول' : 'کسر از کیف پول' }}
                            </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">مبلغ</span>
                                <span class="font-mono font-bold text-[#F8FAFC]">{{ number_format((float)$walletAmount) }} تومان</span>
                            </div>
                            <div class="flex justify-between border-t border-[#202938] pt-1.5">
                                <span class="text-[#94A3B8]">موجودی پس از تراکنش</span>
                                <span class="font-mono font-bold {{ $isValid && $newBalance >= 0 ? 'text-[#F8FAFC]' : 'text-[#EF4444]' }}">
                                {{ number_format(max(0, $newBalance)) }} تومان
                            </span>
                            </div>
                            @if(!$isValid && $walletType === 'minus')
                                <div class="text-[#EF4444] text-[10px] font-bold mt-1">
                                    ⚠️ موجودی زیرنماینده برای انجام این تراکنش کافی نیست.
                                </div>
                            @endif
                            @if(!$isValid && $walletType === 'plus' && (float)$walletAmount <= 0)
                                <div class="text-[#EF4444] text-[10px] font-bold mt-1">
                                    ⚠️ مبلغ باید بیشتر از صفر باشد.
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="sticky bottom-0 bg-[#111722] pt-4 border-t border-[#202938] flex items-center justify-end gap-3">
                        <button type="button" wire:click="$set('isWalletModalOpen', false)" class="px-5 py-2.5 rounded-xl bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] text-sm font-bold transition">
                            انصراف
                        </button>
                        <button wire:click="processWalletTransaction"
                                wire:loading.attr="disabled"
                                @if($hasError) disabled @endif
                                class="px-6 py-2.5 rounded-xl text-white text-sm font-bold shadow-lg transition flex items-center gap-2 {{ !$hasError ? ($walletType === 'plus' ? 'bg-[#10B981] hover:bg-[#059669] shadow-[#10B981]/25' : 'bg-[#EF4444] hover:bg-[#DC2626] shadow-[#EF4444]/25') : 'bg-[#202938] text-[#94A3B8] cursor-not-allowed' }}">
                            <svg wire:loading wire:target="processWalletTransaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                            <span wire:loading.remove wire:target="processWalletTransaction">تایید و ثبت تراکنش</span>
                            <span wire:loading wire:target="processWalletTransaction">در حال پردازش...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
