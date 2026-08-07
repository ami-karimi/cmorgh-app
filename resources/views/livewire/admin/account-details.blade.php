<div class="space-y-6 pb-12 font-sans" wire:key="account-detail-view">

    <div class="relative overflow-hidden bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/60 rounded-[2rem] p-6 shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">

            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-zinc-800 to-zinc-900 border border-zinc-700/50 flex items-center justify-center text-orange-400 font-black text-2xl shadow-inner shadow-white/5">
                    {{ substr($account->username, 0, 2) }}
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-2xl font-black text-white tracking-tight" dir="ltr">@ {{ $account->username }}</h1>
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-zinc-800 text-zinc-300 border border-zinc-700 uppercase tracking-widest">{{ $account->service_group }}</span>
                    </div>
                    <div class="flex items-center gap-4 text-xs font-medium text-zinc-400">
                        <span class="flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full {{ $account->is_enabled ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></div> وضعیت: {{ $account->is_enabled ? 'فعال' : 'مسدود' }}</span>
                        <span>شناسه سیستم: <strong class="font-mono text-zinc-300">#{{ $account->id }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button onclick="navigator.clipboard.writeText('{{ $account->subscription_url }}'); alert('لینک اختصاصی کاربر کپی شد!');"
                        class="px-4 py-2.5 bg-zinc-800/80 hover:bg-zinc-700 text-amber-400 border border-amber-500/20 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    کپی لینک اختصاصی کاربر
                </button>


                <button wire:click="toggleStatus" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2 {{ $account->is_enabled ? 'bg-zinc-950 text-rose-400 hover:bg-rose-500/10 border border-rose-500/20' : 'bg-zinc-950 text-emerald-400 hover:bg-emerald-500/10 border border-emerald-500/20' }}">
                    <svg wire:loading wire:target="toggleStatus" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                    <svg wire:loading.remove wire:target="toggleStatus" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    {{ $account->is_enabled ? 'مسدودسازی' : 'فعال‌سازی' }}
                </button>

                <button wire:click="$set('isAdjustmentModalOpen', true)" class="px-5 py-2.5 bg-zinc-800/80 hover:bg-zinc-700 text-white border border-zinc-700/50 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    تغییر حجم/زمان
                </button>

                <button wire:click="openEditModal" class="px-5 py-2.5 bg-zinc-800/80 hover:bg-zinc-700 text-white border border-zinc-700/50 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    ویرایش
                </button>

                <button wire:click="openRechargeModal" class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-orange-500/25 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    شارژ و تمدید
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="px-5 py-4 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl font-bold flex items-center gap-3 shadow-lg">
            <div class="p-1.5 bg-emerald-500/20 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="px-5 py-4 text-sm text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-2xl font-bold flex items-center gap-3 shadow-lg">
            <div class="p-1.5 bg-rose-500/20 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></div>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="xl:col-span-2 space-y-6">

            @if($account->service_group === 'l2tp_cisco' || $account->service_group === 'openvpn')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] p-6 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-zinc-400">تعداد اتصالات موفق</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="text-2xl font-black text-white font-mono">{{ $totalConnections }}</span>
                                    <span class="text-xs text-zinc-500">بار</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-[11px] font-bold text-blue-400 bg-blue-500/10 px-3 py-1.5 rounded-lg inline-block">امروز: {{ $todayConnections }} اتصال</div>
                    </div>

                    <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] p-6 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-zinc-400">کاربر آنلاین فعلی</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="text-2xl font-black text-white font-mono">{{ count($activeSessions) }}</span>
                                    <span class="text-xs text-zinc-500">نشست</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-[11px] font-bold text-zinc-500 bg-zinc-800/50 px-3 py-1.5 rounded-lg inline-block truncate max-w-full" dir="ltr">IP: {{ $lastServerIp ?? '0.0.0.0' }}</div>
                    </div>
                </div>
            @endif

            @if($account->service_group === 'wireguard')
                @php
                    if (!function_exists('formatVolumeBytes')) {
                        function formatVolumeBytes($bytes, $precision = 2) {
                            if ($bytes <= 0) return '0 B';
                            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                            $pow = min($pow, count($units) - 1);
                            $bytes /= pow(1024, $pow);
                            return round($bytes, $precision) . ' ' . $units[$pow];
                        }
                    }

                    $maxUsage = $account->max_usage ?? 0;
                    $usage = $account->usage ?? 0;
                    $remainingBytes = max(0, $maxUsage - $usage);
                    $usagePercent = ($maxUsage > 0) ? min(100, round(($usage / $maxUsage) * 100, 1)) : 0;

                    $volTextColor = $usagePercent >= 90 ? 'text-rose-400' : ($usagePercent >= 75 ? 'text-amber-400' : 'text-emerald-400');
                    $volBgColor   = $usagePercent >= 90 ? 'bg-rose-500 shadow-rose-500/50' : ($usagePercent >= 75 ? 'bg-amber-500 shadow-amber-500/50' : 'bg-emerald-500 shadow-emerald-500/50');
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] p-6 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-zinc-400">پروتکل فعال سرویس</span>
                                <h3 class="text-xl font-black text-white mt-0.5 tracking-wider uppercase">WireGuard</h3>
                            </div>
                        </div>
                        <div class="text-[11px] font-bold text-purple-400 bg-purple-500/10 px-3 py-1.5 rounded-lg inline-block">تعداد {{ $wgConfigs->count() }} دستگاه متصل (Peers)</div>
                    </div>

                    <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] p-6 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-zinc-400 block mb-0.5">ترافیک باقیمانده</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-xl font-black {{ $volTextColor }} font-mono" dir="ltr">{{ formatVolumeBytes(max(0, $maxUsage - $usage)) }}</span>
                                        @if($account->max_usage > 0) <span class="text-[10px] text-zinc-500 font-mono" dir="ltr">/ {{ formatVolumeBytes($maxUsage) }}</span> @endif
                                    </div>
                                </div>
                            </div>
                            <span class="text-lg font-black text-zinc-700 font-mono">{{ $usagePercent }}%</span>
                        </div>
                        @if($account->max_usage > 0)
                            <div class="w-full bg-zinc-950 rounded-full h-2 border border-zinc-800/50 overflow-hidden shadow-inner">
                                <div class="{{ $volBgColor }} h-full rounded-full transition-all duration-1000 shadow-lg" style="width: {{ $usagePercent }}%"></div>
                            </div>
                            <div class="text-left mt-2"><span class="text-[10px] font-bold text-zinc-500">مصرف شده: <span dir="ltr">{{ formatVolumeBytes($usage) }}</span></span></div>
                        @else
                            <div class="text-[11px] font-bold text-blue-400 bg-blue-500/10 px-3 py-2 rounded-xl text-center border border-blue-500/20">این سرویس دارای ترافیک نامحدود است</div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] overflow-hidden shadow-xl">
                @php
                    $isRadiusService = in_array($account->service_group, ['l2tp_cisco', 'l2tp', 'openvpn']);
                @endphp

                <div class="p-2 border-b border-zinc-800/60 bg-zinc-950/30 overflow-x-auto relative">
                    <div class="flex gap-1 min-w-max">
                        @foreach([
                            'active_sessions' => $isRadiusService ? 'نشست‌های فعال (' . count($activeSessions ?? []) . ')' : null,
                            'session_history' => $isRadiusService ? 'تاریخچه نشست‌ها' : null,
                            'login_logs'      => $isRadiusService ? 'لاگ‌های ورود' : null,
                            'activities'      => 'رخدادها و تغییرات',
                            'wg_configs'      => $account->service_group === 'wireguard' ? 'لیست کانفیگ‌ها (Peers)' : null
                        ] as $tabKey => $tabLabel)
                            @if($tabLabel)
                                <button wire:click="$set('activeTab', '{{ $tabKey }}')" wire:loading.attr="disabled" class="cursor-pointer px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $activeTab === $tabKey ? 'bg-zinc-800 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/50' }}">
                                    {{ $tabLabel }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="p-0 relative min-h-[300px]">
                    <div wire:loading wire:target="activeTab, $set('activeTab')" class="absolute inset-0 z-50 flex items-center justify-center bg-zinc-950/50 backdrop-blur-sm transition-all rounded-b-[2rem]">
                        <div class="flex items-center gap-3 px-5 py-3 bg-zinc-900 border border-zinc-700/50 text-white rounded-2xl shadow-2xl">
                            <svg class="w-5 h-5 animate-spin text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            <span class="text-sm font-bold">درحال بارگذاری اطلاعات...</span>
                        </div>
                    </div>

                    @if($activeTab === 'active_sessions')
                        <div class="overflow-x-auto animate-fade-in" wire:key="tab-active-sessions">
                            <table class="w-full text-right text-xs">
                                <thead class="bg-zinc-950/50 text-zinc-400 font-bold border-b border-zinc-800/80">
                                <tr>
                                    <th class="p-4 font-medium">آی‌پی کاربر</th>
                                    <th class="p-4 font-medium">سرور میزبان</th>
                                    <th class="p-4 font-medium">زمان اتصال</th>
                                    <th class="p-4 font-medium">مدت اتصال</th>
                                    <th class="p-4 font-medium text-center">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                                @forelse($activeSessions as $s)
                                    <tr class="hover:bg-zinc-800/30 transition-colors">
                                        <td class="p-4 font-mono text-white" dir="ltr">{{ $s->callingstationid }}</td>
                                        <td class="p-4 font-mono text-zinc-400" dir="ltr">{{ $s->nasipaddress }}</td>
                                        <td class="p-4 font-mono text-zinc-400" dir="ltr">{{ \Morilog\Jalali\Jalalian::forge($s->acctstarttime)->format('Y/m/d H:i') }}</td>
                                        <td class="p-4 font-bold text-emerald-400 font-mono" dir="ltr">{{ gmdate("H:i:s", now()->diffInSeconds($s->acctstarttime)) }}</td>
                                        <td class="p-4 text-center">
                                            <button wire:click="killSession({{ $s->radacctid }})" class="px-3 py-1.5 bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white rounded-lg text-[10px] font-bold transition-colors">قطع اتصال</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-12 text-center text-zinc-500 font-medium">هیچ کاربر آنلاینی در حال حاضر وجود ندارد.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($activeTab === 'login_logs')
                        <div class="overflow-x-auto animate-fade-in flex flex-col" wire:key="tab-login-logs">
                            <table class="w-full text-right text-xs">
                                <thead class="bg-zinc-950/50 text-zinc-400 font-bold border-b border-zinc-800/80">
                                <tr>
                                    <th class="p-4 font-medium text-center w-28">وضعیت ورود</th>
                                    <th class="p-4 font-medium">پیام سرور (دلیل خطا)</th>
                                    <th class="p-4 font-medium">کلمه عبور ارسالی</th>
                                    <th class="p-4 font-medium">سرور (NAS IP)</th>
                                    <th class="p-4 font-medium">زمان تلاش</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                                @forelse($loginLogs ?? [] as $log)
                                    <tr class="hover:bg-zinc-800/30 transition-colors">
                                        <td class="p-4 text-center">
                                            @if($log->reply === 'Access-Accept')
                                                <span class="inline-block px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold">ورود موفق</span>
                                            @else
                                                <span class="inline-block px-2.5 py-1 rounded-md bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-bold">رد شده</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-[11px] font-medium {{ $log->reply === 'Access-Accept' ? 'text-zinc-500' : 'text-rose-300' }}" dir="ltr">
                                            {{ $log->message ?: '---' }}
                                        </td>
                                        <td class="p-4 font-mono text-zinc-500 text-[11px]" dir="ltr">
                                            {{ $log->pass ?: '---' }}
                                        </td>
                                        <td class="p-4 font-mono text-zinc-400 text-[11px]" dir="ltr">
                                            {{ $log->nas_ip ?: 'نامشخص' }}
                                        </td>
                                        <td class="p-4 font-mono text-zinc-400 text-[11px]" dir="ltr">
                                            {{ \Morilog\Jalali\Jalalian::forge($log->created_at)->format('Y/m/d H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-12 text-center text-zinc-500 font-medium">هیچ لاگ ورودی برای این کاربر در ردیوس ثبت نشده است.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            @if($loginLogs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="p-4 bg-zinc-950/40 border-t border-zinc-800/60 mt-auto" wire:key="login-logs-pagination">
                                    {{ $loginLogs->links() }}
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($activeTab === 'activities')
                        <div class="overflow-x-auto animate-fade-in" wire:key="tab-activities">
                            <table class="w-full text-right text-xs">
                                <thead class="bg-zinc-950/50 text-zinc-400 font-bold border-b border-zinc-800/80">
                                <tr>
                                    <th class="p-4 font-medium">شرح رخداد</th>
                                    <th class="p-4 font-medium w-40">مجری</th>
                                    <th class="p-4 font-medium w-40">زمان</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                                @forelse($activities as $act)
                                    <tr class="hover:bg-zinc-800/30 transition-colors">
                                        <td class="p-4 text-[13px] font-medium text-white leading-relaxed">{{ $act->content }}</td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-zinc-800/50 text-zinc-300 border border-zinc-700/50 font-medium">
                                                <svg class="w-3 h-3 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                {{ $act->causer->name ?? 'سیستم' }}
                                            </span>
                                        </td>
                                        <td class="p-4 font-mono text-zinc-400 text-[11px]" dir="ltr">{{ \Morilog\Jalali\Jalalian::forge($act->created_at)->format('Y/m/d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-12 text-center text-zinc-500 font-medium">رخدادی برای این کاربر ثبت نشده است.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($account->service_group === 'wireguard' && $activeTab === 'wg_configs')
                        <div class="p-6 animate-fade-in" wire:key="tab-wg">
                            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-sm font-bold text-white">مدیریت کانفیگ‌ها (Peers)</h2>
                                    <p class="text-[11px] text-zinc-400 mt-1">ساخت و مدیریت دستگاه‌های متصل به سرور وایرگارد</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <select wire:model="newWgServerId" class="bg-zinc-900 border border-zinc-700/80 text-white text-xs rounded-xl p-2.5 focus:ring-1 focus:ring-emerald-500 outline-none">
                                        <option value="">انتخاب سرور...</option>
                                        @foreach($allWgServers as $srv) <option value="{{ $srv->id }}">{{ $srv->name }}</option> @endforeach
                                    </select>
                                    <button wire:click="createWgConfig" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        ساخت کانفیگ
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                                @forelse($wgConfigs ?? [] as $wg)
                                    @php $srv = \App\Models\Nas::find($wg->server_id); @endphp
                                    <div class="bg-zinc-950 border {{ $wg->is_enabled ? 'border-zinc-800/80' : 'border-rose-900/50 bg-rose-950/10' }} rounded-[1.5rem] p-5 flex flex-col transition-all hover:border-zinc-700">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 class="text-sm font-bold text-white font-mono flex items-center gap-2" dir="ltr">
                                                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    {{ $wg->profile_name }}
                                                </h3>
                                                <span class="text-[10px] text-zinc-500 font-mono mt-1.5 block" dir="ltr">IP: {{ $wg->user_ip }} | سرور: {{ $srv->name ?? 'نامشخص' }}</span>
                                            </div>
                                            <button wire:click="toggleWgConfig({{ $wg->id }})" class="relative h-5 w-9 rounded-full transition-colors duration-200 {{ $wg->is_enabled ? 'bg-emerald-500' : 'bg-zinc-700' }}">
                                                <span class="absolute top-[2px] bg-white w-4 h-4 rounded-full transition-transform duration-200 {{ $wg->is_enabled ? 'left-[2px]' : 'translate-x-[16px] left-[2px]' }}"></span>
                                            </button>
                                        </div>

                                        <div class="bg-zinc-900/80 rounded-xl p-3 mb-3 flex justify-between items-center border border-zinc-800/50">
                                            <div class="text-center w-full">
                                                <span class="block text-[9px] text-zinc-500 mb-1">دانلود (TX)</span>
                                                <span class="text-xs font-bold text-emerald-400 font-mono" dir="ltr">{{ isset($account) ? $account->formatBytes($wg->tx) : '0 B' }}</span>
                                            </div>
                                            <div class="w-px h-6 bg-zinc-800 mx-2"></div>
                                            <div class="text-center w-full">
                                                <span class="block text-[9px] text-zinc-500 mb-1">آپلود (RX)</span>
                                                <span class="text-xs font-bold text-blue-400 font-mono" dir="ltr">{{ isset($account) ? $account->formatBytes($wg->rx) : '0 B' }}</span>
                                            </div>
                                        </div>

                                        <div class="bg-zinc-900/40 rounded-xl p-3 mb-4 border border-zinc-800/50">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-[9px] font-bold text-zinc-500">لیمیت فعلی گروه:</span>
                                                <span class="text-[10px] font-bold text-purple-400 font-mono" dir="ltr">
                                                    @if(method_exists($this, 'getAccountBaseSpeed'))
                                                        {{ $this->getAccountBaseSpeed() }}
                                                    @else
                                                        {{ $wg->config_limit ?? '10M/10M' }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="border-t border-zinc-800/80 pt-2 mt-2">
                                                <label class="block text-[9px] font-bold text-zinc-400 mb-1.5">تغییر دستی لیمیت روتر (M/M):</label>
                                                <div class="flex gap-2">
                                                    <input type="text" wire:model="configSpeedLimit.{{ $wg->id }}" dir="ltr" class="flex-1 bg-zinc-950 border border-zinc-700/50 text-white font-mono text-[10px] rounded-lg p-2 focus:ring focus:ring-purple-500/20 outline-none" placeholder="مثال: 20M/5M">
                                                    <button wire:click="updateConfigSpeed({{ $wg->id }})" class="px-3 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-[10px] rounded-lg transition shadow-md shadow-purple-600/20">ارسال</button>
                                                </div>
                                                @if (session()->has('success_' . $wg->id))
                                                    <span class="text-[9px] text-emerald-400 font-bold mt-1.5 block">✓ {{ session('success_' . $wg->id) }}</span>
                                                @endif
                                                @if (session()->has('error_' . $wg->id))
                                                    <span class="text-[9px] text-rose-400 font-bold mt-1.5 block">✗ {{ session('error_' . $wg->id) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-4 gap-2 pt-4 border-t border-zinc-800/60 mt-auto">
                                            <a href="{{ asset('configs/' . $wg->profile_name . '.png') }}" target="_blank" title="مشاهده QR Code" class="py-2.5 flex justify-center items-center bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white rounded-xl transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                            </a>
                                            <a href="{{ asset('configs/' . $wg->profile_name . '.conf') }}" download title="دانلود فایل کانفیگ" class="py-2.5 flex justify-center items-center bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white rounded-xl transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>
                                            <button wire:click="openChangeServerModal({{ $wg->id }})" title="انتقال به سرور دیگر" class="py-2.5 flex justify-center items-center bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white rounded-xl transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            </button>
                                            <button wire:click="deleteWgConfig({{ $wg->id }})" onclick="confirm('آیا از حذف این کانفیگ مطمئن هستید؟') || event.stopImmediatePropagation()" title="حذف دائمی کانفیگ" class="py-2.5 flex justify-center items-center bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-xl transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full py-12 flex flex-col items-center justify-center border-2 border-dashed border-zinc-800/50 rounded-3xl bg-zinc-950/20">
                                        <p class="text-zinc-500 font-medium text-sm">هنوز هیچ کانفیگی برای این کاربر ساخته نشده است.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-gradient-to-br from-orange-500/10 to-rose-500/5 border border-orange-500/20 rounded-[2rem] p-6 shadow-lg shadow-orange-500/5 relative overflow-hidden">
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-orange-500/20 flex items-center justify-center text-orange-400 border border-orange-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-orange-400/80">اعتبار باقیمانده سرویس</span>
                        <div class="flex items-baseline gap-1 mt-0.5">
                            @if($account->expired || ($account->expire_date && $daysRemaining <= 0))
                                <span class="text-2xl font-black text-rose-500 font-mono animate-pulse">منقضی شده</span>
                            @elseif(!$account->expire_date)
                                <span class="text-2xl font-black text-white font-mono">بدون انقضا</span>
                            @else
                                <span class="text-3xl font-black text-white font-mono">{{ $daysRemaining }}</span>
                                <span class="text-sm text-orange-200">روز</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-[11px] font-bold text-orange-300 bg-orange-500/10 px-3 py-2 rounded-xl border border-orange-500/20 relative z-10" dir="ltr">انقضا: {{ $expireDateFormatted }}</div>
                <svg class="absolute -bottom-4 -right-4 w-32 h-32 text-orange-500/10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path></svg>
            </div>

            @if($account->max_usage > 0)
                @php
                    $usage = $account->usage ?? 0;
                    $maxUsage = $account->max_usage;
                    $usagePercent = min(100, round(($usage / $maxUsage) * 100, 1));
                    $volTextColor = $usagePercent >= 90 ? 'text-rose-400' : ($usagePercent >= 75 ? 'text-amber-400' : 'text-blue-400');
                    $volBgColor   = $usagePercent >= 90 ? 'bg-rose-500 shadow-rose-500/50' : ($usagePercent >= 75 ? 'bg-amber-500 shadow-amber-500/50' : 'bg-blue-500 shadow-blue-500/50');
                @endphp
                <div class="bg-gradient-to-br from-blue-500/10 to-purple-500/5 border border-blue-500/20 rounded-[2rem] p-6 shadow-lg shadow-blue-500/5 relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-4 relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-500/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-blue-400/80">ترافیک باقیمانده حساب</span>
                            <div class="flex items-baseline gap-1 mt-0.5">
                                <span class="text-2xl font-black {{ $volTextColor }} font-mono" dir="ltr">{{ $account->formatBytes(max(0, $maxUsage - $usage)) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10">
                        <div class="flex justify-between text-[11px] font-bold text-zinc-400 mb-2">
                            <span>مصرف: <span dir="ltr">{{ $account->formatBytes($usage) }}</span></span>
                            <span>سقف: <span dir="ltr">{{ $account->formatBytes($maxUsage) }}</span></span>
                        </div>
                        <div class="w-full bg-zinc-950/60 rounded-full h-2 border border-zinc-800/50 overflow-hidden">
                            <div class="{{ $volBgColor }} h-full rounded-full transition-all duration-1000 shadow-lg" style="width: {{ $usagePercent }}%"></div>
                        </div>
                    </div>

                    <svg class="absolute -top-4 -left-4 w-32 h-32 text-blue-500/5 rotate-180" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path></svg>
                </div>
            @endif

            <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] p-6 shadow-xl relative">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-purple-500/10 text-purple-400 rounded-xl border border-purple-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">زنجیره سازندگان و بالادستی</h3>
                        <p class="text-[10px] text-zinc-400 mt-0.5">مسیر سلسله‌مراتب ساخت اکانت</p>
                    </div>
                </div>

                @php
                    $currentCreator = \App\Models\User::find($account->creator);
                    $level = 1;
                @endphp

                @if($currentCreator)
                    <div class="relative before:absolute before:inset-0 before:ml-[1.1rem] before:-translate-x-px md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-zinc-700 before:via-zinc-800 before:to-transparent space-y-4">
                        @while($currentCreator)
                            @php
                                $isUserRole = ($currentCreator->role === 'customer');
                                $profileUrl = $isUserRole ? route('admin.users.show', $currentCreator->id) : route('admin.managers.edit', $currentCreator->id);
                            @endphp

                            <div class="relative flex items-center gap-4">
                                <div class="w-9 h-9 rounded-full bg-zinc-900 border-2 border-zinc-700 flex items-center justify-center text-zinc-400 font-black text-[10px] z-10 shrink-0 shadow-md">
                                    L{{ $level }}
                                </div>
                                <div class="flex-1 bg-zinc-950 border border-zinc-800/80 rounded-2xl p-3 flex flex-col justify-center hover:border-zinc-700 transition-colors">
                                    <div class="flex justify-between items-center mb-1">
                                        <a href="{{ $profileUrl }}" wire:navigate class="text-xs font-bold text-white hover:text-purple-400 transition-colors">{{ $currentCreator->name }}</a>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-zinc-800 text-zinc-400">{{ $level === 1 ? 'کاربر' : 'بالادستی' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-zinc-500 font-mono" dir="ltr">
                                        <span>{{ $currentCreator->role ?? 'نامشخص' }}</span>
                                        <span class="w-1 h-1 rounded-full bg-zinc-700"></span>
                                        <span>{{ $currentCreator->phone ?? 'بدون شماره' }}</span>
                                    </div>
                                </div>
                            </div>

                            @php
                                $currentCreator = $currentCreator->parentAgent;
                                $level++;
                            @endphp
                        @endwhile
                    </div>
                @else
                    <div class="text-center py-6 text-xs font-medium text-zinc-500 bg-zinc-950/50 rounded-2xl border border-zinc-800/50 border-dashed">
                        مستقیماً توسط مدیر کل سیستم ساخته شده است.
                    </div>
                @endif
            </div>

        </div>
    </div>

    @if($isAdjustmentModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in" wire:key="adjustment-modal">
            <div class="relative w-full max-w-lg bg-zinc-900 border border-zinc-700/60 rounded-[2.5rem] shadow-2xl overflow-hidden">

                <div class="flex items-center justify-between px-7 py-5 border-b border-zinc-800/80 bg-zinc-900/80">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white tracking-tight">تغییر دستی حجم و زمان</h2>
                            <p class="text-[11px] text-zinc-400 mt-0.5">اصلاح مستقیم ترافیک یا انقضای سرویس کاربر</p>
                        </div>
                    </div>
                    <button wire:click="$set('isAdjustmentModalOpen', false)" class="p-2 text-zinc-400 hover:text-white bg-zinc-800/60 hover:bg-zinc-700 rounded-full transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-7 space-y-6">
                    <form wire:submit.prevent="submitAdjustment" class="space-y-6">

                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-3">۱. انتخاب نوع عملیات</label>
                            <div class="grid grid-cols-2 gap-3">

                                <button type="button" wire:click="$set('adjustAction', 'add_days')" class="p-3.5 rounded-2xl border text-right transition-all flex items-center gap-3 relative overflow-hidden {{ $adjustAction === 'add_days' ? 'bg-emerald-500/10 border-emerald-500/50 text-white shadow-lg shadow-emerald-500/5' : 'bg-zinc-950/60 border-zinc-800 text-zinc-400 hover:border-zinc-700 hover:text-zinc-200' }}">
                                    <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center font-bold text-xs {{ $adjustAction === 'add_days' ? 'bg-emerald-500 text-zinc-950' : 'bg-zinc-800 text-emerald-400' }}">
                                        +
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block text-xs font-bold leading-tight">افزایش زمان</span>
                                        <span class="text-[10px] text-zinc-500 font-medium mt-0.5 block">تمدید اعتبار (روز)</span>
                                    </div>
                                </button>

                                <button type="button" wire:click="$set('adjustAction', 'reduce_days')" class="p-3.5 rounded-2xl border text-right transition-all flex items-center gap-3 relative overflow-hidden {{ $adjustAction === 'reduce_days' ? 'bg-rose-500/10 border-rose-500/50 text-white shadow-lg shadow-rose-500/5' : 'bg-zinc-950/60 border-zinc-800 text-zinc-400 hover:border-zinc-700 hover:text-zinc-200' }}">
                                    <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center font-bold text-xs {{ $adjustAction === 'reduce_days' ? 'bg-rose-500 text-white' : 'bg-zinc-800 text-rose-400' }}">
                                        -
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block text-xs font-bold leading-tight">کسر زمان</span>
                                        <span class="text-[10px] text-zinc-500 font-medium mt-0.5 block">کاهش اعتبار (روز)</span>
                                    </div>
                                </button>

                                <button type="button" wire:click="$set('adjustAction', 'add_volume')" class="p-3.5 rounded-2xl border text-right transition-all flex items-center gap-3 relative overflow-hidden {{ $adjustAction === 'add_volume' ? 'bg-blue-500/10 border-blue-500/50 text-white shadow-lg shadow-blue-500/5' : 'bg-zinc-950/60 border-zinc-800 text-zinc-400 hover:border-zinc-700 hover:text-zinc-200' }}">
                                    <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center font-bold text-xs {{ $adjustAction === 'add_volume' ? 'bg-blue-500 text-white' : 'bg-zinc-800 text-blue-400' }}">
                                        +
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block text-xs font-bold leading-tight">افزایش حجم</span>
                                        <span class="text-[10px] text-zinc-500 font-medium mt-0.5 block">اضافه به سقف (GB)</span>
                                    </div>
                                </button>

                                <button type="button" wire:click="$set('adjustAction', 'reduce_volume')" class="p-3.5 rounded-2xl border text-right transition-all flex items-center gap-3 relative overflow-hidden {{ $adjustAction === 'reduce_volume' ? 'bg-amber-500/10 border-amber-500/50 text-white shadow-lg shadow-amber-500/5' : 'bg-zinc-950/60 border-zinc-800 text-zinc-400 hover:border-zinc-700 hover:text-zinc-200' }}">
                                    <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center font-bold text-xs {{ $adjustAction === 'reduce_volume' ? 'bg-amber-500 text-zinc-950' : 'bg-zinc-800 text-amber-400' }}">
                                        -
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block text-xs font-bold leading-tight">کسر حجم</span>
                                        <span class="text-[10px] text-zinc-500 font-medium mt-0.5 block">کاهش ترافیک (GB)</span>
                                    </div>
                                </button>

                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-bold text-zinc-300">۲. مقدار مورد نظر</label>
                                <span class="text-[11px] font-bold text-blue-400 bg-blue-500/10 px-2.5 py-0.5 rounded-md border border-blue-500/20">
                                {{ in_array($adjustAction, ['add_days', 'reduce_days']) ? 'واحد: روز' : 'واحد: گیگابایت (GB)' }}
                            </span>
                            </div>

                            <div class="relative">
                                <input wire:model="adjustValue" type="number" step="0.1" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 text-white text-lg font-mono font-bold rounded-2xl p-4 pr-12 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/60 outline-none transition-all placeholder:text-zinc-600" placeholder="مثلاً: 10">

                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-zinc-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16"></path></svg>
                                </div>
                            </div>
                            @error('adjustValue') <span class="text-rose-400 text-[11px] font-bold mt-1.5 block">{{ $message }}</span> @enderror

                            <div class="flex items-center gap-2 mt-3">
                                <span class="text-[10px] text-zinc-500 font-bold ml-1">انتخاب سریع:</span>
                                @if(in_array($adjustAction, ['add_days', 'reduce_days']))
                                    @foreach([5, 10, 15, 30] as $preset)
                                        <button type="button" wire:click="$set('adjustValue', '{{ $preset }}')" class="px-2.5 py-1 rounded-lg bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 text-zinc-400 hover:text-white text-[11px] font-mono transition-all">
                                            {{ $preset }} روز
                                        </button>
                                    @endforeach
                                @else
                                    @foreach([5, 10, 20, 50] as $preset)
                                        <button type="button" wire:click="$set('adjustValue', '{{ $preset }}')" class="px-2.5 py-1 rounded-lg bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 text-zinc-400 hover:text-white text-[11px] font-mono transition-all">
                                            {{ $preset }} GB
                                        </button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-800/80 flex items-center justify-end gap-3">
                            <button type="button" wire:click="$set('isAdjustmentModalOpen', false)" class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-xs rounded-xl transition-all">
                                انصراف
                            </button>

                            <button type="submit" wire:loading.attr="disabled" class="px-7 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                                <svg wire:loading wire:target="submitAdjustment" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                اعمال تغییرات
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($isRechargeModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in" wire:key="recharge-modal">
            <div class="relative w-full max-w-sm bg-zinc-900 border border-zinc-700/50 rounded-[2rem] shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-800/60 bg-zinc-900">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        شارژ و تمدید سرویس
                    </h2>
                    <button wire:click="$set('isRechargeModalOpen', false)" class="p-1 text-zinc-500 hover:text-white bg-zinc-800/50 hover:bg-zinc-700 rounded-full transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6">
                    <p class="text-[11px] text-zinc-400 mb-5 leading-relaxed bg-orange-500/10 px-4 py-3 rounded-xl border border-orange-500/20">
                        با شارژ مجدد، ترافیک مصرفی صفر شده و دوره جدید بر اساس گروه انتخابی آغاز می‌شود (حداکثر ۳ روز باقیمانده بونوس داده می‌شود).
                    </p>
                    <form wire:submit.prevent="confirmRecharge" class="space-y-5">
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-2">تعرفه بسته جدید</label>
                            <select wire:model="recharge_group_id" class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-xl p-3 focus:ring focus:ring-orange-500/20 focus:border-orange-500/50 outline-none transition-all">
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center pt-1">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="pay_from_agent_wallet" class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500 border border-zinc-700"></div>
                                <span class="ms-3 text-xs font-bold text-zinc-300">کسر هزینه پلکانی از نماینده</span>
                            </label>
                        </div>
                        @error('wallet') <span class="text-rose-500 text-[11px] font-bold block mt-2 bg-rose-500/10 p-2 rounded-lg">{{ $message }}</span> @enderror
                        <div class="pt-3 flex justify-end gap-3">
                            <button type="button" wire:click="$set('isRechargeModalOpen', false)" class="px-5 py-2.5 bg-zinc-800 text-white font-bold text-xs rounded-xl hover:bg-zinc-700">انصراف</button>
                            <button type="submit" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-orange-500/25 flex items-center gap-2">
                                <svg wire:loading wire:target="confirmRecharge" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                تایید و شارژ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($isEditModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in" wire:key="edit-modal-container">
            <div class="relative w-full max-w-3xl bg-zinc-900 border border-zinc-700/50 rounded-[2rem] shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between px-8 py-5 border-b border-zinc-800/60 bg-zinc-900">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        تنظیمات و ویرایش حساب
                    </h2>
                    <button wire:click="$set('isEditModalOpen', false)" class="p-1 text-zinc-500 hover:text-white bg-zinc-800/50 hover:bg-zinc-700 rounded-full transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div class="p-8 overflow-y-auto space-y-6">
                    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-5">
                            <h3 class="text-xs font-black text-orange-500 uppercase tracking-widest border-b border-zinc-800/60 pb-2">اطلاعات اتصال</h3>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2">نام کاربری</label>
                                <input wire:model="username" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm font-mono focus:ring focus:ring-orange-500/20 focus:border-orange-500/50 outline-none transition-all">
                                @error('username') <span class="text-rose-500 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2">کلمه عبور</label>
                                <input wire:model="password" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm font-mono focus:ring focus:ring-orange-500/20 focus:border-orange-500/50 outline-none transition-all">
                            </div>

                            <div class="relative mt-2" x-data="{ open: false }" @click.away="open = false">
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2">کاربر متصل (مشتری)</label>
                                @if($selectedCustomerName)
                                    <div class="flex items-center justify-between p-3 bg-zinc-950 border border-emerald-500/30 rounded-xl">
                                        <span class="text-xs font-bold text-emerald-400">{{ $selectedCustomerName }}</span>
                                        <button type="button" wire:click="$set('assigned_user_id', null); $set('selectedCustomerName', '')" class="text-[10px] text-rose-500 hover:underline font-bold">تغییر</button>
                                    </div>
                                @else
                                    <input wire:model.live.debounce.200ms="searchCustomer" @focus="open = true" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-xs focus:ring focus:ring-orange-500/20 outline-none" placeholder="جستجوی نام یا شماره مشتری...">
                                @endif
                                @if(!empty($searchedCustomers))
                                    <div x-show="open" class="absolute z-50 right-0 left-0 mt-2 bg-zinc-800 border border-zinc-700 rounded-xl shadow-2xl max-h-48 overflow-y-auto divide-y divide-zinc-700/50">
                                        @foreach($searchedCustomers as $u)
                                            <button type="button" wire:click="selectCustomer({{ $u->id }}, '{{ $u->name }}', '{{ $u->phone }}')" @click="open = false" class="w-full text-right p-3 hover:bg-zinc-700 transition flex justify-between items-center text-xs">
                                                <span class="font-bold text-white">{{ $u->name }}</span>
                                                <span class="text-zinc-400 font-mono" dir="ltr">{{ $u->phone ?? 'بدون شماره' }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h3 class="text-xs font-black text-orange-500 uppercase tracking-widest border-b border-zinc-800/60 pb-2">نماینده و تعرفه</h3>
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2">سازنده (نماینده)</label>
                                @if($selectedCreatorName)
                                    <div class="flex items-center justify-between p-3 bg-zinc-950 border border-purple-500/30 rounded-xl">
                                        <span class="text-xs font-bold text-purple-400">{{ $selectedCreatorName }}</span>
                                        <button type="button" wire:click="$set('creator', null); $set('selectedCreatorName', '')" class="text-[10px] text-rose-500 hover:underline font-bold">تغییر</button>
                                    </div>
                                @else
                                    <input wire:model.live.debounce.200ms="searchCreator" @focus="open = true" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-xs focus:ring focus:ring-orange-500/20 outline-none" placeholder="جستجوی نماینده...">
                                @endif
                                @if(!empty($searchedCreators))
                                    <div x-show="open" class="absolute z-50 right-0 left-0 mt-2 bg-zinc-800 border border-zinc-700 rounded-xl shadow-2xl max-h-48 overflow-y-auto divide-y divide-zinc-700/50">
                                        @foreach($searchedCreators as $u)
                                            <button type="button" wire:click="selectCreator({{ $u->id }}, '{{ $u->name }}', '{{ $u->role }}')" @click="open = false" class="w-full text-right p-3 hover:bg-zinc-700 transition flex justify-between items-center text-xs">
                                                <span class="font-bold text-white">{{ $u->name }}</span>
                                                <span class="text-orange-400 text-[10px]">{{ $u->role }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2">گروه کاربری</label>
                                <select wire:model="group_id" class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-xl p-3 focus:ring focus:ring-orange-500/20 focus:border-orange-500/50 outline-none transition-all">
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="px-8 py-5 border-t border-zinc-800/60 bg-zinc-900 flex items-center justify-end gap-4">
                    <button wire:click="$set('isEditModalOpen', false)" class="px-6 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-sm rounded-xl transition-colors">انصراف</button>
                    <button wire:click="save" class="px-8 py-2.5 bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/25 transition-all">ذخیره تغییرات</button>
                </div>
            </div>
        </div>
    @endif


    @if($isChangeServerModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in" wire:key="change-server-modal">
            <div class="relative w-full max-w-md bg-zinc-900 border border-zinc-700/60 rounded-[2.5rem] shadow-2xl overflow-hidden">

                <div class="flex items-center justify-between px-7 py-5 border-b border-zinc-800/80 bg-zinc-900/80">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white tracking-tight">انتقال به سرور جدید</h2>
                            <p class="text-[11px] text-zinc-400 mt-0.5">جابه‌جایی کانفیگ وایرگارد به سرور مقصد</p>
                        </div>
                    </div>
                    <button wire:click="$set('isChangeServerModalOpen', false)" class="p-2 text-zinc-400 hover:text-white bg-zinc-800/60 hover:bg-zinc-700 rounded-full transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-7 space-y-5">
                    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs leading-relaxed font-medium flex gap-3 items-start">
                        <svg class="w-5 h-5 shrink-0 text-rose-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            با انتقال سرور، این کانفیگ از سرور فعلی حذف شده و با <strong>کلید و آی‌پی جدید</strong> روی سرور مقصد ساخته می‌شود.
                        </div>
                    </div>

                    <form wire:submit.prevent="changeWgServer" class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-2">انتخاب سرور مقصد</label>
                            <select wire:model="newWgServerId" class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-2xl p-3.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/60 transition-all">
                                <option value="">یک سرور انتخاب کنید...</option>
                                @foreach($allWgServers ?? [] as $srv)
                                    <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->ipaddress }})</option>
                                @endforeach
                            </select>
                            @error('newWgServerId') <span class="text-rose-400 text-[11px] font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-3 border-t border-zinc-800/80 flex items-center justify-end gap-3">
                            <button type="button" wire:click="$set('isChangeServerModalOpen', false)" class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-xs rounded-xl transition-all">
                                انصراف
                            </button>

                            <button type="submit" wire:loading.attr="disabled" class="px-7 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                                <svg wire:loading wire:target="changeWgServer" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                انتقال کانفیگ
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    @endif

</div>
