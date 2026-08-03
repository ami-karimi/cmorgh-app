<div class="space-y-8 animate-fade-in">


    @if($announcements->count() > 0)
        <div class="space-y-3">
            @foreach($announcements as $ann)
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-2xl p-4 flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-400">{{ $ann->title }}</h4>
                        <p class="text-xs text-blue-300 mt-1">{{ $ann->content }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif


        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div>
                <h1 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">خوش آمدید، {{ auth()->user()->name }} 👋</h1>

                <div class="mt-3 flex items-center gap-2 text-xs font-bold bg-zinc-50 dark:bg-zinc-900/50 w-max px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-500">وضعیت اتصال:</span>
                    @if($hasOutage)
                        <span class="text-rose-500 animate-pulse">قطعی در شبکه</span>
                    @elseif($hasDegraded)
                        <span class="text-amber-500 animate-pulse">اختلال جزئی</span>
                    @else
                        <span class="text-emerald-500">پایدار و نرمال</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-emerald-500/10 to-teal-500/10 border border-emerald-500/20 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 block">موجودی اعتبار شما</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono-digit">
                    {{ number_format($balance) }} <span class="text-xs font-sans">تومان</span>
                </span>
                    </div>
                    <button wire:click="$set('isRechargeModalOpen', true)" class="h-[74px] px-5 rounded-2xl bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black font-bold text-sm transition-all shadow-lg flex flex-col items-center justify-center gap-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span class="text-[10px]">شارژ حساب</span>
                    </button>
                </div>

            </div>
        </div>




    @if (session()->has('success_recharge'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success_recharge') }}
        </div>
    @endif

        <div class="space-y-4 font-sans relative">
            <h2 class="text-sm font-black text-zinc-400 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                سرویس‌های فعال شما
            </h2>

            @if (session()->has('success'))
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-bold flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($accounts->count() === 0)
                <div class="bg-white dark:bg-[#111827] border border-dashed border-zinc-300 dark:border-zinc-800 rounded-3xl p-10 text-center">
                    <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-900 text-zinc-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 12H4m8-8v16"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">هیچ سرویس فعالی ندارید</h3>
                    <p class="text-xs text-zinc-500 mt-1">به محض تایید سفارش یا خرید، مشخصات سرویس شما در اینجا نمایش داده می‌شود.</p>
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
                                if ($expireCarbon->isPast()) {
                                    $isExpired = true;
                                }
                            }

                            $isLowVolume = $maxGb > 0 && ($percent >= 85 || ($remGb !== null && $remGb <= 1 && $maxGb > 1));
                            $isLowDays = $daysLeft !== null && $daysLeft <= 4 && !$isExpired;
                            $needsRecharge = $isLowVolume || $isLowDays || $isExpired || !$acc->is_enabled;

                            $progressColor = $percent >= 90 ? 'bg-rose-500 shadow-rose-500/50' : ($percent >= 75 ? 'bg-amber-500 shadow-amber-500/50' : 'bg-emerald-500 shadow-emerald-500/50');
                        @endphp

                        <div class="bg-white dark:bg-[#111827] border {{ $acc->is_enabled && !$isExpired ? 'border-zinc-200 dark:border-zinc-800' : 'border-rose-300 dark:border-rose-900/40 bg-rose-50/20 dark:bg-rose-950/10' }} rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-all relative overflow-hidden flex flex-col justify-between">

                            <div>
                                <div class="flex items-start justify-between mb-4 gap-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] font-black {{ $acc->service_group === 'wireguard' ? 'text-purple-400 bg-purple-500/10 border-purple-500/20' : 'text-orange-500 bg-orange-500/10 border-orange-500/20' }} px-3 py-1 rounded-xl uppercase tracking-wider border">
                                    {{ $acc->service_group }}
                                </span>

                                        @if($acc->service_group === 'wireguard')
                                            <span class="text-[10px] font-bold text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-lg font-mono">
                                        WG Peer
                                    </span>
                                        @endif

                                        @php
                                            $speedLimit = $acc->mikrotik_speed;
                                            if (empty($speedLimit) && $acc->group_id) {
                                                $accGroup = \App\Models\Group::find($acc->group_id);
                                                $speedLimit = $accGroup ? $accGroup->mikrotik_speed : null;
                                            }
                                        @endphp

                                        <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-2 py-0.5 rounded-lg border border-blue-200 dark:border-blue-500/20 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    <span>سرعت:</span>
                                    <span dir="ltr" class="font-mono">{{ $speedLimit ?: 'نامحدود ∞' }}</span>
                                </span>
                                    </div>

                                    <div class="flex items-center gap-1.5 bg-zinc-100 dark:bg-zinc-900 px-2.5 py-1 rounded-full border border-zinc-200 dark:border-zinc-800 shrink-0">
                                        @if($acc->is_enabled && !$isExpired)
                                            <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">فعال</span>
                                        @else
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                            <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400">{{ $isExpired ? 'منقضی شده' : 'غیرفعال' }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-5">
                                    <div class="bg-zinc-50 dark:bg-zinc-900/40 p-3 rounded-2xl border border-zinc-100 dark:border-zinc-800/80">
                                        <span class="text-[10px] text-zinc-400 block mb-1 font-medium">نام کاربری</span>
                                        <h3 class="text-sm font-black font-mono text-zinc-900 dark:text-white truncate" dir="ltr">@ {{ $acc->username }}</h3>
                                    </div>

                                    <div class="bg-zinc-50 dark:bg-zinc-900/40 p-3 rounded-2xl border border-zinc-100 dark:border-zinc-800/80 relative group">
                                        <span class="text-[10px] text-zinc-400 block mb-1 font-medium">کلمه عبور</span>
                                        <div class="flex items-center justify-between gap-1">

                                            @if($acc->service_group === 'wireguard')
                                                <h3 class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 truncate mt-1">احراز با فایل کانفیگ</h3>
                                            @else
                                                <h3 class="text-sm font-black font-mono text-zinc-900 dark:text-white truncate" dir="ltr">{{ $acc->password }}</h3>
                                                <button wire:click="openChangePasswordModal({{ $acc->id }})" class="shrink-0 p-1.5 bg-white dark:bg-[#111827] rounded-lg text-zinc-400 hover:text-orange-500 border border-zinc-200 dark:border-zinc-700 shadow-sm transition" title="تغییر رمز عبور">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-2 mb-5 bg-zinc-50 dark:bg-zinc-900/40 p-3.5 rounded-2xl border border-zinc-100 dark:border-zinc-800/80">
                                    <div class="flex justify-between items-baseline text-xs font-bold">
                                        <span class="text-zinc-500 dark:text-zinc-400">مصرف شده: <strong class="text-zinc-800 dark:text-white font-mono" dir="ltr">{{ $usedGb }} GB</strong></span>
                                        <span class="text-zinc-900 dark:text-zinc-200 font-mono" dir="ltr">
                                    {{ $maxGb > 0 ? $maxGb . ' GB کل' : 'ترافیک نامحدود ∞' }}
                                </span>
                                    </div>

                                    @if($maxGb > 0)
                                        <div class="w-full h-2 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner">
                                            <div class="h-full {{ $progressColor }} rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <div class="flex justify-between items-center text-[10px] font-bold text-zinc-400">
                                            <span>باقیمانده: <strong class="{{ $percent >= 85 ? 'text-rose-500' : 'text-emerald-500' }} font-mono" dir="ltr">{{ $remGb }} GB</strong></span>
                                            <span class="font-mono">{{ $percent }}%</span>
                                        </div>
                                    @else
                                        <div class="text-[11px] font-bold text-emerald-500 bg-emerald-500/10 px-3 py-1.5 rounded-xl text-center border border-emerald-500/20">
                                            میزان حجم: نامحدود
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs text-zinc-500 mb-5 bg-zinc-50 dark:bg-zinc-900/40 p-3.5 rounded-2xl border border-zinc-100 dark:border-zinc-800/80">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @if($acc->expire_date)
                                            <span>انقضا: <strong class="text-zinc-800 dark:text-zinc-200 font-mono" dir="ltr">{{ jdate($acc->expire_date)->format('Y/m/d') }}</strong></span>
                                        @else
                                            <span>انقضا: <strong class="text-amber-500 font-bold">بعد از اولین لاگین</strong></span>
                                        @endif
                                    </div>

                                    @if($acc->expire_date && !$isExpired)
                                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg {{ $daysLeft <= 4 ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-zinc-200/60 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400' }}">
                                    {{ $daysLeft == 0 ? 'امروز' : $daysLeft . ' روز مانده' }}
                                </span>
                                    @elseif(!$acc->expire_date)
                                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-500 border border-blue-500/20">
                                    شروع از اولین اتصال
                                </span>
                                    @endif
                                </div>

                                @if($isLowDays)
                                    <div class="mb-4 p-2.5 bg-amber-500/10 border border-amber-500/20 rounded-xl text-[11px] font-bold text-amber-500 flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span>کمتر از ۴ روز تا پایان انقضای اکانت باقی مانده است!</span>
                                    </div>
                                @elseif($isLowVolume)
                                    <div class="mb-4 p-2.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-[11px] font-bold text-rose-500 flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span>حجم ترافیک سرویس شما رو به اتمام است!</span>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">

                                @if($acc->service_group === 'wireguard')
                                    @php
                                        $wgConfig = \App\Models\WireguardUsers::where('user_id', $acc->id)->first();
                                    @endphp

                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                        @if($wgConfig)
                                            <a href="{{ asset('configs/' . $wgConfig->profile_name . '.conf') }}" download
                                               class="py-2.5 px-3 bg-purple-600/10 hover:bg-purple-600 text-purple-600 dark:text-purple-400 hover:text-white border border-purple-500/20 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                دانلود کانفیگ
                                            </a>

                                            <a href="{{ asset('configs/' . $wgConfig->profile_name . '.png') }}" target="_blank"
                                               class="py-2.5 px-3 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 border border-zinc-200 dark:border-zinc-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                                بارکد QR
                                            </a>
                                        @elseif($acc->subscription_url)
                                            <a href="{{ $acc->subscription_url }}" target="_blank" class="col-span-2 py-2.5 px-3 bg-purple-600/10 hover:bg-purple-600 text-purple-600 dark:text-purple-400 hover:text-white border border-purple-500/20 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                                                دریافت لینک اتصال
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    @php
                                        $multiLogin = $acc->multi_login ?? 1;
                                        $onlineCount = $acc->online_count ?? 0;
                                        $isOnline = $onlineCount > 0;
                                    @endphp

                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                        <div class="bg-zinc-50 dark:bg-zinc-900/40 p-2.5 rounded-xl border border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between">
                                            <span class="text-[10px] text-zinc-500 font-bold">وضعیت اتصال:</span>
                                            @if($isOnline)
                                                <span class="flex items-center gap-1.5 text-[10px] font-black text-emerald-500">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        آنلاین
                    </span>
                                            @else
                                                <span class="flex items-center gap-1.5 text-[10px] font-black text-zinc-400">
                        <span class="h-2 w-2 rounded-full bg-zinc-300 dark:bg-zinc-700"></span>
                        آفلاین
                    </span>
                                            @endif
                                        </div>

                                        <div class="bg-zinc-50 dark:bg-zinc-900/40 p-2.5 rounded-xl border border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between">
                                            <span class="text-[10px] text-zinc-500 font-bold">کاربر همزمان:</span>
                                            <span class="text-[11px] font-mono font-black text-zinc-800 dark:text-zinc-200 flex items-center gap-1" dir="ltr">
                    <span class="{{ $onlineCount >= $multiLogin ? 'text-rose-500' : 'text-emerald-500' }}">
                        {{ $onlineCount }}
                    </span>
                    <span class="text-zinc-400">/</span>
                    {{ $multiLogin }}
                </span>
                                        </div>
                                    </div>
                                @endif

                                @if($acc->subscription_url && $acc->service_group !== 'wireguard')
                                    <button onclick="navigator.clipboard.writeText('{{ $acc->subscription_url }}'); alert('لینک اتصال با موفقیت کپی شد!');"
                                            class="w-full py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800/80 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 border border-zinc-200 dark:border-zinc-700">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        کپی لینک اتصال (Subscription)
                                    </button>
                                @endif

                               <button wire:click="openTutorialModal({{ $acc->id }})"
                                            class="w-full py-2.5 px-4 bg-orange-500/10 hover:bg-orange-500 text-orange-500 hover:text-white border border-orange-500/20 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        <span>اطلاعات و آموزش اتصال</span>
                               </button>

                                <button wire:click="openAccRechargeModal({{ $acc->id }})"
                                        class="w-full py-3 {{ $needsRecharge ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-500/20 hover:from-orange-600 hover:to-amber-600' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black' }} text-xs font-black rounded-xl transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    <span>شارژ و تمدید اکانت</span>
                                    @if($isLowDays)
                                        <span class="text-[9px] bg-black/20 dark:bg-white/20 px-2 py-0.5 rounded-md font-normal">({{ $daysLeft }} روز مانده)</span>
                                    @endif
                                </button>
                            </div>


                        </div>
                    @endforeach
                </div>
            @endif

            @if($showTutorialModal && $selectedAccount)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
                    <div class="w-full max-w-2xl bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

                        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-900/50">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                                <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                                    راهنمای اتصال اکانت: <span class="font-mono text-orange-500">{{ $selectedAccount->username }}</span>
                                </h3>
                            </div>
                            <button wire:click="$set('showTutorialModal', false)" class="text-zinc-500 hover:text-rose-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-5 overflow-y-auto space-y-6">

                            <div class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">🌐 آدرس سرور / لینک اتصال شما:</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-orange-500/10 text-orange-500 font-bold uppercase">
                            {{ $selectedAccount->service_group }}
                        </span>
                                </div>

                                @if($serverAddress)
                                    <div x-data="{ copied: false }" class="relative flex items-center">
                                        <input type="text" readonly value="{{ $serverAddress }}"
                                               class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2.5 text-xs font-mono text-orange-500 outline-none pr-20">

                                        <button @click="navigator.clipboard.writeText('{{ $serverAddress }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                class="absolute left-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-[10px] font-bold transition">
                                            <span x-text="copied ? 'کپی شد!' : 'کپی آدرس'"></span>
                                        </button>
                                    </div>
                                @else
                                    <p class="text-xs text-zinc-400">آدرس اختصاصی سرور برای این سرویس صادر نشده است.</p>
                                @endif
                            </div>

                            @if($accountTutorials->count() > 0)
                                <div class="space-y-4">
                                    <h4 class="text-xs font-bold text-zinc-500 uppercase tracking-wider">آموزش گام‌به‌گام اتصال:</h4>

                                    @foreach($accountTutorials as $tutorial)
                                        <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 space-y-3">
                                            <h5 class="font-bold text-sm text-zinc-900 dark:text-white">{{ $tutorial->title }}</h5>
                                            <div class="prose dark:prose-invert text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                                                {!! $tutorial->content !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6 text-zinc-400 text-xs">
                                    آموزش متنی خاصی برای این نوع سرویس ثبت نشده است.
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endif

            @if($isChangePasswordModalOpen)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
                    <div class="w-full max-w-sm bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden">
                        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-900/50">
                            <h3 class="text-base font-black text-zinc-900 dark:text-white">تغییر رمز عبور</h3>
                            <button wire:click="$set('isChangePasswordModalOpen', false)" class="text-zinc-500 hover:text-rose-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-2">رمز عبور جدید</label>
                                <div class="relative">
                                    <input wire:model="newPassword" type="text" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm text-zinc-900 dark:text-white font-mono focus:ring-2 focus:ring-orange-500 outline-none" dir="ltr" placeholder="رمز عبور...">
                                    <button wire:click="generatePassword" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 bg-zinc-200 dark:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition text-[10px] font-bold">
                                        پیشنهاد خودکار
                                    </button>
                                </div>
                                @error('newPassword') <span class="text-rose-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <button wire:click="changePassword" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-sm rounded-xl transition shadow-lg shadow-emerald-500/20">
                                ذخیره رمز عبور
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    <div class="space-y-4">
        <h2 class="text-sm font-black text-zinc-400 uppercase tracking-wider">تاریخچه تراکنش‌های مالی (اعتبار)</h2>

        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-[10px] font-black uppercase tracking-wider">
                        <th class="p-4">نوع تراکنش</th>
                        <th class="p-4">توضیحات</th>
                        <th class="p-4">مبلغ</th>
                        <th class="p-4">تاریخ</th>
                        <th class="p-4 text-center">وضعیت</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-sm">
                    @forelse($transactions as $trx)
                        @php
                            $isPlus = in_array($trx->type, ['plus', 'plus_amn']);
                        @endphp
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold {{ $isPlus ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                                        {{ $isPlus ? '+' : '-' }}
                                    </div>
                                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                                            {{ $isPlus ? 'شارژ / واریز' : 'کسر / برداشت' }}
                                        </span>
                                </div>
                            </td>
                            <td class="p-4 text-xs text-zinc-600 dark:text-zinc-400">
                                {{ $trx->description ?? 'بدون توضیح' }}
                            </td>
                            <td class="p-4 font-mono-digit font-bold {{ $isPlus ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ $isPlus ? '+' : '-' }}{{ number_format($trx->price) }} تومان
                            </td>
                            <td class="p-4 text-xs text-zinc-500 font-mono-digit">
                                {{ jdate($trx->created_at)->format('Y/m/d H:i') }}
                            </td>
                            <td class="p-4 text-center">
                                @if($trx->approved == 1)
                                    <span class="inline-flex px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-500 text-[10px] font-bold">
                                            تایید شده
                                        </span>
                                @elseif($trx->approved == 0)
                                    <span class="inline-flex px-2.5 py-1 rounded-md bg-amber-500/10 text-amber-500 text-[10px] font-bold animate-pulse">
                                            در انتظار بررسی
                                        </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-md bg-rose-500/10 text-rose-500 text-[10px] font-bold">
                                            رد شده
                                        </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-zinc-500 text-xs">تراکنش مالی ثبت نشده است.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    @if($isRechargeModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in overflow-y-auto">
            <div class="w-full max-w-md bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden my-8">

                <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-900/50">
                    <h3 class="text-base font-black text-zinc-900 dark:text-white">درخواست افزایش موجودی</h3>
                    <button wire:click="$set('isRechargeModalOpen', false)" class="text-zinc-500 hover:text-rose-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="requestRecharge" class="p-6 space-y-5">

                    <div>
                        <label class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 mb-1.5">مبلغ واریزی (تومان) <span class="text-rose-500">*</span></label>
                        <input wire:model="amount" type="number" placeholder="مثلاً: 50000" class="w-full bg-zinc-50 dark:bg-[#09090b] border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none font-mono-digit" dir="ltr">
                        @error('amount') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 mb-1.5">تصویر فیش واریزی <span class="text-rose-500">*</span></label>
                        <input wire:model="receipt" type="file" accept="image/*" class="w-full bg-zinc-50 dark:bg-[#09090b] border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-xl px-4 py-2 text-xs focus:ring-2 focus:ring-emerald-500/50 outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-zinc-200 dark:file:bg-zinc-800 file:text-zinc-900 dark:file:text-white hover:file:bg-zinc-300 cursor-pointer">
                        <div wire:loading wire:target="receipt" class="text-[10px] text-amber-500 mt-2 font-bold animate-pulse">در حال آپلود فیش...</div>
                        @error('receipt') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 mb-1.5">توضیحات (اختیاری)</label>
                        <input wire:model="description" type="text" placeholder="شماره پیگیری یا توضیحات..." class="w-full bg-zinc-50 dark:bg-[#09090b] border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-sm transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2">
                        <svg wire:loading.remove wire:target="requestRecharge" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <svg wire:loading wire:target="requestRecharge" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>ارسال درخواست شارژ</span>
                    </button>

                </form>
            </div>
        </div>
    @endif



        @if($isAccRechargeModalOpen)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-900/80 dark:bg-zinc-950/80 backdrop-blur-sm transition-all animate-fade-in" wire:key="user-acc-recharge-modal">
                <div class="relative w-full max-w-md bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden">

                    <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-100 dark:border-zinc-800/80 bg-zinc-50 dark:bg-[#111827]">
                        <h2 class="text-sm font-bold text-zinc-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            شارژ و تمدید سرویس
                        </h2>
                        <button type="button" wire:click="$set('isAccRechargeModalOpen', false)" class="p-1.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="mb-5 p-3.5 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 text-blue-600 dark:text-blue-400 text-[11px] font-bold leading-relaxed text-justify">
                            کاربر گرامی، با انتخاب بسته جدید، هزینه آن مستقیماً از موجودی کیف پول شما کسر شده و سرویس شما تمدید می‌گردد.
                        </div>

                        <form wire:submit.prevent="confirmAccRecharge" class="space-y-5">
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-600 dark:text-zinc-400 mb-2">انتخاب پلن (بسته تمدید)</label>
                                <select wire:model="selectedGroupId" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-white text-sm rounded-xl p-3.5 outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all cursor-pointer">
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
                                    <svg class="w-4 h-4 text-rose-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-rose-600 dark:text-rose-400 text-[11px] font-bold leading-relaxed">{{ $message }}</span>
                                </div>
                                @enderror
                            </div>

                            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-100 dark:border-zinc-800/80">
                                <button type="button" wire:click="$set('isAccRechargeModalOpen', false)" class="px-5 py-2.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-bold text-xs rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                                    انصراف
                                </button>
                                <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/25 flex items-center gap-2 transition-all">
                                    <svg wire:loading wire:target="confirmAccRecharge" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                    <span wire:loading.remove wire:target="confirmAccRecharge">پرداخت و تمدید سرویس</span>
                                    <span wire:loading wire:target="confirmAccRecharge">در حال پردازش...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

</div>
