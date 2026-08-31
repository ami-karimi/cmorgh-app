<div class="space-y-6 animate-fade-in relative pb-24 font-sans">

    <!-- Flash Message -->
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-sm font-bold flex items-center gap-3 shadow-sm mb-4">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 1. PAGE HEADER                               --}}
    {{-- ============================================ --}}
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 bg-[#111722] rounded-[2rem] p-6 sm:p-8 border border-[#202938] shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-[#F8FAFC] tracking-tight">مدیریت اکانت‌ها</h2>
            <p class="text-xs font-medium text-[#94A3B8] mt-1.5">مشاهده، جستجو و کنترل سرویس‌های VPN نمایندگان و مشتریان</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reseller.accounts.create') }}" wire:navigate class="w-full sm:w-auto bg-[#6366F1] hover:bg-[#4F46E5] text-white px-6 py-3 rounded-xl text-sm font-black transition-all flex items-center justify-center gap-2 shadow-[0_8px_20px_-6px_rgba(99,102,241,0.4)] hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                صدور اکانت جدید
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 2. KPI CARDS                                 --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-4 flex flex-col justify-between shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">کل اکانت‌ها</span>
                <span class="w-6 h-6 rounded-lg bg-[#202938] flex items-center justify-center text-[#F8FAFC] text-[10px]">📊</span>
            </div>
            <span class="text-xl font-black text-[#F8FAFC] font-mono-digit">{{ number_format($totalAccounts ?? 0) }}</span>
        </div>
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-4 flex flex-col justify-between shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">اکانت فعال</span>
                <span class="w-6 h-6 rounded-lg bg-[#10B981]/10 flex items-center justify-center text-[#10B981] text-[10px]">●</span>
            </div>
            <span class="text-xl font-black text-[#10B981] font-mono-digit">{{ number_format($activeAccounts ?? 0) }}</span>
        </div>
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-4 flex flex-col justify-between shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">آنلاین الان</span>
                <span class="w-6 h-6 rounded-lg bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6] text-[10px]">◉</span>
            </div>
            <span class="text-xl font-black text-[#3B82F6] font-mono-digit">{{ number_format($onlineAccounts ?? 0) }}</span>
        </div>
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-4 flex flex-col justify-between shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">نزدیک انقضا</span>
                <span class="w-6 h-6 rounded-lg bg-[#F59E0B]/10 flex items-center justify-center text-[#F59E0B] text-[10px]">⚠</span>
            </div>
            <span class="text-xl font-black text-[#F59E0B] font-mono-digit">{{ number_format($expiringAccountsCount ?? 0) }}</span>
        </div>
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-4 flex flex-col justify-between shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">منقضی شده</span>
                <span class="w-6 h-6 rounded-lg bg-[#EF4444]/10 flex items-center justify-center text-[#EF4444] text-[10px]">✕</span>
            </div>
            <span class="text-xl font-black text-[#EF4444] font-mono-digit">{{ number_format($expiredAccountsCount ?? 0) }}</span>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 3. FILTERING SYSTEM                          --}}
    {{-- ============================================ --}}
    <div class="bg-[#111722] border border-[#202938] rounded-[2rem] p-5 sm:p-6 shadow-sm">

        {{-- Quick Filters --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <span class="text-[10px] text-[#94A3B8] font-bold ml-1 uppercase tracking-wider">دسترسی سریع:</span>
            <button wire:click="$set('quickFilter', 'all')" class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold transition-colors {{ $quickFilter == 'all' ? 'bg-[#6366F1] text-white shadow-md' : 'bg-[#171E2B] border border-[#202938] text-[#94A3B8] hover:text-[#F8FAFC]' }}">همه</button>
            <button wire:click="$set('quickFilter', 'active')" class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold transition-colors {{ $quickFilter == 'active' ? 'bg-[#10B981] text-white shadow-md' : 'bg-[#171E2B] border border-[#202938] text-[#94A3B8] hover:text-[#F8FAFC]' }}">فعال</button>
            <button wire:click="$set('quickFilter', 'online')" class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold transition-colors {{ $quickFilter == 'online' ? 'bg-[#3B82F6] text-white shadow-md' : 'bg-[#171E2B] border border-[#202938] text-[#94A3B8] hover:text-[#F8FAFC]' }}">آنلاین</button>
            <button wire:click="$set('quickFilter', 'expiring')" class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold transition-colors {{ $quickFilter == 'expiring' ? 'bg-[#F59E0B] text-white shadow-md' : 'bg-[#171E2B] border border-[#202938] text-[#94A3B8] hover:text-[#F8FAFC]' }}">نزدیک انقضا</button>
            <button wire:click="$set('quickFilter', 'expired')" class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold transition-colors {{ $quickFilter == 'expired' ? 'bg-[#EF4444] text-white shadow-md' : 'bg-[#171E2B] border border-[#202938] text-[#94A3B8] hover:text-[#F8FAFC]' }}">منقضی</button>
            <button wire:click="$set('quickFilter', 'disabled')" class="px-3.5 py-1.5 rounded-lg text-[11px] font-bold transition-colors {{ $quickFilter == 'disabled' ? 'bg-[#475569] text-white shadow-md' : 'bg-[#171E2B] border border-[#202938] text-[#94A3B8] hover:text-[#F8FAFC]' }}">مسدود</button>
        </div>

        {{-- Advanced Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative md:col-span-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="جستجوی نام کاربری..." class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl py-3 pl-4 pr-10 focus:ring-1 focus:ring-[#6366F1] font-mono-digit transition outline-none shadow-inner">
                <svg class="w-4 h-4 text-[#475569] absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div>
                <select wire:model.live="expireFilter" class="w-full bg-[#080B12] border border-[#202938] text-[#94A3B8] text-xs rounded-xl p-3 focus:ring-1 focus:ring-[#6366F1] outline-none shadow-inner cursor-pointer appearance-none">
                    <option value="all">همه زمان‌های انقضا</option>
                    <option value="expiring_5_days">نزدیک انقضا (۵ روز)</option>
                    <option value="expired">منقضی شده‌ها</option>
                    <option value="expired_week_ago">منقضی (بیش از یک هفته)</option>
                </select>
            </div>
            <div>
                <select wire:model.live="onlineFilter" class="w-full bg-[#080B12] border border-[#202938] text-[#94A3B8] text-xs rounded-xl p-3 focus:ring-1 focus:ring-[#6366F1] outline-none shadow-inner cursor-pointer appearance-none">
                    <option value="all">همه اتصالات</option>
                    <option value="online">فقط کاربران آنلاین</option>
                    <option value="offline">فقط کاربران آفلاین</option>
                </select>
            </div>
            <div>
                <select wire:model.live="statusFilter" class="w-full bg-[#080B12] border border-[#202938] text-[#94A3B8] text-xs rounded-xl p-3 focus:ring-1 focus:ring-[#6366F1] outline-none shadow-inner cursor-pointer appearance-none">
                    <option value="all">همه وضعیت‌ها</option>
                    <option value="active">فقط اکانت‌های فعال</option>
                    <option value="disabled">فقط اکانت‌های مسدود</option>
                </select>
            </div>
        </div>

        {{-- Active Filters Indicator --}}
        @if($search || $expireFilter != 'all' || $onlineFilter != 'all' || $statusFilter != 'all' || $quickFilter != 'all')
            <div class="flex flex-wrap items-center gap-2 mt-5 pt-5 border-t border-[#202938]">
                <span class="text-[10px] text-[#94A3B8] font-bold">فیلترهای فعال:</span>
                @if($search)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#202938] text-[#F8FAFC] text-[10px] font-medium">جستجو  {{$search}} <button wire:click="$set('search', '')" class="text-[#94A3B8] hover:text-white">✕</button></span>
                @endif
                @if($expireFilter != 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#202938] text-[#F8FAFC] text-[10px] font-medium">انقضا <button wire:click="$set('expireFilter', 'all')" class="text-[#94A3B8] hover:text-white">✕</button></span>
                @endif
                @if($statusFilter != 'all')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#202938] text-[#F8FAFC] text-[10px] font-medium">وضعیت <button wire:click="$set('statusFilter', 'all')" class="text-[#94A3B8] hover:text-white">✕</button></span>
                @endif
                <button wire:click="resetFilters" class="text-[10px] text-[#6366F1] hover:text-[#4F46E5] font-bold transition mr-2">پاک کردن فیلترها</button>
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- 4. DESKTOP TABLE                             --}}
    {{-- ============================================ --}}
    <div class="hidden md:block bg-[#111722] border border-[#202938] rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="text-[#94A3B8] bg-[#080B12] border-b border-[#202938]">
                <tr>
                    <th class="p-4 font-bold w-12 text-center">
                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-[#202938] bg-[#111722] text-[#6366F1] focus:ring-[#6366F1] cursor-pointer">
                    </th>
                    <th class="p-4 font-bold tracking-wider">اکانت / آنلاین</th>
                    <th class="p-4 font-bold tracking-wider">مشتری / سازنده</th>
                    <th class="p-4 font-bold tracking-wider">سرویس</th>
                    <th class="p-4 font-bold tracking-wider w-40">مصرف</th>
                    <th class="p-4 font-bold tracking-wider">انقضا</th>
                    <th class="p-4 font-bold text-center tracking-wider w-24">وضعیت</th>
                    <th class="p-4 font-bold text-left tracking-wider">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-[#202938] text-[#F8FAFC] relative">
                <div wire:loading wire:target="previousPage, nextPage, gotoPage" class="absolute inset-0 z-10 bg-[#111722]/50 backdrop-blur-sm flex items-center justify-center">
                    <span class="px-4 py-2 bg-[#080B12] border border-[#202938] rounded-xl text-xs font-bold text-[#6366F1] shadow-lg">در حال بارگذاری...</span>
                </div>

                @forelse($accounts as $acc)
                    <tr class="hover:bg-[#171E2B] transition-colors group">
                        <td class="p-4 text-center">
                            <input type="checkbox" wire:model.live="selectedAccounts" value="{{ $acc->id }}" class="w-4 h-4 rounded border-[#202938] bg-[#080B12] text-[#6366F1] focus:ring-[#6366F1] cursor-pointer">
                        </td>

                        <td class="p-4 align-top">
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="relative flex h-2 w-2 shrink-0">
                                    @if($acc->is_online)
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#10B981] opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#10B981]"></span>
                                    @else
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#475569]"></span>
                                    @endif
                                </div>
                                <a href="{{ route('reseller.accounts.show', $acc->id) }}" wire:navigate class="font-mono-digit text-sm font-black text-[#F8FAFC] hover:text-[#6366F1] transition-colors" title="مشاهده جزئیات">
                                    {{ $acc->username }}
                                </a>
                            </div>
                            <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded border {{ $acc->service_group === 'wireguard' ? 'text-[#8B5CF6] bg-[#8B5CF6]/10 border-[#8B5CF6]/20' : 'text-[#F59E0B] bg-[#F59E0B]/10 border-[#F59E0B]/20' }}">
                                    {{ $acc->service_group === 'wireguard' ? 'WG' : $acc->service_group }}
                                </span>
                        </td>

                        <td class="p-4 align-top">
                            @if($acc->panelUser)
                                <a href="{{ route('reseller.users.show', ['id' => $acc->panelUser->id]) }}" class="text-[11px] font-bold text-[#3B82F6] hover:text-[#60A5FA] hover:underline flex items-center gap-1.5 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $acc->panelUser->name ?? $acc->panelUser->username }}
                                </a>
                            @else
                                <span class="text-[11px] font-bold text-[#94A3B8] flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m8-8v16"></path></svg>
                                        بدون مشتری (سیستمی)
                                    </span>
                            @endif
                            <div class="text-[9px] text-[#94A3B8] mt-1.5 flex items-center gap-1">
                                سازنده: <span class="text-[#F8FAFC]">{{ $acc->panelUser->parentAgent->name ?? 'سیستم' }}</span>
                            </div>
                        </td>

                        <td class="p-4 align-top">
                                <span class="text-[10px] font-bold bg-[#080B12] px-2 py-1 rounded-md text-[#94A3B8] border border-[#202938]">
                                    {{ $acc->group->name ?? 'نامشخص' }}
                                </span>
                        </td>

                        <td class="p-4 align-top">
                            @php
                                $downloadGb = round(($acc->download_usage ?? 0) / 1073741824, 2);
                                $maxGb = round(($acc->max_usage ?? 0) / 1073741824, 2);
                            @endphp
                            @if($maxGb <= 0)
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[#10B981] font-mono-digit text-[11px] font-bold">{{ $downloadGb }} GB</span>
                                    <span class="text-[9px] font-bold text-[#10B981] bg-[#10B981]/10 px-1.5 py-0.5 rounded">∞ نامحدود</span>
                                </div>
                            @else
                                @php
                                    $percent = min(100, round(($downloadGb / $maxGb) * 100));
                                    $barColor = $percent >= 90 ? 'bg-[#EF4444]' : ($percent >= 75 ? 'bg-[#F59E0B]' : 'bg-[#10B981]');
                                @endphp
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex justify-between items-center text-[9px] font-bold font-mono-digit">
                                        <span class="text-[#F8FAFC]">{{ $downloadGb }} <span class="text-[#94A3B8] font-sans">از</span> {{ $maxGb }}</span>
                                        <span class="text-[#94A3B8]">{{ $percent }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-[#080B12] rounded-full overflow-hidden border border-[#202938]">
                                        <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </td>

                        <td class="p-4 align-top">
                            @if($acc->expire_date)
                                @php
                                    $expireDate = \Carbon\Carbon::parse($acc->expire_date);
                                    $daysLeft = now()->diffInDays($expireDate, false);
                                    $isExpired = $expireDate->isPast();
                                    $expClass = $isExpired ? 'text-[#EF4444] bg-[#EF4444]/10 border-[#EF4444]/20' : ($daysLeft <= 4 ? 'text-[#F59E0B] bg-[#F59E0B]/10 border-[#F59E0B]/20' : 'text-[#94A3B8] border-transparent');
                                @endphp
                                <div class="flex flex-col gap-1.5">
                                    <span class="font-mono-digit text-[11px] text-[#F8FAFC]">{{ jdate($acc->expire_date)->format('Y/m/d') }}</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded border inline-block w-max {{ $expClass }}">
                                            {{ $isExpired ? 'منقضی شده' : (int)$daysLeft . ' روز تا انقضا' }}
                                        </span>
                                </div>
                            @else
                                <span class="text-[10px] text-[#94A3B8] font-bold bg-[#080B12] px-2 py-0.5 rounded-md border border-[#202938]">در انتظار اتصال</span>
                            @endif
                        </td>

                        <td class="p-4 align-top text-center">
                            <div class="flex flex-col items-center gap-1.5">
                                <button wire:click="toggleStatus({{ $acc->id }})" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $acc->is_enabled ? 'bg-[#10B981]' : 'bg-[#475569]' }}">
                                    <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $acc->is_enabled ? 'translate-x-1' : 'translate-x-4' }}"></span>
                                </button>
                                <span class="text-[9px] font-black {{ $acc->is_enabled ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">{{ $acc->is_enabled ? 'فعال' : 'مسدود' }}</span>
                            </div>
                        </td>

                        <td class="p-4 align-top text-left">
                            <a href="{{ route('reseller.accounts.show', $acc->id) }}" wire:navigate class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-[#080B12] border border-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] hover:border-[#6366F1] hover:bg-[#6366F1]/10 transition-colors text-[10px] font-bold" title="مشاهده">
                                مشاهده
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-[#171E2B] rounded-full flex items-center justify-center mb-4 border border-[#202938]">
                                    <svg class="w-8 h-8 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <span class="text-sm font-black text-[#F8FAFC] mb-1">اکانتی پیدا نشد</span>
                                <span class="text-xs text-[#94A3B8]">با فیلترهای فعلی هیچ سرویسی برای نمایش وجود ندارد.</span>
                                <button wire:click="resetFilters" class="mt-4 px-4 py-2 rounded-xl bg-[#202938] text-white text-xs font-bold hover:bg-[#171E2B] transition">پاک کردن فیلترها</button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($accounts->hasPages())
            <div class="p-4 border-t border-[#202938] bg-[#080B12]">
                {{ $accounts->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- 5. MOBILE CARD VIEW                          --}}
    {{-- ============================================ --}}
    <div class="md:hidden space-y-4">
        @forelse($accounts as $acc)
            @php
                $downloadGb = round(($acc->download_usage ?? 0) / 1073741824, 2);
                $maxGb = round(($acc->max_usage ?? 0) / 1073741824, 2);
                $percent = $maxGb > 0 ? min(100, round(($downloadGb / $maxGb) * 100)) : 0;
                $barColor = $percent >= 90 ? 'bg-[#EF4444]' : ($percent >= 75 ? 'bg-[#F59E0B]' : 'bg-[#10B981]');

                $daysLeft = null;
                $isExpired = false;
                if($acc->expire_date) {
                    $expireDate = \Carbon\Carbon::parse($acc->expire_date);
                    $isExpired = $expireDate->isPast();
                    $daysLeft = (int) now()->diffInDays($expireDate, false);
                }
            @endphp
            <div class="bg-[#111722] border border-[#202938] rounded-[1.5rem] p-5 flex flex-col relative overflow-hidden shadow-sm">
                <!-- Status Bar Indicator -->
                <div class="absolute top-0 right-0 w-1 h-full {{ $acc->is_enabled ? 'bg-[#10B981]' : 'bg-[#475569]' }}"></div>

                <!-- Checkbox (Mobile Bulk) -->
                <div class="absolute top-4 left-4 z-10">
                    <input type="checkbox" wire:model.live="selectedAccounts" value="{{ $acc->id }}" class="w-4 h-4 rounded border-[#202938] bg-[#080B12] text-[#6366F1] focus:ring-[#6366F1]">
                </div>

                <div class="flex items-start gap-3 mb-4 pr-2">
                    <div class="relative mt-1">
                        @if($acc->is_online)
                            <div class="w-2.5 h-2.5 rounded-full bg-[#10B981] animate-pulse"></div>
                        @else
                            <div class="w-2.5 h-2.5 rounded-full bg-[#475569]"></div>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <a href="{{ route('reseller.accounts.show', $acc->id) }}" wire:navigate class="font-mono-digit text-base font-black text-[#F8FAFC]">{{ $acc->username }}</a>
                        <span class="text-[10px] font-bold uppercase text-[#94A3B8] mt-0.5">{{ $acc->service_group === 'wireguard' ? 'WG Peer' : $acc->service_group }}</span>
                    </div>
                </div>

                <div class="bg-[#080B12] rounded-xl border border-[#202938] p-3.5 space-y-3 mb-4">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-[#94A3B8] font-bold">مشتری:</span>
                        <span class="text-[#F8FAFC] font-bold">{{ $acc->panelUser->name ?? ($acc->panelUser->username ?? 'سیستم') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-[#94A3B8] font-bold">سرویس:</span>
                        <span class="text-[#F8FAFC] font-medium text-[11px]">{{ $acc->group->name ?? 'نامشخص' }}</span>
                    </div>
                    <div class="pt-2 border-t border-[#202938]">
                        @if($maxGb <= 0)
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-[#94A3B8] font-bold">مصرف:</span>
                                <span class="text-[#10B981] font-bold">∞ نامحدود ({{ $downloadGb }}GB)</span>
                            </div>
                        @else
                            <div class="flex justify-between items-center text-[10px] font-bold mb-1.5 font-mono-digit">
                                <span class="text-[#F8FAFC]">{{ $downloadGb }} <span class="text-[#94A3B8] font-sans">از</span> {{ $maxGb }} GB</span>
                                <span class="{{ $percent >= 90 ? 'text-[#EF4444]' : ($percent >= 70 ? 'text-[#F59E0B]' : 'text-[#94A3B8]') }}">{{ $percent }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-[#111722] rounded-full overflow-hidden border border-[#202938]">
                                <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-[#94A3B8] mb-0.5">انقضا</span>
                        @if($acc->expire_date)
                            <span class="text-xs font-mono-digit text-[#F8FAFC]">{{ jdate($acc->expire_date)->format('Y/m/d') }}</span>
                            @if($isExpired)
                                <span class="text-[9px] font-black text-[#EF4444] mt-0.5">منقضی شده</span>
                            @else
                                <span class="text-[9px] font-black {{ $daysLeft <= 4 ? 'text-[#F59E0B]' : 'text-[#94A3B8]' }} mt-0.5">{{ $daysLeft }} روز مانده</span>
                            @endif
                        @else
                            <span class="text-[10px] font-bold text-[#94A3B8]">در انتظار اتصال</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <button wire:click="toggleStatus({{ $acc->id }})" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $acc->is_enabled ? 'bg-[#10B981]' : 'bg-[#475569]' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $acc->is_enabled ? 'translate-x-1' : 'translate-x-6' }}"></span>
                        </button>
                        <a href="{{ route('reseller.accounts.show', $acc->id) }}" wire:navigate class="px-4 py-2 bg-[#202938] hover:bg-[#6366F1] text-[#F8FAFC] rounded-xl text-[11px] font-bold transition-colors">
                            مشاهده
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 flex flex-col items-center justify-center bg-[#111722] rounded-[2rem] border border-dashed border-[#202938]">
                <div class="w-14 h-14 bg-[#171E2B] rounded-full flex items-center justify-center mb-3 border border-[#202938]">
                    <svg class="w-6 h-6 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <span class="text-sm font-black text-[#F8FAFC] mb-1">اکانتی پیدا نشد</span>
            </div>
        @endforelse

        @if($accounts->hasPages())
            <div class="pt-2">
                {{ $accounts->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- 6. FLOATING BULK ACTIONS BAR                 --}}
    {{-- ============================================ --}}
    @if(count($selectedAccounts) > 0)
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-xl bg-[#080B12]/90 backdrop-blur-md border border-[#6366F1]/30 rounded-2xl p-3 flex items-center justify-between shadow-[0_10px_30px_rgba(0,0,0,0.5)] animate-fade-in">
            <span class="text-xs text-[#F8FAFC] font-bold mr-2"><span class="text-[#6366F1]">{{ count($selectedAccounts) }}</span> اکانت انتخاب شده</span>
            <div class="flex items-center gap-2">
                <button wire:click="bulkEnable" wire:loading.attr="disabled" class="px-3 py-2 rounded-xl bg-[#10B981] hover:bg-[#059669] text-white text-[11px] font-bold transition-all flex items-center gap-1">
                    <span wire:loading.remove wire:target="bulkEnable">فعال‌سازی</span>
                    <span wire:loading wire:target="bulkEnable">صبر کنید...</span>
                </button>
                <button wire:click="bulkDisable" wire:loading.attr="disabled" class="px-3 py-2 rounded-xl bg-[#EF4444] hover:bg-[#DC2626] text-white text-[11px] font-bold transition-all flex items-center gap-1">
                    <span wire:loading.remove wire:target="bulkDisable">مسدود‌سازی</span>
                    <span wire:loading wire:target="bulkDisable">صبر کنید...</span>
                </button>
                <button wire:click="$set('selectedAccounts', [])" class="p-2 rounded-xl bg-[#171E2B] text-[#94A3B8] hover:text-[#F8FAFC] transition-colors" title="لغو انتخاب">
                    ✕
                </button>
            </div>
        </div>
    @endif
</div>
