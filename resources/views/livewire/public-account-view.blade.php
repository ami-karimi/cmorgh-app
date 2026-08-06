<div class="min-h-screen bg-zinc-950 text-zinc-100 font-sans p-4 sm:p-6 flex justify-center items-start pt-6 pb-16">
    <div class="w-full max-w-xl space-y-6">

        <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-[2rem] p-4 px-6 flex items-center justify-between shadow-xl">
            <div class="flex items-center gap-3.5">
                @if($brandLogo)
                    <img src="{{ asset($brandLogo) }}" alt="{{ $brandName }}" class="w-11 h-11 rounded-2xl object-cover border border-zinc-700/50 shadow-md">
                @else
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-orange-500/20">
                        {{ mb_substr($brandName, 0, 1) }}
                    </div>
                @endif
                <div>
                    <span class="text-[10px] font-bold text-zinc-500 block">ارائه‌دهنده سرویس</span>
                    <h2 class="text-sm font-black text-white tracking-wide">{{ $brandName }}</h2>
                </div>
            </div>

            @if($supportId)
                <a href="https://t.me/{{ ltrim($supportId, '@') }}" target="_blank" class="px-3.5 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 border border-blue-500/20 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.01-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.23.14.33-.01.06-.01.18-.02.26z"/></svg>
                    پشتیبانی
                </a>
            @endif
        </div>

        @php
            $maxUsage = $account->max_usage ?? 0;
            $usage = $account->usage ?? 0;

            $maxUsageStr = ($maxUsage == 0) ? 'نامحدود' : (method_exists($account, 'formatBytes') ? $account->formatBytes($maxUsage) : $maxUsage);
            $usageStr = method_exists($account, 'formatBytes') ? $account->formatBytes($usage) : $usage;
            $remainingBytes = ($maxUsage == 0) ? 0 : max(0, $maxUsage - $usage);
            $remainingStr = ($maxUsage == 0) ? 'نامحدود' : (method_exists($account, 'formatBytes') ? $account->formatBytes($remainingBytes) : $remainingBytes);

            $usagePercent = ($maxUsage > 0) ? min(100, round(($usage / $maxUsage) * 100)) : 0;
            $volTextColor = $usagePercent > 90 ? 'text-rose-400' : ($usagePercent > 75 ? 'text-amber-400' : 'text-emerald-400');
            $volBgColor   = $usagePercent > 90 ? 'bg-rose-500' : ($usagePercent > 75 ? 'bg-amber-500' : 'bg-emerald-500');
        @endphp

        <div class="relative overflow-hidden bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-[2.5rem] p-6 shadow-2xl space-y-5">
            <div class="absolute top-0 right-0 w-48 h-48 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800/80 pb-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-800 border border-zinc-700/50 flex items-center justify-center text-orange-400 font-black text-xl shadow-inner shrink-0">
                        🔑
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h1 class="text-xl font-black text-white font-mono" dir="ltr">@ {{ $account->username }}</h1>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold text-orange-400 bg-orange-500/10 border border-orange-500/20 px-2.5 py-0.5 rounded-lg">
                                📦 {{ $groupName }}
                            </span>
                            <span class="text-[10px] font-extrabold text-zinc-500 uppercase font-mono">
                                ({{ $account->service_group }})
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex sm:flex-col items-start sm:items-end justify-between gap-2 border-t sm:border-t-0 border-zinc-800/60 pt-3 sm:pt-0">
                    <span class="px-3 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider border {{ $account->is_enabled ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20' }}">
                        {{ $account->is_enabled ? '● حساب فعال' : '● حساب مسدود' }}
                    </span>

                    <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-zinc-950 border border-zinc-800 text-[11px] font-bold">
                        <div class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-emerald-400 animate-pulse shadow-[0_0_8px_rgba(52,211,153,0.8)]' : 'bg-zinc-600' }}"></div>
                        <span class="{{ $isOnline ? 'text-emerald-400' : 'text-zinc-500' }}">
                            {{ $isOnline ? 'آنلاین (متصل)' : 'آفلاین' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 relative z-10">

                <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-2xl p-4 flex flex-col justify-between">
                    <span class="text-[11px] font-bold text-zinc-400 mb-2">اعتبار زمانی سرویس:</span>
                    <div class="mb-2">
                        @if($isExpired)
                            <span class="text-lg font-black text-rose-500">منقضی شده</span>
                        @elseif($isFirstLogin)
                            <span class="text-sm font-black text-blue-400">در انتظار اولین اتصال</span>
                        @else
                            <span class="text-2xl font-black text-white font-mono">{{ $daysRemaining }}</span>
                            <span class="text-xs text-amber-300 font-bold">روز باقیمانده</span>
                        @endif
                    </div>
                    <span class="text-[10px] text-zinc-500 font-mono" dir="ltr">تاریخ انقضا: {{ $isFirstLogin ? 'پس از اولین اتصال' : $expireDateFormatted }}</span>
                </div>

                <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-2xl p-4 flex flex-col justify-between">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[11px] font-bold text-zinc-400">ترافیک باقیمانده:</span>
                        <span class="text-xs font-mono font-black text-zinc-500">{{ $usagePercent }}%</span>
                    </div>

                    <div class="mb-2">
                        <span class="text-xl font-black {{ $volTextColor }} font-mono" dir="ltr">{{ $remainingStr }}</span>
                    </div>

                    @if($maxUsage > 0)
                        <div class="w-full bg-zinc-900 rounded-full h-1.5 overflow-hidden border border-zinc-800/80 mb-1">
                            <div class="{{ $volBgColor }} h-full rounded-full transition-all duration-500" style="width: {{ $usagePercent }}%"></div>
                        </div>
                        <div class="flex justify-between text-[9px] text-zinc-500 font-mono">
                            <span>مصرفی: {{ $usageStr }}</span>
                            <span>کل: {{ $maxUsageStr }}</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        @if($account->service_group === 'wireguard')
            <div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-[2.5rem] p-6 shadow-2xl space-y-4">
                <h2 class="text-sm font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    دانلود کانفیگ و بارکد اتصال WireGuard
                </h2>

                <div class="space-y-4">
                    @forelse($wgConfigs as $wg)
                        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-white font-mono" dir="ltr">{{ $wg->profile_name }}</span>
                                <span class="text-[10px] text-zinc-500 font-mono" dir="ltr">IP: {{ $wg->user_ip }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ asset('configs/' . $wg->profile_name . '.png') }}" target="_blank" class="py-2.5 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white rounded-xl text-xs font-bold text-center border border-zinc-800 transition flex items-center justify-center gap-1.5">
                                    📷 بارکد QR
                                </a>
                                <a href="{{ asset('configs/' . $wg->profile_name . '.conf') }}" download="{{ $wg->profile_name }}.conf" class="py-2.5 bg-orange-600 hover:bg-orange-500 text-white rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 shadow-lg shadow-orange-600/20">
                                    📥 دانلود فایل Conf
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-zinc-500 font-bold border-2 border-dashed border-zinc-800/50 rounded-2xl">
                            کانفیگی یافت نشد.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

    </div>
</div>
