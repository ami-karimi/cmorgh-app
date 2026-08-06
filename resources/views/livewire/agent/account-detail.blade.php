<div class="space-y-6 pb-12 font-sans" wire:key="agent-account-detail-view">

    @php
        // محاسبه وضعیت انقضا
        $isExpired = $account->expire_date && \Carbon\Carbon::parse($account->expire_date)->isPast();
        $isFirstLogin = is_null($account->expire_date); // اگر نال باشد یعنی منتظر اولین اتصال است

        // وضعیت آنلاین (اگر متغیر متفاوتی دارید جایگزین کنید)
        $isOnline = $account->is_online ?? (isset($activeSessions) && count($activeSessions) > 0) ?? false;

        // متغیرهای حجم برای همه سرویس‌ها
        $maxUsage = $account->max_usage ?? 0;
        $usage = $account->usage ?? 0;

        $maxUsageStr = ($maxUsage == 0) ? 'نامحدود' : (method_exists($account, 'formatBytes') ? $account->formatBytes($maxUsage) : $maxUsage);
        $usageStr = method_exists($account, 'formatBytes') ? $account->formatBytes($usage) : $usage;
        $remainingBytes = ($maxUsage == 0) ? 0 : max(0, $maxUsage - $usage);
        $remainingStr = ($maxUsage == 0) ? 'نامحدود' : (method_exists($account, 'formatBytes') ? $account->formatBytes($remainingBytes) : $remainingBytes);

        $usagePercent = ($maxUsage > 0) ? min(100, round(($usage / $maxUsage) * 100)) : 0;
        $volTextColor = $usagePercent > 90 ? 'text-rose-400' : ($usagePercent > 75 ? 'text-amber-400' : 'text-emerald-400');
        $volBgColor   = $usagePercent > 90 ? 'bg-rose-500 shadow-rose-500/50' : ($usagePercent > 75 ? 'bg-amber-500 shadow-amber-500/50' : 'bg-emerald-500 shadow-emerald-500/50');

        $isRadiusService = in_array($account->service_group, ['l2tp_cisco', 'l2tp', 'openvpn']);
    @endphp

    @if($customer)
        <div class="bg-gradient-to-r from-zinc-900 to-zinc-950 border border-zinc-800/80 rounded-[1.5rem] p-4 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-zinc-500 block">مشتری متصل به اکانت</span>
                    <span class="text-sm font-black text-white">{{ $customer->name ?? 'نامشخص' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <div>
                    <span class="text-[10px] font-bold text-emerald-500 block">موجودی حساب مشتری</span>
                    <span class="text-sm font-black text-emerald-400 font-mono" dir="ltr">
                        {{ number_format($customer->wallet ?? $customer->balance ?? 0) }} <span class="text-[10px] font-sans">تومان</span>
                    </span>
                </div>
            </div>
        </div>
    @endif

    <div class="relative overflow-hidden bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/60 rounded-[2rem] p-6 shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/5 rounded-full blur-3xl pointer-events-none"></div>

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
                        <span class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full {{ $account->is_enabled ? 'bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]' }}"></div>
                            وضعیت: {{ $account->is_enabled ? 'فعال' : 'مسدود' }}
                        </span>
                        <span class="flex items-center gap-1.5 border-r border-zinc-700 pr-4">
                            <div class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-blue-500 animate-pulse shadow-[0_0_8px_rgba(59,130,246,0.6)]' : 'bg-zinc-600' }}"></div>
                            اتصال: <span class="{{ $isOnline ? 'text-blue-400 font-bold' : 'text-zinc-500' }}">{{ $isOnline ? 'آنلاین (متصل)' : 'آفلاین' }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button wire:click="toggleStatus" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2 {{ $account->is_enabled ? 'bg-zinc-950 text-rose-400 hover:bg-rose-500/10 border border-rose-500/20' : 'bg-zinc-950 text-emerald-400 hover:bg-emerald-500/10 border border-emerald-500/20' }}">
                    <svg wire:loading wire:target="toggleStatus" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                    {{ $account->is_enabled ? 'مسدودسازی' : 'فعال‌سازی' }}
                </button>

                <button wire:click="openEditModal" class="px-5 py-2.5 bg-zinc-800/80 hover:bg-zinc-700 text-white border border-zinc-700/50 rounded-xl text-sm font-bold transition-all">
                    ویرایش
                </button>

                <button wire:click="openRechargeModal" class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-400 hover:to-amber-400 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-orange-500/25 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    شارژ و تمدید
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="px-5 py-4 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl font-bold flex items-center gap-3">
            <div class="p-1.5 bg-emerald-500/20 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="xl:col-span-2 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] p-6 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-zinc-400 block mb-0.5">ترافیک باقیمانده</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xl font-black {{ $volTextColor }} font-mono" dir="ltr">{{ $remainingStr }}</span>
                                </div>
                            </div>
                        </div>
                        <span class="text-lg font-black text-zinc-700 font-mono">{{ $usagePercent }}%</span>
                    </div>
                    @if($maxUsage > 0)
                        <div class="w-full bg-zinc-950 rounded-full h-2 border border-zinc-800/50 overflow-hidden shadow-inner">
                            <div class="{{ $volBgColor }} h-full rounded-full transition-all duration-1000 shadow-lg" style="width: {{ $usagePercent }}%"></div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-[10px] font-bold text-zinc-500">مصرف شده: <span dir="ltr">{{ $usageStr }}</span></span>
                            <span class="text-[10px] font-bold text-zinc-600">کل: <span dir="ltr">{{ $maxUsageStr }}</span></span>
                        </div>
                    @endif
                </div>

                @if($account->service_group === 'wireguard')
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
                        <div class="text-[11px] font-bold text-purple-400 bg-purple-500/10 px-3 py-1.5 rounded-lg inline-block">تعداد {{ count($wgConfigs ?? []) }} دستگاه متصل (Peers)</div>
                    </div>
                @endif

                @if($isRadiusService)
                    <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] p-6 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-zinc-400">تعداد اتصالات موفق</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="text-2xl font-black text-white font-mono">{{ $totalConnections ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-[11px] font-bold text-blue-400 bg-blue-500/10 px-3 py-1.5 rounded-lg inline-block">سرویس Radius فعال</div>
                    </div>
                @endif
            </div>

            <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] overflow-hidden shadow-xl">

                <div class="p-2 border-b border-zinc-800/60 bg-zinc-950/30 overflow-x-auto">
                    <div class="flex gap-1 min-w-max">
                        @foreach([
                            'active_sessions' => $isRadiusService ? 'نشست‌های فعال (' . count($activeSessions ?? []) . ')' : null,
                            'session_history' => $isRadiusService ? 'تاریخچه نشست‌ها' : null,
                            'activities'      => 'رخدادها و تغییرات',
                            'wg_configs'      => $account->service_group === 'wireguard' ? 'لیست کانفیگ‌ها (Peers)' : null
                        ] as $tabKey => $tabLabel)
                            @if($tabLabel)
                                <button wire:click="$set('activeTab', '{{ $tabKey }}')"
                                        class="relative px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $activeTab === $tabKey ? 'bg-zinc-800 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/50' }}">
                                    {{ $tabLabel }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="p-0 relative min-h-[200px]">

                    <div wire:loading wire:target="activeTab" class="absolute inset-0 z-50 flex flex-col items-center justify-center bg-zinc-900/80 backdrop-blur-sm">
                        <svg class="w-8 h-8 text-orange-500 animate-spin mb-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path>
                        </svg>
                        <span class="text-xs font-bold text-zinc-400 animate-pulse">در حال دریافت اطلاعات...</span>
                    </div>

                    <div wire:loading.class="opacity-0 pointer-events-none" class="transition-opacity duration-300">

                        @if($activeTab === 'active_sessions' && $isRadiusService)
                            <div class="p-6 text-center text-zinc-500 text-sm">لیست نشست‌های فعال...</div>
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
                                    @forelse($activities ?? [] as $act)
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
                                <div class="mb-6 flex items-center justify-between">
                                    <div>
                                        <h2 class="text-sm font-bold text-white">کانفیگ‌های متصل (Peers)</h2>
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

                                            <div class="bg-zinc-900/80 rounded-xl p-3 mb-4 flex justify-between items-center border border-zinc-800/50">
                                                <div class="text-center w-full">
                                                    <span class="block text-[9px] text-zinc-500 mb-1">دانلود (TX)</span>
                                                    <span class="text-xs font-bold text-emerald-400 font-mono" dir="ltr">{{ method_exists($account, 'formatBytes') ? $account->formatBytes($wg->tx) : '0 B' }}</span>
                                                </div>
                                                <div class="w-px h-6 bg-zinc-800 mx-2"></div>
                                                <div class="text-center w-full">
                                                    <span class="block text-[9px] text-zinc-500 mb-1">آپلود (RX)</span>
                                                    <span class="text-xs font-bold text-blue-400 font-mono" dir="ltr">{{ method_exists($account, 'formatBytes') ? $account->formatBytes($wg->rx) : '0 B' }}</span>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-3 gap-2 mt-auto border-t border-zinc-800/60 pt-4">
                                                <a href="{{ asset('configs/' . $wg->profile_name . '.png') }}" target="_blank" title="مشاهده بارکد" class="py-2.5 flex justify-center items-center bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white rounded-xl text-[10px] font-bold transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                                    بارکد QR
                                                </a>

                                                <a href="{{ asset('configs/' . $wg->profile_name . '.conf') }}" download="{{ $wg->profile_name }}.conf" title="دانلود فایل" class="py-2.5 flex justify-center items-center bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white rounded-xl text-[10px] font-bold transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    دانلود فایل
                                                </a>

                                                <button wire:click="openChangeServerModal({{ $wg->id }})" title="انتقال سرور" class="py-2.5 flex justify-center items-center bg-blue-600/10 hover:bg-blue-600 text-blue-400 hover:text-white rounded-xl text-[10px] font-bold transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                                    انتقال سرور
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-span-full py-12 text-center text-sm font-bold text-zinc-500 border-2 border-dashed border-zinc-800/50 rounded-[1.5rem]">
                                            کانفیگی یافت نشد.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-gradient-to-br from-amber-500/10 to-orange-500/5 border border-amber-500/20 rounded-[2rem] p-6 shadow-lg relative overflow-hidden">
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 flex items-center justify-center text-amber-500 border border-amber-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-amber-500/80">وضعیت زمانی سرویس</span>
                        <div class="flex items-baseline gap-1 mt-0.5">
                            @if($isExpired)
                                <span class="text-xl font-black text-rose-500 drop-shadow-md">منقضی شده</span>
                            @elseif($isFirstLogin)
                                <span class="text-lg font-black text-blue-400 drop-shadow-md">در انتظار اولین اتصال</span>
                            @else
                                <span class="text-3xl font-black text-white font-mono">{{ $daysRemaining ?? 0 }}</span>
                                <span class="text-sm text-amber-200">روز باقیمانده</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-[11px] font-bold text-amber-300 bg-amber-500/10 px-3 py-2 rounded-xl border border-amber-500/20 relative z-10" dir="ltr">
                    انقضا: {{ $isFirstLogin ? 'محاسبه بعد از اولین اتصال' : ($expireDateFormatted ?? 'نامشخص') }}
                </div>
            </div>

            <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-[2rem] p-6 shadow-xl relative">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-purple-500/10 text-purple-400 rounded-xl border border-purple-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 01-2-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">زنجیره سازندگان و بالادستی</h3>
                        <p class="text-[10px] text-zinc-400 mt-0.5">مسیر سلسله‌مراتب ساخت و نظارت اکانت</p>
                    </div>
                </div>

                @php
                    $currentCreator = \App\Models\User::find($account->creator);
                    $level = 1;
                @endphp

                @if($currentCreator)
                    <div class="relative space-y-4">
                        @while($currentCreator)
                            @php
                                $isUserRole = in_array($currentCreator->role, ['user', 'customer']);
                                $profileUrl = $isUserRole
                                    ? route('reseller.users.show', $currentCreator->id)
                                    : "#";
                            @endphp

                            <div class="relative flex items-center gap-4">
                                <div class="w-9 h-9 rounded-full bg-zinc-900 border-2 border-zinc-700 flex items-center justify-center text-purple-400 font-black text-[10px] z-10 shrink-0 shadow-md">
                                    L{{ $level }}
                                </div>
                                <div class="flex-1 bg-zinc-950 border border-zinc-800/80 rounded-2xl p-3 flex flex-col justify-center hover:border-purple-500/40 transition-colors">
                                    <div class="flex justify-between items-center mb-1">
                                        <a href="{{ $profileUrl }}" wire:navigate class="text-xs font-bold text-white hover:text-purple-400 transition-colors">
                                            {{ $currentCreator->name }}
                                        </a>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-zinc-900 text-purple-400 border border-zinc-800">
                                        {{ $currentCreator->role ?? 'نامشخص' }}
                                    </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-zinc-500 font-mono" dir="ltr">
                                        <span>{{ $currentCreator->phone ?? $currentCreator->email ?? 'بدون اطلاعات تماس' }}</span>
                                    </div>
                                </div>
                            </div>

                            @php
                                $currentCreator = $currentCreator->parentAgent ?? $currentCreator->creatorUser ?? null;
                                $level++;
                            @endphp
                        @endwhile
                    </div>
                @else
                    <div class="text-center py-6 text-xs font-medium text-zinc-500 bg-zinc-950/50 rounded-2xl border border-zinc-800/50 border-dashed">
                        این اکانت بالادستی ندارد.
                    </div>
                @endif
            </div>

        </div>

    </div>

    @if($isRechargeModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in" wire:key="recharge-modal">
            <div class="relative w-full max-w-sm bg-zinc-900 border border-zinc-700/50 rounded-[2rem] shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-800/60 bg-zinc-900">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        شارژ و تمدید حساب کاربری
                    </h2>
                    <button wire:click="$set('isRechargeModalOpen', false)" class="p-1 text-zinc-500 hover:text-white bg-zinc-800/50 hover:bg-zinc-700 rounded-full transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6">
                    <div class="mb-5 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[11px] font-bold leading-relaxed text-justify">
                        نماینده گرامی، پس از تایید، هزینه پلن انتخابی به صورت سیستمی از موجودی کیف پول شما کسر خواهد شد.
                    </div>
                    <form wire:submit.prevent="confirmRecharge" class="space-y-5">
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-2">انتخاب پلن (بسته جدید)</label>
                            <select wire:model="selectedGroupId" class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-xl p-3 outline-none focus:ring focus:ring-amber-500/30">
                                @foreach($availableGroups ?? [] as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }} ({{ number_format($group->getFinalPriceFor(auth()->user())) }} تومان)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-3 border-t border-zinc-800/80">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" wire:model="pay_from_user_wallet" class="mt-0.5 w-4 h-4 rounded bg-zinc-950 border-zinc-700 text-amber-500 focus:ring-amber-500/30 focus:ring-offset-0 cursor-pointer">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-zinc-200 group-hover:text-amber-400 transition-colors">کسر هزینه از کیف پول کاربر (مشتری)</span>
                                    <span class="text-[10px] text-zinc-500 mt-0.5">در صورت غیرفعال بودن تیک، تمدید بدون کسر اعتبار از کیف پول کاربر انجام می‌شود.</span>
                                </div>
                            </label>
                        </div>

                        @error('wallet')
                        <div class="mt-3 p-2.5 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-start gap-2">
                            <svg class="w-4 h-4 text-rose-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-rose-400 text-[11px] font-bold leading-relaxed">{{ $message }}</span>
                        </div>
                        @enderror


                        <div class="pt-3 flex justify-end gap-3 border-t border-zinc-800/80">
                            <button type="button" wire:click="$set('isRechargeModalOpen', false)" class="px-5 py-2.5 bg-zinc-800 text-white font-bold text-xs rounded-xl hover:bg-zinc-700">انصراف</button>
                            <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-amber-500/25 flex items-center gap-2">
                                <svg wire:loading wire:target="confirmRecharge" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                <span wire:loading.remove wire:target="confirmRecharge">تایید نهایی و کسر از کیف‌پول</span>
                                <span wire:loading wire:target="confirmRecharge">در حال پردازش...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($isEditModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in" wire:key="edit-modal-container">
            <div class="relative w-full max-w-2xl bg-zinc-900 border border-zinc-700/50 rounded-[2rem] shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between px-8 py-5 border-b border-zinc-800/60 bg-zinc-900">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        ویرایش سریع اطلاعات اکانت
                    </h2>
                    <button wire:click="$set('isEditModalOpen', false)" class="p-1 text-zinc-500 hover:text-white bg-zinc-800/50 hover:bg-zinc-700 rounded-full transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div class="p-8 overflow-y-auto space-y-6">
                    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-5">
                            <h3 class="text-xs font-black text-orange-500 uppercase tracking-widest border-b border-zinc-800/60 pb-2">اطلاعات اتصال</h3>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2">نام کاربری</label>
                                <input wire:model="username" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm font-mono outline-none">
                                @error('username') <span class="text-rose-500 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2">کلمه عبور</label>
                                <input wire:model="password" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm font-mono outline-none">
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h3 class="text-xs font-black text-orange-500 uppercase tracking-widest border-b border-zinc-800/60 pb-2">اطلاعات دارنده</h3>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2">نام کامل خریدار</label>
                                <input wire:model="name" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2">شماره تماس</label>
                                <input wire:model="phonenumber" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm font-mono outline-none">
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
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        انتقال به سرور جدید
                    </h2>
                    <button wire:click="$set('isChangeServerModalOpen', false)" class="p-2 text-zinc-400 hover:text-white bg-zinc-800/60 hover:bg-zinc-700 rounded-full transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-7 space-y-5">
                    <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs leading-relaxed font-medium">
                        کانفیگ کاربر از سرور فعلی حذف شده و با کلید و آی‌پی جدید روی سرور مقصد ساخته می‌شود.
                    </div>

                    <form wire:submit.prevent="changeWgServer" class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-2">انتخاب سرور مقصد</label>
                            <select wire:model="newWgServerId" class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-2xl p-3.5 outline-none focus:ring-2 focus:ring-blue-500/30">
                                <option value="">یک سرور انتخاب کنید...</option>
                                @foreach($allWgServers ?? [] as $srv)
                                    <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->ipaddress }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pt-3 border-t border-zinc-800/80 flex items-center justify-end gap-3">
                            <button type="button" wire:click="$set('isChangeServerModalOpen', false)" class="px-6 py-3 bg-zinc-800 text-zinc-300 font-bold text-xs rounded-xl transition-all">انصراف</button>
                            <button type="submit" class="px-7 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-lg transition-all">انتقال کانفیگ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
