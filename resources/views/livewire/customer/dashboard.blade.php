<div class="space-y-8 animate-fade-in pb-12 font-sans" x-data="{ toast: null, toastTimeout: null }" @show-toast.window="toast = { message: '{{ $toastMessage }}', type: '{{ $toastType }}' }; clearTimeout(toastTimeout); toastTimeout = setTimeout(() => toast = null, 5000);">

    {{-- ==================== TOAST ==================== --}}
    <template x-if="toast">
        <div x-show="toast" x-transition:enter.duration.300ms.opacity.scale x-transition:leave.duration.300ms.opacity.scale class="fixed top-6 left-1/2 -translate-x-1/2 z-[200] w-[calc(100%-2rem)] max-w-md">
            <div class="px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3 border" :class="{
                'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400': toast.type === 'success',
                'bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400': toast.type === 'error',
                'bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/20 text-amber-700 dark:text-amber-400': toast.type === 'warning',
                'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/20 text-blue-700 dark:text-blue-400': toast.type === 'info',
            }">
                <span x-text="toast.message" class="text-sm font-bold flex-1"></span>
                <button @click="toast = null" class="p-1 rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </template>

    {{-- ==================== ANNOUNCEMENTS ==================== --}}
    @if($announcements->count() > 0)
        <div class="space-y-3">
            @foreach($announcements as $ann)
                <div x-data="{ dismissed: false }" x-show="!dismissed" class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20 rounded-2xl p-4 flex items-start gap-4 relative">
                    <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-black text-blue-900 dark:text-blue-100">{{ $ann->title }}</h4>
                        <p class="text-xs text-blue-800/80 dark:text-blue-200/80 mt-1 leading-relaxed">{{ $ann->content }}</p>
                    </div>
                    <button @click="dismissed = true" class="p-1.5 rounded-lg text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors shrink-0" title="بستن">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ==================== WELCOME + WALLET ==================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        {{-- Welcome --}}
        <div class="lg:col-span-2 bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-[2rem] p-6 sm:p-8 flex flex-col justify-center shadow-sm relative overflow-hidden">
            <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                سلام، {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-medium">
                @if($accountsData->count() === 0)
                    هنوز سرویسی فعال ندارید. اولین سرویس خود را تهیه کنید.
                @elseif($accountsData->where('is_expired', true)->count() > 0)
                    برخی از سرویس‌های شما نیاز به تمدید دارند.
                @elseif($accountsData->filter(function($item){ return !$item['is_expired'] && $item['days_left'] !== null && $item['days_left'] <= 4; })->count() > 0)
                    یکی از سرویس‌های شما به زودی منقضی می‌شود.
                @else
                    سرویس شما فعال است و آماده استفاده می‌باشد.
                @endif
            </p>
        </div>

        {{-- Wallet --}}
        <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 dark:from-[#18181b] dark:to-[#09090b] border border-zinc-800 rounded-[2rem] p-6 sm:p-8 flex flex-col justify-between shadow-xl shadow-zinc-900/10">
            <div>
                <span class="text-xs font-bold text-zinc-400 block mb-1">موجودی کیف پول</span>
                <div class="text-3xl font-black text-white font-mono-digit">
                    {{ number_format($balance) }} <span class="text-sm font-sans font-bold text-zinc-500">تومان</span>
                </div>
                @php
                    $lastTrx = $transactions->first();
                @endphp
                @if($lastTrx)
                    <div class="mt-2 text-[10px] font-bold text-zinc-400 flex items-center gap-1">
                        آخرین تراکنش:
                        <span class="{{ in_array($lastTrx->type, ['plus', 'plus_amn']) ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ in_array($lastTrx->type, ['plus', 'plus_amn']) ? '+' : '-' }}{{ number_format($lastTrx->price) }} تومان
                        </span>
                    </div>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4">
                <button wire:click="$set('isRechargeModalOpen', true)" class="py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-black text-sm transition-all shadow-[0_8px_20px_-6px_rgba(16,185,129,0.4)] active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>شارژ</span>
                </button>
                <a href="#transactions" class="py-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-sm transition-all active:scale-95 flex items-center justify-center gap-2 text-center">
                    <span>تراکنش‌ها</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ==================== QUICK STATS ==================== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Active Services --}}
        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-2xl p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <div class="text-xl font-black text-zinc-900 dark:text-white">{{ $quickStats['active'] }}</div>
                <div class="text-[10px] font-bold text-zinc-500">سرویس فعال</div>
            </div>
        </div>

        {{-- Needs Renew --}}
        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-2xl p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ $quickStats['needs_renew'] > 0 ? 'bg-rose-500/10 text-rose-500' : 'bg-emerald-500/10 text-emerald-500' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-xl font-black text-zinc-900 dark:text-white">
                    {{ $quickStats['needs_renew'] > 0 ? $quickStats['needs_renew'] : '✓' }}
                </div>
                <div class="text-[10px] font-bold text-zinc-500">
                    {{ $quickStats['needs_renew'] > 0 ? 'نیازمند تمدید' : 'وضعیت خوب' }}
                </div>
            </div>
        </div>

        {{-- Expiring Soon --}}
        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-2xl p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ $quickStats['expiring_soon'] > 0 ? 'bg-amber-500/10 text-amber-500' : 'bg-emerald-500/10 text-emerald-500' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <div class="text-xl font-black text-zinc-900 dark:text-white">
                    {{ $quickStats['expiring_soon'] > 0 ? $quickStats['expiring_soon'] . ' روز' : '✓' }}
                </div>
                <div class="text-[10px] font-bold text-zinc-500">
                    {{ $quickStats['expiring_soon'] > 0 ? 'نزدیک به انقضا' : 'بدون مشکل' }}
                </div>
            </div>
        </div>

        {{-- Balance --}}
        <button wire:click="$set('isRechargeModalOpen', true)" class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-2xl p-4 shadow-sm flex items-center gap-3 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all text-left">
            <div class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 8v1m0-1v-1m0-1v1m0-1V8"></path></svg>
            </div>
            <div>
                <div class="text-xl font-black text-zinc-900 dark:text-white">{{ number_format($balance) }}</div>
                <div class="text-[10px] font-bold text-zinc-500">موجودی کیف پول</div>
            </div>
        </button>
    </div>

    {{-- ==================== DASHBOARD SUMMARY ==================== --}}
    <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
        <span class="text-sm font-bold text-zinc-900 dark:text-white">وضعیت حساب شما:</span>
        <span class="text-sm font-bold flex items-center gap-1.5
            {{ $summaryStatus === 'critical' ? 'text-rose-500' : ($summaryStatus === 'warning' ? 'text-amber-500' : 'text-emerald-500') }}">
            {{ $summaryStatus === 'critical' ? '🔴' : ($summaryStatus === 'warning' ? '⚠️' : '✓') }}
            {{ $summaryMessage }}
        </span>
        <span class="text-xs text-zinc-400 mr-auto">
            {{ $accountsData->count() }} سرویس
        </span>
    </div>

    {{-- ==================== NETWORK STATUS ==================== --}}
    <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-[2rem] p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0
                {{ $hasOutage ? 'bg-rose-500/10 text-rose-500' : ($hasDegraded ? 'bg-amber-500/10 text-amber-500' : 'bg-emerald-500/10 text-emerald-500') }}">
                @if($hasOutage)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                @elseif($hasDegraded)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                    @if($hasOutage)
                        🔴 اختلال یا قطعی شبکه
                    @elseif($hasDegraded)
                        🟠 اختلال جزئی در شبکه
                    @else
                        🟢 اتصال شبکه پایدار است
                    @endif
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    @if($hasOutage)
                        تیم فنی در حال بررسی مشکل است.
                    @elseif($hasDegraded)
                        ممکن است برخی کاربران کاهش سرعت یا ناپایداری اتصال را تجربه کنند.
                    @else
                        در حال حاضر اختلالی در شبکه گزارش نشده است.
                    @endif
                </p>
                <div class="mt-2 flex items-center gap-3 text-[10px] font-bold text-zinc-400">
                    <span>وضعیت: {{ $hasOutage ? 'قطع' : ($hasDegraded ? 'مختل' : 'پایدار') }}</span>
                    <span class="w-px h-3 bg-zinc-200 dark:bg-zinc-700"></span>
                    <span>آخرین بروزرسانی: هم‌اکنون</span>
                </div>
            </div>
            @if($hasOutage || $hasDegraded)
                <div class="w-2 h-2 rounded-full {{ $hasOutage ? 'bg-rose-500' : 'bg-amber-500' }} animate-pulse shrink-0 mt-1"></div>
            @endif
        </div>
    </div>

    {{-- ==================== SMART ALERTS ==================== --}}
    @if(count($smartAlerts) > 0)
        <div>
            <h2 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight mb-4 flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-full bg-amber-500"></span>
                نیازمند توجه
            </h2>
            <div class="space-y-3">
                @foreach($smartAlerts as $alert)
                    <div class="bg-{{ $alert['color'] }}-50 dark:bg-{{ $alert['color'] }}-500/10 border border-{{ $alert['color'] }}-200/50 dark:border-{{ $alert['color'] }}-500/20 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex items-start sm:items-center gap-3 flex-1">
                            <span class="text-xl">{{ $alert['icon'] }}</span>
                            <div>
                                <h4 class="text-sm font-black text-zinc-900 dark:text-white">{{ $alert['title'] }}</h4>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $alert['description'] }}</p>
                            </div>
                        </div>
                        <button wire:click="openAccRechargeModal({{ $alert['account_id'] }})" class="px-5 py-2.5 rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-bold text-[11px] hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all active:scale-95 shrink-0">
                            تمدید سرویس
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ==================== SERVICES ==================== --}}
    <div>
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 mb-4">
            <div>
                <h2 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">سرویس‌های من</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">مدیریت و مشاهده وضعیت سرویس‌های اینترنت</p>
            </div>
            <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-800 px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 shrink-0">
                {{ $accountsData->count() }} سرویس
            </span>
        </div>

        @if($accountsData->count() === 0)
            {{-- Empty State --}}
            <div class="bg-white dark:bg-[#111827] border border-dashed border-zinc-300 dark:border-zinc-800 rounded-[2rem] p-12 text-center flex flex-col items-center">
                <div class="w-20 h-20 bg-zinc-50 dark:bg-zinc-900/50 text-zinc-400 dark:text-zinc-600 rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M20 12H4m8-8v16"></path></svg>
                </div>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white mb-2">هنوز سرویسی ندارید</h3>
                <p class="text-sm font-medium text-zinc-500 max-w-sm mb-6">با تهیه اولین سرویس، اتصال خود را در چند دقیقه راه‌اندازی کنید.</p>
                <a href="{{ route('store.index') }}" wire:navigate class="px-8 py-3.5 rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-bold text-sm shadow-md transition-transform active:scale-95 hover:bg-zinc-800 dark:hover:bg-zinc-100">
                    مشاهده فروشگاه
                </a>
            </div>
        @else
            {{-- Services Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($accountsData as $accountData)
                    @php
                        $acc = $accountData['account'];
                        $usedGb = $accountData['used_gb'];
                        $maxGb = $accountData['max_gb'];
                        $remGb = $accountData['rem_gb'];
                        $percent = $accountData['percent'];
                        $daysLeft = $accountData['days_left'];
                        $isExpired = $accountData['is_expired'];
                        $isLowVolume = $accountData['is_low_volume'];
                        $isLowDays = $accountData['is_low_days'];
                        $needsRecharge = $accountData['needs_recharge'];
                        $progressColor = $accountData['progress_color'];
                        $isWG = $accountData['is_wg'];
                        $wgConfig = $accountData['wireguard_config'];
                        $speedLimit = $accountData['speed_limit'];
                        $multiLogin = $accountData['multi_login'];
                        $onlineCount = $accountData['online_count'];
                        $isOnline = $accountData['is_online'];
                        $health = $accountData['health'];
                    @endphp

                    {{-- Service Card --}}
                    <div class="bg-white dark:bg-[#111827] border {{ $isExpired || !$acc->is_enabled ? 'border-rose-200 dark:border-rose-900/40' : 'border-zinc-200 dark:border-zinc-800' }} rounded-[2rem] p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col relative">

                        {{-- Top Badge (for issues) --}}
                        @if($isExpired)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-rose-500 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-md z-10 whitespace-nowrap">🔴 منقضی شده</div>
                        @elseif($isLowDays)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-amber-500 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-md z-10 whitespace-nowrap">⚠️ {{ $daysLeft }} روز باقی مانده</div>
                        @elseif($isLowVolume)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-rose-500 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-md z-10 whitespace-nowrap">⚠️ حجم رو به اتمام</div>
                        @endif

                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-4 mt-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[10px] font-black {{ $isWG ? 'text-violet-700 dark:text-violet-400 bg-violet-50 dark:bg-violet-500/10 border-violet-200 dark:border-violet-500/20' : 'text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 border-orange-200 dark:border-orange-500/20' }} px-2.5 py-0.5 rounded-lg border uppercase tracking-wider">
                                    {{ $acc->service_group }}
                                </span>
                                @if($isWG)
                                    <span class="text-[9px] font-bold text-zinc-500 bg-zinc-100 dark:bg-zinc-800/80 px-2 py-0.5 rounded-md uppercase">WG</span>
                                @endif
                                <span class="text-[9px] font-bold text-zinc-400 bg-zinc-100 dark:bg-zinc-800/80 px-2 py-0.5 rounded-md">⚡ {{ $speedLimit ?: '∞' }}</span>
                            </div>
                            <div class="shrink-0">
                                <span class="flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1 rounded-full border
                                    {{ $isExpired || !$acc->is_enabled ? 'bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/20 text-rose-600 dark:text-rose-400' : 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isExpired || !$acc->is_enabled ? 'bg-rose-500' : 'bg-emerald-500 animate-pulse' }}"></span>
                                    {{ $isExpired ? 'منقضی' : ($acc->is_enabled ? 'فعال' : 'غیرفعال') }}
                                </span>
                            </div>
                        </div>


                        {{-- Username --}}
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xs text-zinc-500 font-medium">Username</span>
                            <span class="text-sm font-black font-mono-digit text-zinc-900 dark:text-white" dir="ltr">{{ $acc->username }}</span>
                            <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $acc->username }}'); copied = true; setTimeout(() => copied = false, 1500)" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 transition-colors" title="کپی">
                                <span x-show="!copied">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </span>
                                <span x-show="copied" x-cloak class="text-emerald-500 text-[10px] font-bold">✓</span>
                            </button>
                        </div>

                        {{-- Password (for non-WireGuard) --}}
                        @if(!$isWG && $acc->password)
                            <div class="flex items-center gap-2 mb-3" x-data="{ showPassword: false }">
                                <span class="text-xs text-zinc-500 font-medium">Password</span>
                                <span class="text-sm font-black font-mono-digit text-zinc-900 dark:text-white" dir="ltr">
            <span x-show="!showPassword">••••••••</span>
            <span x-show="showPassword" x-cloak>{{ $acc->password }}</span>
        </span>
                                <button @click="showPassword = !showPassword" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 transition-colors" title="نمایش/مخفی کردن رمز">
                                    <svg x-show="!showPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="showPassword" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                </button>
                                <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $acc->password }}'); copied = true; setTimeout(() => copied = false, 1500)" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 transition-colors" title="کپی">
            <span x-show="!copied">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </span>
                                    <span x-show="copied" x-cloak class="text-emerald-500 text-[10px] font-bold">✓</span>
                                </button>
                            </div>
                        @endif

                        {{-- Main Info: Usage, Remaining, Expiry --}}
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5 text-center border border-zinc-100 dark:border-zinc-800/80">
                                <div class="text-[10px] font-bold text-zinc-500">مصرف</div>
                                <div class="text-xs font-black text-zinc-900 dark:text-white font-mono-digit">{{ $usedGb }} GB</div>
                            </div>
                            <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5 text-center border border-zinc-100 dark:border-zinc-800/80">
                                <div class="text-[10px] font-bold text-zinc-500">باقی‌مانده</div>
                                <div class="text-xs font-black text-zinc-900 dark:text-white font-mono-digit">{{ $maxGb > 0 ? $remGb . ' GB' : '∞' }}</div>
                            </div>
                            <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5 text-center border border-zinc-100 dark:border-zinc-800/80">
                                <div class="text-[10px] font-bold text-zinc-500">انقضا</div>
                                <div class="text-xs font-black text-zinc-900 dark:text-white font-mono-digit">
                                    @if($acc->expire_date)
                                        {{ jdate($acc->expire_date)->format('Y/m/d') }}
                                    @else
                                        <span class="text-[9px] text-blue-500">نامحدود</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        @if($maxGb > 0)
                            <div class="mb-3">
                                <div class="flex justify-between items-end mb-1.5">
                                    <span class="text-[10px] font-bold text-zinc-500 font-mono-digit">{{ $percent }}%</span>
                                    <span class="text-[10px] font-bold text-zinc-400">{{ $remGb }} GB باقی‌مانده</span>
                                </div>
                                <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $progressColor }} transition-all duration-700" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @else
                            <div class="mb-3 flex items-center justify-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-500/20">
                                ترافیک نامحدود ∞
                            </div>
                        @endif

                        {{-- Service Health --}}
                        <div class="flex items-center gap-2 mb-3 text-[10px] font-bold">
                            <span class="text-zinc-400">وضعیت:</span>
                            <span class="flex items-center gap-1 {{ $health['color'] === 'rose' ? 'text-rose-500' : ($health['color'] === 'amber' ? 'text-amber-500' : 'text-emerald-500') }}">
                                {{ $health['icon'] }} {{ $health['label'] }}
                            </span>
                            @if($daysLeft !== null && !$isExpired && $daysLeft > 0)
                                <span class="text-zinc-400 text-[9px]">({{ $daysLeft }} روز)</span>
                            @endif
                        </div>

                        {{-- Collapsible Advanced Info --}}
                        <div x-data="{ open: false }" class="mb-3">
                            <button @click="open = !open" class="text-[10px] font-bold text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors flex items-center gap-1">
                                <span x-text="open ? '▼' : '▶'"></span>
                                جزئیات بیشتر
                            </button>
                            <div x-show="open" x-collapse class="mt-2 space-y-1.5 text-[10px] text-zinc-500 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800/80 pt-2.5">
                                @if(!$isWG)
                                    <div class="flex items-center justify-between">
                                        <span>وضعیت آنلاین:</span>
                                        <span class="font-bold {{ $isOnline ? 'text-emerald-500' : 'text-zinc-400' }}">{{ $isOnline ? 'آنلاین' : 'آفلاین' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>تعداد کاربران:</span>
                                        <span class="font-mono-digit font-bold {{ $onlineCount >= $multiLogin ? 'text-rose-500' : 'text-zinc-900 dark:text-white' }}">{{ $onlineCount }} / {{ $multiLogin }}</span>
                                    </div>
                                @else
                                    <div class="text-center text-violet-500 dark:text-violet-400 font-bold">پروتکل اختصاصی WireGuard</div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span>شناسه سرویس:</span>
                                    <span class="font-mono-digit text-[9px]">{{ $acc->id }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-auto space-y-2">
                            {{-- Primary Action --}}
                            <button wire:click="openAccRechargeModal({{ $acc->id }})" class="w-full py-3 {{ $needsRecharge ? 'bg-orange-500 hover:bg-orange-600 text-white shadow-lg shadow-orange-500/20' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-900 shadow-md' }} font-black text-[13px] rounded-xl transition-transform active:scale-95 flex items-center justify-center gap-1.5">
                                {{ $needsRecharge ? '⚡ شارژ و تمدید' : 'شارژ و تمدید' }}
                            </button>

                            {{-- Secondary Actions --}}
                            <div class="flex flex-wrap gap-1.5">
                                {{-- Tutorial --}}
                                <button wire:click="openTutorialModal({{ $acc->id }})" class="flex-1 min-w-[60px] py-2 bg-zinc-50 dark:bg-zinc-800/40 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 font-bold text-[10px] rounded-xl transition flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>اطلاعات</span>
                                </button>

                                {{-- Change Password --}}
                                <button wire:click="openChangePasswordModal({{ $acc->id }})" class="flex-1 min-w-[60px] py-2 bg-zinc-50 dark:bg-zinc-800/40 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 font-bold text-[10px] rounded-xl transition flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    <span>تغییر رمز</span>
                                </button>

                                {{-- WireGuard Specific --}}
                                @if($isWG && $wgConfig)
                                    <a href="{{ asset('configs/' . $wgConfig->profile_name . '.conf') }}" download class="flex-1 min-w-[60px] py-2 bg-violet-50 dark:bg-violet-500/10 hover:bg-violet-100 dark:hover:bg-violet-500/20 text-violet-700 dark:text-violet-400 font-bold text-[10px] rounded-xl transition flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span>کانفیگ</span>
                                    </a>
                                    <a href="{{ asset('configs/' . $wgConfig->profile_name . '.png') }}" target="_blank" class="flex-1 min-w-[60px] py-2 bg-zinc-50 dark:bg-zinc-800/40 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 font-bold text-[10px] rounded-xl transition flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        <span>QR</span>
                                    </a>
                                @elseif(!$isWG && $acc->subscription_url)
                                    <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $acc->subscription_url }}'); copied = true; setTimeout(() => copied = false, 1500)" class="flex-1 min-w-[60px] py-2 bg-zinc-50 dark:bg-zinc-800/40 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 font-bold text-[10px] rounded-xl transition flex items-center justify-center gap-1">
                                        <span x-show="!copied">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </span>
                                        <span x-show="copied" x-cloak class="text-emerald-500">✓</span>
                                        <span x-text="copied ? 'کپی شد' : 'کپی لینک'"></span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ==================== TRANSACTIONS ==================== --}}
    <div id="transactions" class="pt-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 mb-4">
            <div>
                <h2 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">تاریخچه تراکنش‌های مالی</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">{{ $transactions->count() }} تراکنش اخیر</p>
            </div>
            @if($transactions->count() > 0)
                <span class="text-[10px] font-bold text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-3 py-1 rounded-lg border border-zinc-200 dark:border-zinc-700">۱۰ مورد اخیر</span>
            @endif
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-[2rem] shadow-sm overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="bg-zinc-50 dark:bg-[#18181b] border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-[11px] font-black uppercase tracking-wider">
                    <th class="p-5">نوع تراکنش</th>
                    <th class="p-5">توضیحات</th>
                    <th class="p-5 text-left">مبلغ</th>
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
                        <td class="p-5 text-sm font-mono-digit font-black text-left {{ $isPlus ? 'text-emerald-500' : 'text-rose-500' }}">
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

        {{-- Mobile Cards --}}
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

    {{-- ==================== MODALS ==================== --}}

    {{-- 1. Tutorial Modal --}}
    @if($showTutorialModal && $selectedAccount)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/80 dark:bg-black/80 backdrop-blur-sm animate-fade-in" x-data @keydown.escape.window="$wire.set('showTutorialModal', false)">
            <div class="w-full max-w-2xl bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <div class="p-5 border-b border-zinc-200 dark:border-zinc-800/80 flex items-center justify-between bg-zinc-50 dark:bg-[#18181b] shrink-0">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span> راهنمای اتصال
                    </h3>
                    <button wire:click="$set('showTutorialModal', false)" class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:text-rose-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    {{-- Service Info --}}
                    <div class="bg-zinc-50 dark:bg-[#18181b] p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800/80 space-y-2 mb-5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-zinc-500 font-bold">نام کاربری:</span>
                            <span class="font-mono-digit font-black text-zinc-900 dark:text-white" dir="ltr">{{ $selectedAccount->username }}</span>
                            <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $selectedAccount->username }}'); copied = true; setTimeout(() => copied = false, 1500)" class="p-1 rounded text-zinc-400 hover:text-zinc-600 transition-colors">
                                <span x-show="!copied">📋</span>
                                <span x-show="copied" x-cloak class="text-emerald-500 text-[10px] font-bold">✓</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-zinc-500 font-bold">پروتکل:</span>
                            <span class="font-black text-orange-500">{{ $selectedAccount->service_group }}</span>
                        </div>
                        @if($serverAddress)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-zinc-500 font-bold">آدرس سرور:</span>
                                <span class="font-mono-digit font-black text-zinc-900 dark:text-white text-[10px]" dir="ltr">{{ $serverAddress }}</span>
                                <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $serverAddress }}'); copied = true; setTimeout(() => copied = false, 1500)" class="p-1 rounded text-zinc-400 hover:text-zinc-600 transition-colors">
                                    <span x-show="!copied">📋</span>
                                    <span x-show="copied" x-cloak class="text-emerald-500 text-[10px] font-bold">✓</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Tutorials --}}
                    @if($accountTutorials->count() > 0)
                        <div class="space-y-4">
                            <h4 class="text-[11px] font-black text-zinc-400 uppercase tracking-wider">مراحل اتصال:</h4>
                            @foreach($accountTutorials as $index => $tutorial)
                                <div class="bg-zinc-50 dark:bg-[#18181b] p-5 rounded-2xl border border-zinc-100 dark:border-zinc-800/80">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 flex items-center justify-center text-[10px] font-black">{{ $index + 1 }}</span>
                                        <h5 class="font-bold text-sm text-zinc-900 dark:text-white">{{ $tutorial->title }}</h5>
                                    </div>
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

    {{-- 2. Change Password Modal --}}
    @if($isChangePasswordModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/80 dark:bg-black/80 backdrop-blur-sm animate-fade-in" x-data @keydown.escape.window="$wire.set('isChangePasswordModalOpen', false)">
            <div class="w-full max-w-sm bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden">
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

    {{-- 3. Wallet Recharge Modal --}}
    {{-- 3. Wallet Recharge Modal --}}
    @if($isRechargeModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/80 dark:bg-black/80 backdrop-blur-sm animate-fade-in overflow-y-auto" x-data @keydown.escape.window="$wire.set('isRechargeModalOpen', false)">
            <div class="w-full max-w-md bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden my-8">
                <div class="p-5 border-b border-zinc-200 dark:border-zinc-800/80 flex items-center justify-between bg-zinc-50 dark:bg-[#18181b] shrink-0">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">درخواست افزایش موجودی</h3>
                    <button wire:click="$set('isRechargeModalOpen', false)" class="p-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:text-rose-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form wire:submit.prevent="requestRecharge" class="p-6 space-y-5">

                    {{-- ==================== BANK INFO ==================== --}}
                    <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20 rounded-xl p-4 space-y-2">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-black text-blue-700 dark:text-blue-300">اطلاعات واریز</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-blue-600 dark:text-blue-400 font-bold text-[11px]">شماره کارت:</span>
                            <div class="flex items-center gap-2">
                                <span class="font-mono-digit font-black text-blue-900 dark:text-blue-100 text-sm" dir="ltr">{{ $bankInfo['card_number'] ?? '6037-9918-1234-5678' }}</span>
                                <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $bankInfo['card_number'] ?? '6037991812345678' }}'); copied = true; setTimeout(() => copied = false, 1500)" class="p-1 rounded text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors" title="کپی شماره کارت">
                                <span x-show="!copied">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </span>
                                    <span x-show="copied" x-cloak class="text-emerald-500 text-[10px] font-bold">✓</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm border-t border-blue-200/50 dark:border-blue-500/20 pt-2">
                            <span class="text-blue-600 dark:text-blue-400 font-bold text-[11px]">صاحب حساب:</span>
                            <span class="font-bold text-blue-900 dark:text-blue-100 text-sm">{{ $bankInfo['owner'] ?? 'علی‌رضا محمدی' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-blue-600 dark:text-blue-400 font-bold text-[11px]">بانک:</span>
                            <span class="font-bold text-blue-900 dark:text-blue-100 text-sm">{{ $bankInfo['bank_name'] ?? 'بانک ملی' }}</span>
                        </div>
                        @if(isset($bankInfo['shaba']) && !empty($bankInfo['shaba']))
                            <div class="flex items-center justify-between text-sm border-t border-blue-200/50 dark:border-blue-500/20 pt-2">
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-[11px]">شبا:</span>
                                <span class="font-mono-digit font-bold text-blue-900 dark:text-blue-100 text-[10px]" dir="ltr">{{ $bankInfo['shaba'] }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Amount with quick select --}}
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 mb-2">مبلغ واریزی (تومان) <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-4 gap-2 mb-3">
                            @foreach([100000, 200000, 500000, 1000000] as $quickAmount)
                                <button type="button" wire:click="$set('amount', {{ $quickAmount }})" class="py-2 rounded-lg text-[11px] font-bold transition-all {{ $amount == $quickAmount ? 'bg-emerald-500 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                                    {{ number_format($quickAmount) }}
                                </button>
                            @endforeach
                        </div>
                        <input wire:model="amount" type="number" placeholder="مثلاً: 50000" class="w-full bg-zinc-50 dark:bg-[#18181b] border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none font-mono-digit transition-all" dir="ltr">
                        @error('amount') <span class="text-rose-500 text-[10px] mt-1.5 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Receipt Upload --}}
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 mb-2">تصویر فیش واریزی <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input wire:model="receipt" type="file" accept="image/*" class="w-full bg-zinc-50 dark:bg-[#18181b] border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-emerald-500/50 outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-zinc-200 dark:file:bg-zinc-700 file:text-zinc-900 dark:file:text-white hover:file:bg-zinc-300 dark:hover:file:bg-zinc-600 cursor-pointer transition-all">
                            @if($receipt)
                                <div class="mt-3 relative inline-block">
                                    <img src="{{ $receipt->temporaryUrl() }}" class="max-h-32 rounded-xl border border-zinc-200 dark:border-zinc-700">
                                    <button type="button" wire:click="$set('receipt', null)" class="absolute -top-2 -right-2 p-1 rounded-full bg-rose-500 text-white hover:bg-rose-600 transition-colors text-[10px] font-bold shadow-md">
                                        ✕
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div wire:loading wire:target="receipt" class="text-[10px] text-emerald-500 mt-2 font-bold flex items-center gap-1.5">
                            <span class="w-3 h-3 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></span> در حال آپلود...
                        </div>
                        @error('receipt') <span class="text-rose-500 text-[10px] mt-1.5 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Description --}}
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

    {{-- 4. Account Recharge Modal --}}
    @if($isAccRechargeModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-900/80 dark:bg-black/80 backdrop-blur-sm animate-fade-in" x-data @keydown.escape.window="$wire.set('isAccRechargeModalOpen', false)">
            <div class="relative w-full max-w-md bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-5 border-b border-zinc-200 dark:border-zinc-800/80 bg-zinc-50 dark:bg-[#18181b] shrink-0">
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

                    @php
                        $selectedAccountForRenew = \App\Models\Accounts::find($selectedAccountId);
                        $selectedGroupForRenew = $selectedGroupId ? \App\Models\Group::find($selectedGroupId) : null;
                        $groupPrice = $selectedGroupForRenew ? auth()->user()->getGroupPrice($selectedGroupForRenew) : 0;
                        $balance = auth()->user()->balance;
                        $hasEnoughBalance = $groupPrice > 0 && $balance >= $groupPrice;
                    @endphp

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
                        </div>

                        {{-- Summary --}}
                        @if($selectedGroupForRenew)
                            <div class="bg-zinc-50 dark:bg-[#18181b] rounded-xl p-4 border border-zinc-100 dark:border-zinc-800/80 space-y-2 text-[12px]">
                                <div class="flex items-center justify-between">
                                    <span class="text-zinc-500 font-bold">بسته انتخاب شده:</span>
                                    <span class="font-black text-zinc-900 dark:text-white">{{ $selectedGroupForRenew->name }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-zinc-500 font-bold">هزینه:</span>
                                    <span class="font-black text-orange-500">{{ number_format($groupPrice) }} تومان</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-zinc-500 font-bold">موجودی فعلی:</span>
                                    <span class="font-black text-zinc-900 dark:text-white">{{ number_format($balance) }} تومان</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-zinc-200 dark:border-zinc-700/50 pt-2">
                                    <span class="text-zinc-500 font-bold">موجودی پس از تمدید:</span>
                                    <span class="font-black {{ $hasEnoughBalance ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ number_format($balance - $groupPrice) }} تومان
                                    </span>
                                </div>
                            </div>
                            @error('wallet')
                            <div class="p-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 rounded-xl flex items-start gap-2">
                                <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-rose-700 dark:text-rose-400 text-[11px] font-bold leading-relaxed">{{ $message }}</span>
                            </div>
                            @enderror
                        @endif

                        <div class="pt-2 flex items-center gap-3">
                            <button type="button" wire:click="$set('isAccRechargeModalOpen', false)" class="w-1/3 py-4 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold text-sm rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                                انصراف
                            </button>
                            <button type="submit" wire:loading.attr="disabled" {{ !$hasEnoughBalance ? 'disabled' : '' }} class="w-2/3 py-4 {{ $hasEnoughBalance ? 'bg-orange-500 hover:bg-orange-600 shadow-[0_8px_20px_-6px_rgba(249,115,22,0.4)]' : 'bg-zinc-300 dark:bg-zinc-700 cursor-not-allowed' }} text-white font-black text-sm rounded-xl flex items-center justify-center gap-2 transition-all active:scale-95">
                                <svg wire:loading wire:target="confirmAccRecharge" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                <span wire:loading.remove wire:target="confirmAccRecharge">{{ $hasEnoughBalance ? 'پرداخت و تمدید' : 'موجودی ناکافی' }}</span>
                                <span wire:loading wire:target="confirmAccRecharge">درحال پردازش...</span>
                            </button>
                        </div>
                        @if(!$hasEnoughBalance && $selectedGroupForRenew)
                            <div class="text-center">
                                <button type="button" wire:click="$set('isRechargeModalOpen', true); $set('isAccRechargeModalOpen', false)" class="text-[11px] font-bold text-emerald-500 hover:text-emerald-600 transition-colors">
                                    افزایش موجودی کیف پول
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
