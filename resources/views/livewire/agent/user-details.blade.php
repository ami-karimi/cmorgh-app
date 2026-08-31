<div class="space-y-6 pb-12">
    {{-- ============================================ --}}
    {{-- 1. PAGE HEADER                               --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            {{-- Avatar --}}
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#F59E0B] to-[#D97706] flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-[#F59E0B]/20">
                {{ mb_substr($customer->name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-[#F8FAFC]">{{ $customer->name }}</h1>
                    <span class="inline-flex items-center gap-1.5 text-xs">
                        <span class="w-2 h-2 rounded-full {{ $customer->is_active ? 'bg-[#10B981] animate-pulse' : 'bg-[#EF4444]' }}"></span>
                        <span class="font-bold {{ $customer->is_active ? 'text-[#10B981]' : 'text-[#EF4444]' }}">
                            {{ $customer->is_active ? 'فعال' : 'مسدود' }}
                        </span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#202938] text-[#94A3B8] border border-[#202938]">
                        {{ $customer->role === 'customer' ? 'مشتری عادی' : 'زیرنماینده' }}
                    </span>
                </div>
                <div class="flex items-center gap-3 text-xs text-[#94A3B8] mt-1">
                    <span>شناسه: <span class="font-mono text-[#F8FAFC]">#{{ $customer->id }}</span></span>
                    <span>|</span>
                    <span>عضویت: {{ \Morilog\Jalali\Jalalian::fromCarbon($customer->created_at)->format('Y/m/d') }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('reseller.customers') }}" wire:navigate
               class="px-4 py-2.5 rounded-xl bg-[#202938] hover:bg-[#171E2B] text-[#94A3B8] hover:text-[#F8FAFC] text-xs font-bold transition flex items-center gap-2 border border-[#202938]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                بازگشت به لیست
            </a>

            <button wire:click="openEditModal"
                    class="px-4 py-2.5 rounded-xl bg-[#202938] hover:bg-[#6366F1] text-[#94A3B8] hover:text-white text-xs font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                ویرایش مشتری
            </button>

            <a href="{{ route('reseller.accounts.create', ['customer_id' => $customer->id]) }}" wire:navigate
               class="px-5 py-2.5 rounded-xl bg-[#6366F1] hover:bg-[#4F46E5] text-white text-xs font-bold transition shadow-lg shadow-[#6366F1]/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                صدور اکانت جدید
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('profile_msg'))
        <div class="p-4 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('profile_msg') }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 2. KPI SUMMARY CARDS                         --}}
    {{-- ============================================ --}}
    @php
        $totalAccounts = $customer->vpnAccounts->count();
        $activeAccounts = $customer->vpnAccounts->where('is_enabled', 1)->count();
        $expiredAccounts = $customer->vpnAccounts->filter(fn($a) => $a->expire_date && \Carbon\Carbon::parse($a->expire_date)->isPast())->count();
        $totalPurchases = \App\Models\Financial::where('for', $customer->id)
            ->whereIn('type', ['minus'])
            ->where('approved', 1)
            ->sum('price');
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">موجودی کیف پول</span>
                <span class="text-xl">💰</span>
            </div>
            <span class="text-xl font-bold text-[#F8FAFC] font-mono mt-1 block" dir="ltr">
                {{ number_format($balance) }}
            </span>
            <span class="text-[10px] text-[#94A3B8]">تومان</span>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">کل سرویس‌ها</span>
                <span class="text-xl">📡</span>
            </div>
            <span class="text-xl font-bold text-[#F8FAFC] mt-1 block">{{ $totalAccounts }}</span>
            <span class="text-[10px] text-[#94A3B8]">سرویس</span>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">سرویس‌های فعال</span>
                <span class="text-xl text-[#10B981]">●</span>
            </div>
            <span class="text-xl font-bold text-[#10B981] mt-1 block">{{ $activeAccounts }}</span>
            <span class="text-[10px] text-[#94A3B8]">فعال</span>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">سرویس‌های منقضی</span>
                <span class="text-xl text-[#EF4444]">✕</span>
            </div>
            <span class="text-xl font-bold text-[#EF4444] mt-1 block">{{ $expiredAccounts }}</span>
            <span class="text-[10px] text-[#94A3B8]">منقضی</span>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">مجموع خرید</span>
                <span class="text-xl text-[#F59E0B]">🛒</span>
            </div>
            <span class="text-xl font-bold text-[#F59E0B] font-mono mt-1 block" dir="ltr">
                {{ number_format($totalPurchases) }}
            </span>
            <span class="text-[10px] text-[#94A3B8]">تومان</span>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 3. MAIN GRID: Customer Info + VPN Services   --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Customer Information Card --}}
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-[#F8FAFC]">اطلاعات مشتری</h3>
                <button wire:click="openEditModal" class="p-2 rounded-lg bg-[#202938] text-[#94A3B8] hover:bg-[#6366F1] hover:text-white transition" title="ویرایش">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center py-2.5 border-b border-[#202938]">
                    <span class="text-xs text-[#94A3B8]">ایجاد کننده</span>
                    <span class="text-xs font-bold text-[#F59E0B]">{{ $creatorName }}</span>
                </div>

                <div class="flex justify-between items-center py-2.5 border-b border-[#202938]">
                    <span class="text-xs text-[#94A3B8] flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        شماره تماس
                    </span>
                    <span class="text-xs font-mono text-[#F8FAFC]" dir="ltr">{{ $customer->phone ?? 'ثبت نشده' }}</span>
                </div>

                <div class="flex justify-between items-center py-2.5 border-b border-[#202938]">
                    <span class="text-xs text-[#94A3B8] flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        آدرس ایمیل
                    </span>
                    <span class="text-xs font-mono text-[#F8FAFC]" dir="ltr">{{ $customer->email ?? 'ثبت نشده' }}</span>
                </div>

                <div class="flex justify-between items-center py-2.5 border-b border-[#202938]">
                    <span class="text-xs text-[#94A3B8]">نقش کاربر</span>
                    <span class="text-xs font-bold text-[#F8FAFC]">{{ $customer->role === 'customer' ? 'مشتری عادی' : 'زیرنماینده' }}</span>
                </div>

                <div class="flex justify-between items-center py-2.5 border-b border-[#202938]">
                    <span class="text-xs text-[#94A3B8]">تاریخ عضویت</span>
                    <span class="text-xs font-mono text-[#F8FAFC]">{{ \Morilog\Jalali\Jalalian::fromCarbon($customer->created_at)->format('Y/m/d') }}</span>
                </div>

                <div class="flex justify-between items-center pt-2.5">
                    <span class="text-xs text-[#94A3B8] flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        دسترسی ورود
                    </span>
                    <button wire:click="toggleUserStatus"
                            wire:loading.attr="disabled"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $customer->is_active ? 'bg-[#10B981]' : 'bg-[#94A3B8]' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $customer->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: VPN Services --}}
        <div class="lg:col-span-2 bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#6366F1]/10 border border-[#6366F1]/20 flex items-center justify-center text-[#6366F1]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#F8FAFC]">سرویس‌های VPN</h3>
                        <p class="text-xs text-[#94A3B8]">{{ $totalAccounts }} سرویس • {{ $activeAccounts }} فعال</p>
                    </div>
                </div>
                <a href="{{ route('reseller.accounts.create', ['customer_id' => $customer->id]) }}" wire:navigate
                   class="px-4 py-2 rounded-xl bg-[#6366F1] hover:bg-[#4F46E5] text-white text-xs font-bold transition shadow-lg shadow-[#6366F1]/20 flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    صدور اکانت جدید
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#080B12] text-[#94A3B8] font-bold border-b border-[#202938]">
                    <tr>
                        <th class="p-4">سرویس</th>
                        <th class="p-4">پلن</th>
                        <th class="p-4">وضعیت</th>
                        <th class="p-4">انقضا</th>
                        <th class="p-4 text-center">عملیات</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-[#202938] text-[#F8FAFC]">
                    @forelse($customer->vpnAccounts as $account)
                        @php
                            $isExpired = $account->expire_date && \Carbon\Carbon::parse($account->expire_date)->isPast();
                            $daysLeft = $account->expire_date ? now()->diffInDays(\Carbon\Carbon::parse($account->expire_date), false) : null;
                            $expColor = $isExpired ? '#EF4444' : ($daysLeft !== null && $daysLeft <= 7 ? '#F59E0B' : '#10B981');
                            $expLabel = $isExpired ? 'منقضی شده' : ($daysLeft !== null ? $daysLeft . ' روز مانده' : 'نامحدود');
                        @endphp
                        <tr class="hover:bg-[#171E2B]/40 transition">
                            <td class="p-4">
                                <span class="font-mono font-bold text-[#F8FAFC]" dir="ltr">{{ $account->username }}</span>
                                <div class="text-[9px] text-[#94A3B8] mt-0.5">{{ strtoupper($account->service_group) }}</div>
                            </td>
                            <td class="p-4">
                                    <span class="text-[10px] font-bold bg-[#202938] px-2 py-1 rounded text-[#F8FAFC]">
                                        {{ $account->group->name ?? 'نامشخص' }}
                                    </span>
                            </td>
                            <td class="p-4">
                                    <span class="px-2 py-1 rounded text-[10px] font-bold border {{ $account->is_enabled ? 'bg-[#10B981]/10 text-[#10B981] border-[#10B981]/20' : 'bg-[#EF4444]/10 text-[#EF4444] border-[#EF4444]/20' }}">
                                        {{ $account->is_enabled ? 'فعال' : 'مسدود' }}
                                    </span>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col gap-0.5">
                                        <span class="font-mono text-[11px] text-[#F8FAFC]">
                                            {{ $account->expire_date ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($account->expire_date))->format('Y/m/d') : 'نامحدود' }}
                                        </span>
                                    @if($account->expire_date)
                                        <span class="text-[10px] font-bold" style="color: {{ $expColor }};">
                                                {{ $expLabel }}
                                            </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('reseller.accounts.show', $account->id) }}" wire:navigate
                                   class="inline-block px-3 py-1.5 rounded-lg bg-[#202938] hover:bg-[#6366F1] text-[#94A3B8] hover:text-white text-[10px] font-bold transition">
                                    مشاهده
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-[#202938] text-[#94A3B8] flex items-center justify-center mb-4 text-3xl">📭</div>
                                    <h4 class="text-sm font-bold text-[#F8FAFC] mb-1">هنوز سرویسی ثبت نشده است</h4>
                                    <p class="text-xs text-[#94A3B8] mb-4">برای این مشتری اولین سرویس VPN را صادر کنید.</p>
                                    <a href="{{ route('reseller.accounts.create', ['customer_id' => $customer->id]) }}" wire:navigate
                                       class="text-xs text-[#6366F1] hover:text-[#4F46E5] font-bold transition flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        صدور اکانت
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 4. FINANCIAL SECTION                         --}}
    {{-- ============================================ --}}
    <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#F59E0B]/10 border border-[#F59E0B]/20 flex items-center justify-center text-[#F59E0B]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#F8FAFC]">کیف پول و تراکنش‌ها</h3>
                    <p class="text-xs text-[#94A3B8] mt-0.5">موجودی فعلی: <span class="font-bold text-[#10B981] font-mono">{{ number_format($balance) }}</span> تومان</p>
                </div>
            </div>
            <button wire:click="openTrxModal"
                    class="px-5 py-2.5 rounded-xl bg-[#202938] hover:bg-[#6366F1] text-[#94A3B8] hover:text-white text-xs font-bold transition flex items-center gap-2 border border-[#202938] hover:border-[#6366F1]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                ثبت تراکنش دستی
            </button>
        </div>

        {{-- Financial Summary --}}
        @php
            $totalPlus = \App\Models\Financial::where('for', $customer->id)
                ->whereIn('type', ['plus'])
                ->where('approved', 1)
                ->sum('price');
            $totalMinus = \App\Models\Financial::where('for', $customer->id)
                ->whereIn('type', ['minus'])
                ->where('approved', 1)
                ->sum('price');
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-[#080B12] border border-[#202938] rounded-xl p-4">
                <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">مجموع واریز</span>
                <div class="text-lg font-bold text-[#10B981] font-mono mt-1" dir="ltr">{{ number_format($totalPlus) }}</div>
                <span class="text-[10px] text-[#94A3B8]">تومان</span>
            </div>
            <div class="bg-[#080B12] border border-[#202938] rounded-xl p-4">
                <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">مجموع برداشت</span>
                <div class="text-lg font-bold text-[#EF4444] font-mono mt-1" dir="ltr">{{ number_format($totalMinus) }}</div>
                <span class="text-[10px] text-[#94A3B8]">تومان</span>
            </div>
            <div class="bg-[#080B12] border border-[#202938] rounded-xl p-4">
                <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">موجودی فعلی</span>
                <div class="text-lg font-bold text-[#F8FAFC] font-mono mt-1" dir="ltr">{{ number_format($balance) }}</div>
                <span class="text-[10px] text-[#94A3B8]">تومان</span>
            </div>
        </div>

        {{-- Flash Message for Transactions --}}
        @if (session()->has('trx_msg'))
            <div class="p-3 mb-4 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('trx_msg') }}
            </div>
        @endif

        {{-- Transaction Filters --}}
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="text-xs font-bold text-[#94A3B8]">فیلتر:</span>
            <button wire:click="$set('transactionFilter', 'all')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $transactionFilter == 'all' ? 'bg-[#6366F1] text-white' : 'bg-[#202938] text-[#94A3B8] hover:bg-[#171E2B]' }}">
                همه
            </button>
            <button wire:click="$set('transactionFilter', 'plus')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $transactionFilter == 'plus' ? 'bg-[#10B981] text-white' : 'bg-[#202938] text-[#94A3B8] hover:bg-[#171E2B]' }}">
                واریز
            </button>
            <button wire:click="$set('transactionFilter', 'minus')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $transactionFilter == 'minus' ? 'bg-[#EF4444] text-white' : 'bg-[#202938] text-[#94A3B8] hover:bg-[#171E2B]' }}">
                برداشت
            </button>
            <div class="flex-1"></div>
            <div class="relative">
                <svg class="w-4 h-4 absolute right-3 top-2.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="transactionSearch"
                       type="text"
                       placeholder="جستجوی شرح..."
                       class="w-48 bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-lg py-2 pr-9 pl-3 focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#080B12] text-[#94A3B8] font-bold border-b border-[#202938]">
                <tr>
                    <th class="p-4">شرح تراکنش</th>
                    <th class="p-4">نوع</th>
                    <th class="p-4 text-left">مبلغ (تومان)</th>
                    <th class="p-4">تاریخ ثبت</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-[#202938] text-[#F8FAFC]">
                @forelse($transactions as $trx)
                    <tr class="hover:bg-[#171E2B]/40 transition">
                        <td class="p-4 font-medium">{{ $trx->description }}</td>
                        <td class="p-4">
                            @if(in_array($trx->type, ['plus']))
                                <span class="px-2 py-1 rounded text-[10px] font-bold border bg-[#10B981]/10 text-[#10B981] border-[#10B981]/20">واریز</span>
                            @else
                                <span class="px-2 py-1 rounded text-[10px] font-bold border bg-[#EF4444]/10 text-[#EF4444] border-[#EF4444]/20">برداشت</span>
                            @endif
                        </td>
                        <td class="p-4 text-left font-bold font-mono {{ in_array($trx->type, ['plus']) ? 'text-[#10B981]' : 'text-[#EF4444]' }}" dir="ltr">
                            {{ in_array($trx->type, ['plus']) ? '+' : '-' }}{{ number_format($trx->price) }}
                        </td>
                        <td class="p-4 text-[#94A3B8] font-mono">
                            {{ \Morilog\Jalali\Jalalian::fromCarbon($trx->created_at)->format('Y/m/d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center text-[#94A3B8]">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-[#202938] text-[#94A3B8] flex items-center justify-center mb-4 text-3xl">📭</div>
                                <h4 class="text-sm font-bold text-[#F8FAFC] mb-1">هیچ تراکنشی ثبت نشده است</h4>
                                <p class="text-xs text-[#94A3B8]">هنوز هیچ تراکنش مالی برای این مشتری ثبت نشده است.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="pt-4 border-t border-[#202938] mt-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- 5. EDIT MODAL                                --}}
    {{-- ============================================ --}}
    @if($isEditModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('isEditModalOpen', false)"></div>
            <div class="relative w-full max-w-lg bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-5 border-b border-[#202938]">
                    <h3 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#6366F1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        ویرایش مشخصات: {{ $customer->name }}
                    </h3>
                    <button wire:click="$set('isEditModalOpen', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="updateProfile" class="p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">نام و نام خانوادگی <span class="text-[#EF4444]">*</span></label>
                            <input wire:model="editName" type="text" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                            @error('editName') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">نقش کاربر</label>
                            <select wire:model="editRole" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                                <option value="customer">مشتری عادی</option>
                                <option value="sub_agent">زیرنماینده</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">شماره تماس</label>
                            <input wire:model="editPhone" type="text" dir="ltr" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">آدرس ایمیل</label>
                            <input wire:model="editEmail" type="email" dir="ltr" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">
                            رمز عبور <span class="text-[#94A3B8] font-normal">(در صورت عدم تغییر خالی بگذارید)</span>
                        </label>
                        <div class="relative">
                            <input wire:model="editPassword"
                                   type="password"
                                   placeholder="رمز جدید..."
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
                        @error('editPassword') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-[#202938]">
                        <button type="button" wire:click="$set('isEditModalOpen', false)" class="px-5 py-2.5 rounded-xl bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] text-sm font-bold transition">
                            انصراف
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-[#6366F1] hover:bg-[#4F46E5] text-white text-sm font-bold shadow-lg shadow-[#6366F1]/25 transition flex items-center gap-2">
                            <svg wire:loading wire:target="updateProfile" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                            <span wire:loading.remove wire:target="updateProfile">ذخیره تغییرات</span>
                            <span wire:loading wire:target="updateProfile">در حال ذخیره...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 6. TRANSACTION MODAL                         --}}
    {{-- ============================================ --}}
    @if($isTrxModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('isTrxModalOpen', false)"></div>
            <div class="relative w-full max-w-md bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-[#202938]">
                    <h3 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        ثبت تراکنش برای: {{ $customer->name }}
                    </h3>
                    <button wire:click="$set('isTrxModalOpen', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="addTransaction" class="p-5 space-y-4">
                    {{-- Show current balance warning if minus --}}
                    @if($newType == 'minus' && $newPrice > 0 && $newPrice > $balance)
                        <div class="p-3 rounded-xl bg-[#EF4444]/10 border border-[#EF4444]/20 text-[#EF4444] text-xs font-bold flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div>
                                <div class="font-bold">موجودی فعلی: {{ number_format($balance) }} تومان</div>
                                <div>مبلغ برداشت بیشتر از موجودی است!</div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">نوع تراکنش</label>
                        <select wire:model="newType" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                            <option value="plus">افزایش موجودی کاربر (+)</option>
                            <option value="minus">کسر موجودی کاربر (-)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">مبلغ (تومان)</label>
                        <input wire:model="newPrice" type="number" dir="ltr" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                        @error('newPrice') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">شرح تراکنش</label>
                        <input wire:model="newDescription" type="text" placeholder="بابت..." class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#6366F1] transition">
                        @error('newDescription') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-[#202938]">
                        <button type="button" wire:click="$set('isTrxModalOpen', false)" class="px-5 py-2.5 rounded-xl bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] text-sm font-bold transition">
                            انصراف
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-[#6366F1] hover:bg-[#4F46E5] text-white text-sm font-bold shadow-lg shadow-[#6366F1]/25 transition flex items-center gap-2">
                            <svg wire:loading wire:target="addTransaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                            <span wire:loading.remove wire:target="addTransaction">ثبت نهایی</span>
                            <span wire:loading wire:target="addTransaction">در حال ثبت...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
