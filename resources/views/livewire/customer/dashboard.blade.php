<div class="space-y-8 animate-fade-in pb-12 font-sans">

    <!-- Announcements Notification Banner -->
    @if($announcements->count() > 0)
        <div class="space-y-3">
            @foreach($announcements as $ann)
                <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20 rounded-2xl p-4 flex items-start gap-4">
                    <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-blue-900 dark:text-blue-100">{{ $ann->title }}</h4>
                        <p class="text-xs text-blue-800/80 dark:text-blue-200/80 mt-1 leading-relaxed">{{ $ann->content }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

<!-- Alert Messages (Session) -->
    @if (session()->has('success_recharge') || session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success_recharge') ?? session('success') }}
        </div>
    @endif

<!-- Welcome Hero & Wallet Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Hero Section -->
        <div class="lg:col-span-2 bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-[2rem] p-6 sm:p-8 flex flex-col justify-center shadow-sm relative overflow-hidden">
            <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">سلام، {{ auth()->user()->name }} 👋</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-medium">همه‌چیز آماده است. وضعیت اتصال شبکه را بررسی کنید.</p>

            <div class="mt-6 flex items-center gap-2 text-xs font-bold bg-zinc-50 dark:bg-zinc-900/50 w-max px-3.5 py-2 rounded-xl border border-zinc-200/60 dark:border-zinc-800">
                <span class="text-zinc-500">وضعیت شبکه:</span>
                @if($hasOutage)
                    <span class="text-rose-500 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span> 🔴 قطعی در شبکه</span>
                @elseif($hasDegraded)
                    <span class="text-amber-500 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> 🟠 اختلال جزئی</span>
                @else
                    <span class="text-emerald-500 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> 🟢 اتصال پایدار</span>
                @endif
            </div>
        </div>

        <!-- Wallet Card -->
        <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 dark:from-[#18181b] dark:to-[#09090b] border border-zinc-800 rounded-[2rem] p-6 sm:p-8 flex flex-col justify-between shadow-xl shadow-zinc-900/10">
            <div>
                <span class="text-xs font-bold text-zinc-400 block mb-1">موجودی کیف پول</span>
                <div class="text-3xl font-black text-white font-mono-digit">
                    {{ number_format($balance) }} <span class="text-sm font-sans font-bold text-zinc-500">تومان</span>
                </div>
            </div>
            <button wire:click="$set('isRechargeModalOpen', true)" class="w-full mt-6 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-black text-sm transition-all shadow-[0_8px_20px_-6px_rgba(16,185,129,0.4)] active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>شارژ حساب</span>
            </button>
        </div>
    </div>

    <!-- Services Header -->
    <div class="pt-4 flex items-center justify-between">
        <h2 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">سرویس‌های من</h2>
        <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-800 px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700">
            {{ $accounts->count() }} سرویس فعال
        </span>
    </div>

    <!-- Services Grid -->
    @if($accounts->count() === 0)
    <!-- Empty State -->
        <div class="bg-white dark:bg-[#111827] border border-dashed border-zinc-300 dark:border-zinc-800 rounded-[2rem] p-12 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-zinc-50 dark:bg-zinc-900/50 text-zinc-400 dark:text-zinc-600 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M20 12H4m8-8v16"></path></svg>
            </div>
            <h3 class="text-lg font-black text-zinc-900 dark:text-white mb-2">هنوز سرویسی ندارید</h3>
            <p class="text-sm font-medium text-zinc-500 max-w-sm mb-6">اولین سرویس خود را تهیه کنید و بلافاصله از اینترنت آزاد لذت ببرید.</p>
            <a href="{{ route('store.index') }}" wire:navigate class="px-6 py-3 rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-bold text-sm shadow-md transition-transform active:scale-95">خرید سرویس جدید</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($accounts as $acc)
            @php
                $totalUsageBytes = $acc->usage ?? (($acc->download_usage ?? 0) + ($acc->upload_usage ?? 0));
                $maxGb = $acc->max_usage > 0 ? round($acc->max_usage / 1073741824, 2) : 0;
                $usedGb = round($totalUsageBytes / 1073741824, 2);
                $remGb = $maxGb > 0 ? max(0, round($maxGb - $usedGb, 2)) : null;
                $percent = $maxGb > 0 ? min(100, round(($usedGb / $maxGb) * 100)) : 0;

                $daysLeft = null;
                $isExpired = false;
                if ($acc->expire_date) {
                    $expireCarbon = \Carbon\Carbon::parse($acc->expire_date);
                    $daysLeft = (int) now()->diffInDays($expireCarbon, false);
                    if ($expireCarbon->isPast()) { $isExpired = true; }
                }

                $isLowVolume = $maxGb > 0 && ($percent >= 85 || ($remGb !== null && $remGb <= 1 && $maxGb > 1));
                $isLowDays = $daysLeft !== null && $daysLeft <= 4 && !$isExpired;
                $needsRecharge = $isLowVolume || $isLowDays || $isExpired || !$acc->is_enabled;

                $progressColor = $percent >= 90 ? 'bg-rose-500' : ($percent >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
                $isWG = $acc->service_group === 'wireguard';
            @endphp

            <!-- Service Card -->
                <div class="bg-white dark:bg-[#111827] border {{ $acc->is_enabled && !$isExpired ? 'border-zinc-200 dark:border-zinc-800' : 'border-rose-200 dark:border-rose-900/40' }} rounded-[2rem] p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col relative">

                    <!-- Smart Alerts (In-Card) -->
                    @if($isExpired)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-rose-500 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-md z-10 whitespace-nowrap">🔴 این سرویس منقضی شده است</div>
                    @elseif($isLowDays)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-amber-500 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-md z-10 whitespace-nowrap">⚠️ فقط {{ $daysLeft }} روز باقی مانده</div>
                    @elseif($isLowVolume)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-rose-500 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-md z-10 whitespace-nowrap">⚠️ حجم سرویس رو به اتمام است</div>
                @endif

                <!-- Header -->
                    <div class="flex items-start justify-between mb-5 mt-2">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-black {{ $isWG ? 'text-violet-700 dark:text-violet-400 bg-violet-50 dark:bg-violet-500/10 border-violet-200 dark:border-violet-500/20' : 'text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 border-orange-200 dark:border-orange-500/20' }} px-2 py-0.5 rounded-lg border uppercase tracking-wider">
                                    {{ $acc->service_group }}
                                </span>
                                @if($isWG)
                                    <span class="text-[9px] font-bold text-zinc-500 bg-zinc-100 dark:bg-zinc-800/80 px-2 py-0.5 rounded-md uppercase">WG PEER</span>
                                @endif
                            </div>

                            @php
                                $speedLimit = $acc->mikrotik_speed;
                                if (empty($speedLimit) && $acc->group_id) {
                                    $accGroup = \App\Models\Group::find($acc->group_id);
                                    $speedLimit = $accGroup ? $accGroup->mikrotik_speed : null;
                                }
                            @endphp
                            <span class="text-[10px] font-bold text-zinc-500 flex items-center gap-1">
                                ⚡ {{ $speedLimit ?: 'نامحدود ∞' }}
                            </span>
                        </div>

                        <!-- Status Pill -->
                        <div class="shrink-0">
                            @if($acc->is_enabled && !$isExpired)
                                <span class="flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> فعال
                                </span>
                            @else
                                <span class="flex items-center gap-1.5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 text-[10px] font-bold px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ $isExpired ? 'منقضی' : 'غیرفعال' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Credentials -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl p-3.5 mb-5 border border-zinc-100 dark:border-zinc-800/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-zinc-500 font-medium">Username</span>
                            <span class="text-sm font-black font-mono-digit text-zinc-900 dark:text-white" dir="ltr">{{ $acc->username }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-zinc-200/50 dark:border-zinc-700/50">
                            <span class="text-xs text-zinc-500 font-medium">Password</span>
                            @if($isWG)
                                <span class="text-[10px] font-bold text-zinc-400">احراز با فایل کانفیگ</span>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black font-mono-digit text-zinc-900 dark:text-white tracking-widest" dir="ltr">{{ $acc->password }}</span>
                                    <button wire:click="openChangePasswordModal({{ $acc->id }})" class="p-1.5 rounded-lg text-zinc-400 hover:text-orange-500 bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 shadow-sm transition" title="تغییر رمز عبور">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Traffic Progress -->
                    <div class="mb-5">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300 font-mono-digit" dir="ltr">{{ $usedGb }} GB</span>
                            <span class="text-[10px] font-bold text-zinc-400">از {{ $maxGb > 0 ? $maxGb . ' GB' : 'نامحدود ∞' }}</span>
                        </div>
                        @if($maxGb > 0)
                            <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full {{ $progressColor }} transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between items-center text-[10px] mt-1.5 font-bold">
                                <span class="text-zinc-500">{{ $remGb }} GB باقی‌مانده</span>
                                <span class="text-zinc-400 font-mono-digit">{{ $percent }}%</span>
                            </div>
                        @else
                            <div class="w-full flex items-center justify-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 py-1.5 rounded-lg">
                                ترافیک نامحدود ∞
                            </div>
                        @endif
                    </div>

                    <!-- Expiration & Status Box -->
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <!-- Expiration -->
                        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-3 rounded-2xl border border-zinc-100 dark:border-zinc-800/80 flex flex-col justify-center">
                            <span class="text-[10px] font-bold text-zinc-500 mb-1">انقضا</span>
                            @if($acc->expire_date)
                                <div class="text-[13px] font-black text-zinc-900 dark:text-white font-mono-digit mb-0.5" dir="ltr">{{ jdate($acc->expire_date)->format('Y/m/d') }}</div>
                                @if(!$isExpired)
                                    <span class="text-[10px] font-bold {{ $daysLeft <= 4 ? 'text-amber-500' : 'text-zinc-500' }}">{{ $daysLeft == 0 ? 'امروز' : $daysLeft . ' روز مانده' }}</span>
                                @else
                                    <span class="text-[10px] font-bold text-rose-500">منقضی شده</span>
                                @endif
                            @else
                                <div class="text-[10px] font-bold text-blue-500 mt-1">شروع از اولین لاگین</div>
                            @endif
                        </div>

                        <!-- Status / Online -->
                        @if(!$isWG)
                            @php
                                $multiLogin = $acc->multi_login ?? 1;
                                $onlineCount = $acc->online_count ?? 0;
                                $isOnline = $onlineCount > 0;
                            @endphp
                            <div class="bg-zinc-50 dark:bg-zinc-800/40 p-3 rounded-2xl border border-zinc-100 dark:border-zinc-800/80 flex flex-col justify-center">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold text-zinc-500">وضعیت</span>
                                    <span class="text-[10px] font-black {{ $isOnline ? 'text-emerald-500' : 'text-zinc-400' }}">{{ $isOnline ? 'آنلاین' : 'آفلاین' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-zinc-500">کاربران</span>
                                    <span class="text-[11px] font-mono-digit font-black flex items-center gap-0.5" dir="ltr">
                                        <span class="{{ $onlineCount >= $multiLogin ? 'text-rose-500' : 'text-zinc-900 dark:text-white' }}">{{ $onlineCount }}</span>
                                        <span class="text-zinc-400">/</span>
                                        <span class="text-zinc-500">{{ $multiLogin }}</span>
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="bg-violet-50/50 dark:bg-violet-500/5 p-3 rounded-2xl border border-violet-100 dark:border-violet-500/10 flex items-center justify-center text-center">
                                <span class="text-[10px] font-bold text-violet-600 dark:text-violet-400">پروتکل اختصاصی<br>WireGuard</span>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="mt-auto space-y-2">
                        @if($isWG)
                            @php $wgConfig = \App\Models\WireGuardUsers::where('user_id', $acc->id)->first(); @endphp
                            @if($wgConfig)
                                <div class="grid grid-cols-2 gap-2 mb-1">
                                    <a href="{{ asset('configs/' . $wgConfig->profile_name . '.conf') }}" download class="py-2.5 bg-violet-100 dark:bg-violet-500/10 hover:bg-violet-200 dark:hover:bg-violet-500/20 text-violet-700 dark:text-violet-400 font-bold text-[11px] rounded-xl transition flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        دانلود کانفیگ
                                    </a>
                                    <a href="{{ asset('configs/' . $wgConfig->profile_name . '.png') }}" target="_blank" class="py-2.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-[11px] rounded-xl transition flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        مشاهده QR
                                    </a>
                                </div>
                            @elseif($acc->subscription_url)
                                <a href="{{ $acc->subscription_url }}" target="_blank" class="w-full py-2.5 mb-1 bg-violet-100 dark:bg-violet-500/10 hover:bg-violet-200 dark:hover:bg-violet-500/20 text-violet-700 dark:text-violet-400 font-bold text-[11px] rounded-xl transition flex items-center justify-center gap-1.5">
                                    دریافت لینک اتصال
                                </a>
                            @endif
                        @else
                            @if($acc->subscription_url)
                                <div x-data="{ copied: false }" class="mb-1">
                                    <button @click="navigator.clipboard.writeText('{{ $acc->subscription_url }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-2.5 bg-zinc-100 dark:bg-zinc-800/80 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-[11px] rounded-xl transition flex items-center justify-center gap-1.5">
                                        <svg x-show="!copied" class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-text="copied ? '✓ لینک اتصال کپی شد' : 'کپی لینک اتصال'"></span>
                                    </button>
                                </div>
                            @endif
                        @endif

                        <button wire:click="openTutorialModal({{ $acc->id }})" class="w-full py-2.5 bg-zinc-50 dark:bg-zinc-800/40 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 font-bold text-[11px] rounded-xl transition flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            اطلاعات و آموزش اتصال
                        </button>

                        <button wire:click="openAccRechargeModal({{ $acc->id }})" class="w-full py-3 {{ $needsRecharge ? 'bg-orange-500 hover:bg-orange-600 text-white shadow-lg shadow-orange-500/20' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-900 shadow-md' }} font-black text-[13px] rounded-xl transition-transform active:scale-95 flex items-center justify-center gap-1.5">
                            @if($needsRecharge)
                                ⚡ شارژ و تمدید سرویس
                            @else
                                شارژ و تمدید سرویس
                            @endif
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

<!-- Financial Transactions Section -->
    <div class="pt-6">
        <h2 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight mb-4">تاریخچه تراکنش‌های مالی</h2>

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-[2rem] shadow-sm overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="bg-zinc-50 dark:bg-[#18181b] border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-[11px] font-black uppercase tracking-wider">
                    <th class="p-5">نوع تراکنش</th>
                    <th class="p-5">توضیحات</th>
                    <th class="p-5">مبلغ</th>
                    <th class="p-5">تاریخ</th>
                    <th class="p-5 text-center">وضعیت</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-sm">
                @forelse($transactions as $trx)
                    @php $isPlus = in_array($trx->type, ['plus', 'plus_amn']); @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black {{ $isPlus ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                                    {{ $isPlus ? '+' : '-' }}
                                </div>
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $isPlus ? 'شارژ / واریز' : 'کسر / برداشت' }}</span>
                            </div>
                        </td>
                        <td class="p-5 text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $trx->description ?? 'بدون توضیح' }}</td>
                        <td class="p-5 text-sm font-mono-digit font-black {{ $isPlus ? 'text-emerald-500' : 'text-rose-500' }}">
                            {{ $isPlus ? '+' : '-' }}{{ number_format($trx->price) }} <span class="text-[10px] font-sans font-bold">تومان</span>
                        </td>
                        <td class="p-5 text-xs text-zinc-500 font-mono-digit">{{ jdate($trx->created_at)->format('Y/m/d H:i') }}</td>
                        <td class="p-5 text-center">
                            @if($trx->approved == 1)
                                <span class="inline-flex px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black border border-emerald-100 dark:border-emerald-500/20">تایید شده</span>
                            @elseif($trx->approved == 0)
                                <span class="inline-flex px-2.5 py-1 rounded-md bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-black border border-amber-100 dark:border-amber-500/20">در انتظار</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-md bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-black border border-rose-100 dark:border-rose-500/20">رد شده</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-zinc-500 text-sm font-medium">تراکنش مالی ثبت نشده است.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Transaction Cards -->
        <div class="md:hidden space-y-3">
            @forelse($transactions as $trx)
                @php $isPlus = in_array($trx->type, ['plus', 'plus_amn']); @endphp
                <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-2xl p-4 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black {{ $isPlus ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                                {{ $isPlus ? '+' : '-' }}
                            </div>
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">{{ $isPlus ? 'شارژ حساب' : 'برداشت از حساب' }}</span>
                        </div>
                        <span class="text-sm font-mono-digit font-black {{ $isPlus ? 'text-emerald-500' : 'text-rose-500' }}">
                            {{ $isPlus ? '+' : '-' }}{{ number_format($trx->price) }} <span class="text-[9px] font-sans">تومان</span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-zinc-500 truncate max-w-[140px]">{{ $trx->description ?? 'بدون توضیح' }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-zinc-400 font-mono-digit">{{ jdate($trx->created_at)->format('Y/m/d') }}</span>
                            @if($trx->approved == 1)
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            @elseif($trx->approved == 0)
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-[#111827] border border-dashed border-zinc-300 dark:border-zinc-800 rounded-2xl p-6 text-center text-zinc-500 text-xs">تراکنشی یافت نشد.</div>
            @endforelse
        </div>
    </div>


    <!-- ================= MODALS ================= -->

    <!-- 1. Tutorial Modal -->
    @if($showTutorialModal && $selectedAccount)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/80 dark:bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-2xl bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <div class="p-5 border-b border-zinc-200 dark:border-zinc-800/80 flex items-center justify-between bg-zinc-50 dark:bg-[#18181b]">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span> راهنمای اتصال
                    </h3>
                    <button wire:click="$set('showTutorialModal', false)" class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:text-rose-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto space-y-6">
                    <!-- Config Address Box -->
                    <div class="bg-zinc-50 dark:bg-[#18181b] p-4 sm:p-5 rounded-2xl border border-zinc-100 dark:border-zinc-800/80">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                            <span class="text-[11px] font-bold text-zinc-500">آدرس سرور / لینک اتصال شما برای نام کاربری: <span class="font-mono-digit text-orange-500">{{ $selectedAccount->username }}</span></span>
                            <span class="text-[10px] w-max px-2 py-0.5 rounded bg-orange-500/10 text-orange-600 dark:text-orange-400 font-black uppercase">{{ $selectedAccount->service_group }}</span>
                        </div>
                        @if($serverAddress)
                            <div x-data="{ copied: false }" class="relative flex items-center">
                                <input type="text" readonly value="{{ $serverAddress }}" class="w-full bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3.5 text-xs font-mono-digit text-zinc-900 dark:text-white outline-none pr-[90px]" dir="ltr">
                                <button @click="navigator.clipboard.writeText('{{ $serverAddress }}'); copied = true; setTimeout(() => copied = false, 2000)" class="absolute right-1.5 px-3 py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-900 rounded-lg text-[10px] font-bold transition-colors">
                                    <span x-text="copied ? '✓ کپی شد' : 'کپی آدرس'"></span>
                                </button>
                            </div>
                        @else
                            <p class="text-[11px] text-zinc-500 bg-white dark:bg-[#111827] border border-dashed border-zinc-300 dark:border-zinc-700 p-3 rounded-xl text-center">آدرس اختصاصی سرور برای این سرویس صادر نشده است.</p>
                        @endif
                    </div>
                    <!-- Tutorials List -->
                    @if($accountTutorials->count() > 0)
                        <div class="space-y-4">
                            <h4 class="text-[11px] font-black text-zinc-400 uppercase tracking-wider">مراحل اتصال:</h4>
                            @foreach($accountTutorials as $tutorial)
                                <div class="bg-zinc-50 dark:bg-[#18181b] p-5 rounded-2xl border border-zinc-100 dark:border-zinc-800/80 space-y-3">
                                    <h5 class="font-bold text-sm text-zinc-900 dark:text-white">{{ $tutorial->title }}</h5>
                                    <div class="prose dark:prose-invert text-[13px] text-zinc-600 dark:text-zinc-400 leading-loose">
                                        {!! $tutorial->content !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-zinc-400 text-[11px] font-medium border border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">آموزش متنی خاصی برای این نوع سرویس ثبت نشده است.</div>
                    @endif
                </div>
            </div>
        </div>
    @endif

<!-- 2. Change Password Modal -->
    @if($isChangePasswordModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/80 dark:bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-sm bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden relative">
                <div class="p-5 border-b border-zinc-200 dark:border-zinc-800/80 flex items-center justify-between bg-zinc-50 dark:bg-[#18181b]">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">تغییر رمز عبور</h3>
                    <button wire:click="$set('isChangePasswordModalOpen', false)" class="p-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:text-rose-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 mb-2">رمز عبور جدید</label>
                        <div class="relative group">
                            <input wire:model="newPassword" type="text" class="w-full bg-zinc-50 dark:bg-[#18181b] border border-zinc-200 dark:border-zinc-800 rounded-xl pl-4 pr-[85px] py-3.5 text-sm text-zinc-900 dark:text-white font-mono-digit focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all" dir="ltr" placeholder="رمز جدید...">
                            <button wire:click="generatePassword" type="button" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-2.5 py-2 bg-zinc-200 dark:bg-zinc-700 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600 transition-colors text-[10px] font-bold">
                                پیشنهاد خودکار
                            </button>
                        </div>
                        @error('newPassword') <span class="text-rose-500 text-[10px] font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <button wire:click="changePassword" class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-sm rounded-xl transition-all shadow-[0_8px_20px_-6px_rgba(16,185,129,0.4)] active:scale-95">
                        ذخیره رمز عبور
                    </button>
                </div>
            </div>
        </div>
    @endif

<!-- 3. Wallet Recharge Modal -->
    @if($isRechargeModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/80 dark:bg-black/80 backdrop-blur-sm animate-fade-in overflow-y-auto">
            <div class="w-full max-w-md bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden my-8">
                <div class="p-5 border-b border-zinc-200 dark:border-zinc-800/80 flex items-center justify-between bg-zinc-50 dark:bg-[#18181b]">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">درخواست افزایش موجودی</h3>
                    <button wire:click="$set('isRechargeModalOpen', false)" class="p-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:text-rose-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form wire:submit.prevent="requestRecharge" class="p-6 space-y-5">
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 mb-2">مبلغ واریزی (تومان) <span class="text-rose-500">*</span></label>
                        <input wire:model="amount" type="number" placeholder="مثلاً: 50000" class="w-full bg-zinc-50 dark:bg-[#18181b] border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none font-mono-digit transition-all" dir="ltr">
                        @error('amount') <span class="text-rose-500 text-[10px] mt-1.5 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 mb-2">تصویر فیش واریزی <span class="text-rose-500">*</span></label>
                        <input wire:model="receipt" type="file" accept="image/*" class="w-full bg-zinc-50 dark:bg-[#18181b] border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500/50 outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-zinc-200 dark:file:bg-zinc-700 file:text-zinc-900 dark:file:text-white hover:file:bg-zinc-300 dark:hover:file:bg-zinc-600 cursor-pointer transition-all">
                        <div wire:loading wire:target="receipt" class="text-[10px] text-emerald-500 mt-2 font-bold flex items-center gap-1.5">
                            <span class="w-3 h-3 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></span> در حال آپلود...
                        </div>
                        @error('receipt') <span class="text-rose-500 text-[10px] mt-1.5 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 mb-2">توضیحات (اختیاری)</label>
                        <input wire:model="description" type="text" placeholder="شماره پیگیری..." class="w-full bg-zinc-50 dark:bg-[#18181b] border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all">
                    </div>
                    <button type="submit" class="w-full mt-2 py-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-sm transition-all shadow-[0_8px_20px_-6px_rgba(16,185,129,0.4)] active:scale-95 flex items-center justify-center gap-2">
                        <svg wire:loading.remove wire:target="requestRecharge" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <svg wire:loading wire:target="requestRecharge" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>ارسال درخواست شارژ</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

<!-- 4. Account Recharge (Renew) Modal -->
    @if($isAccRechargeModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-900/80 dark:bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="relative w-full max-w-md bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-5 border-b border-zinc-200 dark:border-zinc-800/80 bg-zinc-50 dark:bg-[#18181b]">
                    <h2 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span> شارژ و تمدید سرویس
                    </h2>
                    <button type="button" wire:click="$set('isAccRechargeModalOpen', false)" class="p-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:text-rose-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="mb-5 p-3.5 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 text-blue-700 dark:text-blue-400 text-[11px] font-bold leading-relaxed text-justify flex gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        هزینه انتخاب بسته جدید مستقیماً از موجودی کیف پول شما کسر شده و سرویس بلافاصله تمدید می‌گردد.
                    </div>
                    <form wire:submit.prevent="confirmAccRecharge" class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 mb-2">بسته مورد نظر را انتخاب کنید</label>
                            <select wire:model="selectedGroupId" class="w-full bg-zinc-50 dark:bg-[#18181b] border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-sm rounded-xl p-3.5 outline-none focus:ring-2 focus:ring-orange-500/50 transition-all cursor-pointer font-medium">
                                @if(isset($availableGroups) && count($availableGroups) > 0)
                                    @foreach($availableGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }} ({{ number_format(auth()->user()->getGroupPrice($group)) }} تومان)</option>
                                    @endforeach
                                @else
                                    <option value="">هیچ بسته‌ای یافت نشد</option>
                                @endif
                            </select>
                            @error('wallet')
                            <div class="mt-3 p-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 rounded-xl flex items-start gap-2">
                                <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-rose-700 dark:text-rose-400 text-[11px] font-bold leading-relaxed">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>
                        <div class="pt-2 flex items-center gap-3">
                            <button type="button" wire:click="$set('isAccRechargeModalOpen', false)" class="w-1/3 py-4 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold text-sm rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                                انصراف
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="w-2/3 py-4 bg-orange-500 hover:bg-orange-600 text-white font-black text-sm rounded-xl shadow-[0_8px_20px_-6px_rgba(249,115,22,0.4)] flex items-center justify-center gap-2 transition-all active:scale-95">
                                <svg wire:loading wire:target="confirmAccRecharge" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                <span wire:loading.remove wire:target="confirmAccRecharge">پرداخت و تمدید</span>
                                <span wire:loading wire:target="confirmAccRecharge">درحال پردازش...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
