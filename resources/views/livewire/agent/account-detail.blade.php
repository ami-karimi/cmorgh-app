<div class="space-y-6 pb-12 font-sans" wire:key="agent-account-detail-view">
    {{-- ============================================ --}}
    {{-- 1. BREADCRUMB                               --}}
    {{-- ============================================ --}}
    <nav class="flex items-center gap-2 text-xs text-[#94A3B8]">
        <a href="{{ route('reseller.dashboard') }}" wire:navigate class="hover:text-[#F8FAFC] transition">نمایندگان</a>
        <span class="text-[#202938]">/</span>
        <a href="{{ route('reseller.accounts.index') }}" wire:navigate class="hover:text-[#F8FAFC] transition">مدیریت اکانت‌ها</a>
        <span class="text-[#202938]">/</span>
        <span class="text-[#F8FAFC] font-mono">{{ $account->username }}</span>
    </nav>

    {{-- ============================================ --}}
    {{-- 2. PAGE HEADER                               --}}
    {{-- ============================================ --}}
    <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Left: Account Identity --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-[#6366F1]/10 border border-[#6366F1]/20 flex items-center justify-center text-[#6366F1] font-bold text-xl">
                    {{ strtoupper(substr($account->username, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-xl font-bold text-[#F8FAFC] font-mono" dir="ltr">{{ $account->username }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#202938] text-[#94A3B8] border border-[#202938]">
                            {{ strtoupper($account->service_group) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs">
                            <span class="w-2 h-2 rounded-full {{ $account->is_enabled ? 'bg-[#10B981] animate-pulse' : 'bg-[#EF4444]' }}"></span>
                            <span class="font-medium {{ $account->is_enabled ? 'text-[#10B981]' : 'text-[#EF4444]' }}">
                                {{ $account->is_enabled ? 'فعال' : 'مسدود' }}
                            </span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs">
                            <span class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-[#10B981] animate-pulse' : 'bg-[#94A3B8]' }}"></span>
                            <span class="font-medium {{ $isOnline ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">
                                {{ $isOnline ? 'آنلاین' : 'آفلاین' }}
                            </span>
                        </span>
                    </div>
                    <p class="text-xs text-[#94A3B8] mt-1">
                        {{ $account->name ?? 'بدون اطلاعات مشتری' }}
                        @if($account->phonenumber)
                            • <span dir="ltr">{{ $account->phonenumber }}</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="openRechargeModal" class="px-5 py-2.5 bg-[#6366F1] hover:bg-[#4F46E5] text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-[#6366F1]/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    شارژ و تمدید
                </button>

                <button wire:click="openEditModal" class="px-4 py-2.5 bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] rounded-xl text-xs font-bold transition border border-[#202938]">
                    ویرایش
                </button>

                <button wire:click="toggleStatus" wire:loading.attr="disabled" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $account->is_enabled ? 'bg-[#EF4444]/10 text-[#EF4444] hover:bg-[#EF4444]/20 border border-[#EF4444]/20' : 'bg-[#10B981]/10 text-[#10B981] hover:bg-[#10B981]/20 border border-[#10B981]/20' }}">
                    <svg wire:loading wire:target="toggleStatus" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                    {{ $account->is_enabled ? 'مسدودسازی' : 'فعال‌سازی' }}
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 3. TOAST / FLASH MESSAGES                    --}}
    {{-- ============================================ --}}
    @if (session()->has('message'))
        <div class="px-5 py-4 text-sm text-[#10B981] bg-[#10B981]/10 border border-[#10B981]/20 rounded-xl font-bold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="px-5 py-4 text-sm text-[#EF4444] bg-[#EF4444]/10 border border-[#EF4444]/20 rounded-xl font-bold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 4. MAIN GRID: 2/3 + 1/3                     --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ========================================= --}}
        {{-- LEFT COLUMN (2/3)                         --}}
        {{-- ========================================= --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 4.1 KPI SUMMARY --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Status --}}
                <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full {{ $account->is_enabled ? 'bg-[#10B981]' : 'bg-[#EF4444]' }}"></span>
                        <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">وضعیت</span>
                    </div>
                    <span class="text-lg font-bold {{ $account->is_enabled ? 'text-[#10B981]' : 'text-[#EF4444]' }}">
                        {{ $account->is_enabled ? 'فعال' : 'مسدود' }}
                    </span>
                </div>

                {{-- Online --}}
                <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-[#10B981] animate-pulse' : 'bg-[#94A3B8]' }}"></span>
                        <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">اتصال</span>
                    </div>
                    <span class="text-lg font-bold {{ $isOnline ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">
                        {{ $isOnline ? 'آنلاین' : 'آفلاین' }}
                    </span>
                </div>

                {{-- Traffic Used --}}
                <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
                    <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider block mb-1">مصرف ترافیک</span>
                    <span class="text-lg font-bold text-[#F8FAFC] font-mono">{{ $usageStr }}</span>
                </div>

                {{-- Days Remaining --}}
                <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
                    <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider block mb-1">زمان باقیمانده</span>
                    @if($isExpired)
                        <span class="text-lg font-bold text-[#EF4444]">منقضی</span>
                    @elseif($isFirstLogin)
                        <span class="text-lg font-bold text-[#3B82F6]">در انتظار</span>
                    @else
                        <span class="text-lg font-bold text-[#F8FAFC] font-mono">{{ $daysRemaining }}</span>
                    @endif
                </div>
            </div>

            {{-- 4.2 TRAFFIC CARD --}}
            <div class="bg-[#111722] border border-[#202938] rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-[#F8FAFC]">مصرف ترافیک</h3>
                    <span class="text-xs font-bold text-[#94A3B8]">{{ $usagePercent }}%</span>
                </div>

                @if($maxUsage > 0)
                    <div class="flex items-baseline gap-2 mb-2">
                        <span class="text-2xl font-bold text-[#F8FAFC] font-mono">{{ $usageStr }}</span>
                        <span class="text-sm text-[#94A3B8]">از</span>
                        <span class="text-lg font-bold text-[#F8FAFC] font-mono">{{ $maxUsageStr }}</span>
                    </div>

                    <div class="w-full bg-[#202938] rounded-full h-2 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                             style="width: {{ min($usagePercent, 100) }}%; background-color: {{ $usagePercent < 75 ? '#10B981' : ($usagePercent < 90 ? '#F59E0B' : '#EF4444') }};">
                        </div>
                    </div>

                    <div class="flex justify-between mt-2 text-xs text-[#94A3B8]">
                        <span>مصرف شده: <span class="font-mono text-[#F8FAFC]">{{ $usageStr }}</span></span>
                        <span>باقیمانده: <span class="font-mono text-[#F8FAFC]">{{ $remainingStr }}</span></span>
                        <span>کل: <span class="font-mono text-[#F8FAFC]">{{ $maxUsageStr }}</span></span>
                    </div>
                @else
                    <div class="py-6 text-center">
                        <span class="text-lg font-bold text-[#10B981]">∞ ترافیک نامحدود</span>
                        <p class="text-xs text-[#94A3B8] mt-1">مصرف فعلی: <span class="font-mono text-[#F8FAFC]">{{ $usageStr }}</span></p>
                    </div>
                @endif
            </div>

            {{-- 4.3 TABS --}}
            <div class="bg-[#111722] border border-[#202938] rounded-xl overflow-hidden">
                {{-- Tab Navigation --}}
                <div class="p-2 border-b border-[#202938] bg-[#080B12] overflow-x-auto">
                    <div class="flex gap-1 min-w-max">
                        @if($account->service_group === 'wireguard')
                            <button wire:click="$set('activeTab', 'wg_configs')"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'wg_configs' ? 'bg-[#202938] text-[#F8FAFC]' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938]/50' }}">
                                دستگاه‌های متصل
                            </button>
                        @else
                            <button wire:click="$set('activeTab', 'active_sessions')"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'active_sessions' ? 'bg-[#202938] text-[#F8FAFC]' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938]/50' }}">
                                نشست‌های فعال
                            </button>
                            <button wire:click="$set('activeTab', 'session_history')"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'session_history' ? 'bg-[#202938] text-[#F8FAFC]' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938]/50' }}">
                                تاریخچه نشست‌ها
                            </button>
                        @endif
                        <button wire:click="$set('activeTab', 'activities')"
                                class="px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'activities' ? 'bg-[#202938] text-[#F8FAFC]' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938]/50' }}">
                            رخدادها
                        </button>
                    </div>
                </div>

                {{-- Tab Content --}}
                <div class="p-4 relative min-h-[200px]">
                    <div wire:loading wire:target="activeTab" class="absolute inset-0 z-10 flex items-center justify-center bg-[#111722]/80 backdrop-blur-sm rounded-b-xl">
                        <svg class="w-8 h-8 text-[#6366F1] animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                    </div>

                    <div wire:loading.class="opacity-50 pointer-events-none" class="transition-opacity duration-200">
                        {{-- WireGuard Peers --}}
                        @if($activeTab === 'wg_configs' && $account->service_group === 'wireguard')
                            @if($wgConfigs->count() > 0)
                                <div class="space-y-3">
                                    @foreach($wgConfigs as $wg)
                                        @php $srv = $allWgServers->firstWhere('id', $wg->server_id); @endphp
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-[#080B12] border border-[#202938] rounded-xl hover:border-[#6366F1]/30 transition-all gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-lg bg-[#8B5CF6]/10 border border-[#8B5CF6]/20 flex items-center justify-center text-[#8B5CF6] text-xs font-bold shrink-0">
                                                    {{ substr($wg->profile_name, 0, 2) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="text-sm font-bold text-[#F8FAFC] font-mono">{{ $wg->profile_name }}</span>
                                                        <span class="inline-flex items-center gap-1.5 text-[10px]">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $wg->is_enabled ? 'bg-[#10B981]' : 'bg-[#94A3B8]' }}"></span>
                                                            <span class="{{ $wg->is_enabled ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">
                                                                {{ $wg->is_enabled ? 'فعال' : 'غیرفعال' }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-3 text-xs text-[#94A3B8] mt-0.5">
                                                        <span dir="ltr">IP: {{ $wg->user_ip }}</span>
                                                        <span>|</span>
                                                        <span>سرور: {{ $srv->name ?? 'نامشخص' }}</span>
                                                        @if($wg->last_handshake)
                                                            <span>|</span>
                                                            <span>آخرین اتصال: {{ \Carbon\Carbon::parse($wg->last_handshake)->diffForHumans() }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <button wire:click="toggleWgConfig({{ $wg->id }})" class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition {{ $wg->is_enabled ? 'bg-[#EF4444]/10 text-[#EF4444] hover:bg-[#EF4444]/20' : 'bg-[#10B981]/10 text-[#10B981] hover:bg-[#10B981]/20' }}">
                                                    {{ $wg->is_enabled ? 'غیرفعال' : 'فعال' }}
                                                </button>

                                                @if($wg->profile_name)
                                                    <button wire:click="$set('showQrModal', true)" class="p-2 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938] transition" title="QR Code">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                                    </button>
                                                    <a href="{{ route('download.wg.config', ['profile' => $wg->profile_name]) }}" class="p-2 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938] transition" title="دانلود">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    </a>
                                                    <button wire:click="openChangeServerModal({{ $wg->id }})" class="p-2 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938] transition" title="انتقال سرور">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-12 text-center">
                                    <div class="w-16 h-16 rounded-full bg-[#202938] text-[#94A3B8] flex items-center justify-center mx-auto mb-3 text-2xl">📭</div>
                                    <h4 class="text-sm font-bold text-[#F8FAFC]">هیچ کانفیگ WireGuard ثبت نشده است</h4>
                                    <p class="text-xs text-[#94A3B8] mt-1">هنوز هیچ دستگاهی به این سرویس متصل نشده است.</p>
                                </div>
                            @endif
                        @endif

                        {{-- Active Sessions --}}
                        @if($activeTab === 'active_sessions' && $account->service_group !== 'wireguard')
                            @if($activeSessions->count() > 0)
                                <div class="space-y-2">
                                    @foreach($activeSessions as $session)
                                        <div class="flex items-center justify-between p-3 bg-[#080B12] border border-[#202938] rounded-xl">
                                            <div>
                                                <span class="text-sm font-bold text-[#F8FAFC] font-mono" dir="ltr">{{ $session->framedipaddress }}</span>
                                                <div class="flex items-center gap-3 text-xs text-[#94A3B8] mt-0.5">
                                                    <span>سرور: {{ $session->nasipaddress }}</span>
                                                    <span>|</span>
                                                    <span>از: {{ \Carbon\Carbon::parse($session->acctstarttime)->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <button wire:click="killSession({{ $session->radacctid }})" class="px-3 py-1.5 rounded-lg bg-[#EF4444]/10 text-[#EF4444] hover:bg-[#EF4444]/20 text-[10px] font-bold transition">
                                                قطع اتصال
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-12 text-center">
                                    <div class="w-16 h-16 rounded-full bg-[#202938] text-[#94A3B8] flex items-center justify-center mx-auto mb-3 text-2xl">⏳</div>
                                    <h4 class="text-sm font-bold text-[#F8FAFC]">هیچ نشست فعالی وجود ندارد</h4>
                                    <p class="text-xs text-[#94A3B8] mt-1">در حال حاضر هیچ کاربری به این سرویس متصل نیست.</p>
                                </div>
                            @endif
                        @endif

                        {{-- Session History --}}
                        @if($activeTab === 'session_history' && $account->service_group !== 'wireguard')
                            @if($sessionHistory->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-right text-xs">
                                        <thead class="text-[#94A3B8] border-b border-[#202938]">
                                        <tr>
                                            <th class="p-2 font-bold">IP</th>
                                            <th class="p-2 font-bold">سرور</th>
                                            <th class="p-2 font-bold">شروع</th>
                                            <th class="p-2 font-bold">پایان</th>
                                            <th class="p-2 font-bold">مدت</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#202938]">
                                        @foreach($sessionHistory as $session)
                                            <tr class="hover:bg-[#171E2B]/40 transition">
                                                <td class="p-2 font-mono text-[#F8FAFC]" dir="ltr">{{ $session->framedipaddress }}</td>
                                                <td class="p-2 text-[#94A3B8]">{{ $session->nasipaddress }}</td>
                                                <td class="p-2 text-[#94A3B8]">{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($session->acctstarttime))->format('Y/m/d H:i') }}</td>
                                                <td class="p-2 text-[#94A3B8]">{{ $session->acctstoptime ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($session->acctstoptime))->format('Y/m/d H:i') : '—' }}</td>
                                                <td class="p-2 text-[#94A3B8] font-mono">{{ $session->acctsessiontime ? gmdate('H:i:s', $session->acctsessiontime) : '—' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="py-12 text-center">
                                    <div class="w-16 h-16 rounded-full bg-[#202938] text-[#94A3B8] flex items-center justify-center mx-auto mb-3 text-2xl">📋</div>
                                    <h4 class="text-sm font-bold text-[#F8FAFC]">تاریخچه نشستی ثبت نشده است</h4>
                                    <p class="text-xs text-[#94A3B8] mt-1">هنوز هیچ نشستی برای این سرویس ثبت نشده است.</p>
                                </div>
                            @endif
                        @endif

                        {{-- Activities --}}
                        @if($activeTab === 'activities')
                            @if($activities->count() > 0)
                                <div class="relative space-y-4 before:absolute before:right-3 before:top-0 before:bottom-0 before:w-px before:bg-[#202938]">
                                    @foreach($activities as $act)
                                        <div class="flex gap-4 pr-8 relative">
                                            <div class="absolute right-0 top-1.5 w-2 h-2 rounded-full bg-[#6366F1] ring-4 ring-[#111722]"></div>
                                            <div class="flex-1 bg-[#080B12] border border-[#202938] rounded-xl p-3">
                                                <div class="flex items-start justify-between gap-2">
                                                    <span class="text-sm font-medium text-[#F8FAFC]">{{ $act->content }}</span>
                                                    <span class="text-[10px] text-[#94A3B8] font-mono whitespace-nowrap">
                                                        {{ \Morilog\Jalali\Jalalian::fromCarbon($act->created_at)->format('Y/m/d H:i') }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2 mt-1 text-xs text-[#94A3B8]">
                                                    <span>توسط:</span>
                                                    <span class="font-bold text-[#F8FAFC]">{{ $act->causer->name ?? 'سیستم' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-12 text-center">
                                    <div class="w-16 h-16 rounded-full bg-[#202938] text-[#94A3B8] flex items-center justify-center mx-auto mb-3 text-2xl">📭</div>
                                    <h4 class="text-sm font-bold text-[#F8FAFC]">رخدادی ثبت نشده است</h4>
                                    <p class="text-xs text-[#94A3B8] mt-1">هنوز هیچ تغییری در این سرویس ثبت نشده است.</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- 4.4 SERVICE INFORMATION --}}
            <div class="bg-[#111722] border border-[#202938] rounded-xl p-6">
                <h3 class="text-sm font-bold text-[#F8FAFC] mb-4">اطلاعات سرویس</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between py-2 border-b border-[#202938]/50">
                        <span class="text-[#94A3B8]">نام کاربری</span>
                        <span class="font-mono text-[#F8FAFC] flex items-center gap-2" dir="ltr">
                            {{ $account->username }}
                            <button onclick="navigator.clipboard.writeText('{{ $account->username }}')" class="text-[#94A3B8] hover:text-[#F8FAFC] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </button>
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#202938]/50">
                        <span class="text-[#94A3B8]">کلمه عبور</span>
                        <span class="font-mono text-[#F8FAFC] flex items-center gap-2" dir="ltr">
                            {{ $account->password }}
                            <button onclick="navigator.clipboard.writeText('{{ $account->password }}')" class="text-[#94A3B8] hover:text-[#F8FAFC] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </button>
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#202938]/50">
                        <span class="text-[#94A3B8]">نوع سرویس</span>
                        <span class="font-bold text-[#F8FAFC]">{{ strtoupper($account->service_group) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#202938]/50">
                        <span class="text-[#94A3B8]">تاریخ ایجاد</span>
                        <span class="text-[#F8FAFC]">{{ \Morilog\Jalali\Jalalian::fromCarbon($account->created_at)->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#202938]/50">
                        <span class="text-[#94A3B8]">تاریخ انقضا</span>
                        <span class="font-bold {{ $isExpired ? 'text-[#EF4444]' : ($daysRemaining <= 7 ? 'text-[#F59E0B]' : 'text-[#F8FAFC]') }}">
                            {{ $isFirstLogin ? '—' : $expireDateFormatted }}
                        </span>
                    </div>
                    @if($maxUsage > 0)
                        <div class="flex justify-between py-2 border-b border-[#202938]/50">
                            <span class="text-[#94A3B8]">محدودیت ترافیک</span>
                            <span class="font-mono text-[#F8FAFC]">{{ $maxUsageStr }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-[#202938]/50">
                            <span class="text-[#94A3B8]">ترافیک مصرفی</span>
                            <span class="font-mono text-[#F8FAFC]">{{ $usageStr }}</span>
                        </div>
                    @else
                        <div class="flex justify-between py-2 border-b border-[#202938]/50 col-span-2">
                            <span class="text-[#94A3B8]">محدودیت ترافیک</span>
                            <span class="font-bold text-[#10B981]">نامحدود</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 4.5 SUBSCRIPTION URL --}}
            <div class="bg-[#111722] border border-[#202938] rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-[#F8FAFC]">لینک اشتراک</h3>
                    <span class="text-[10px] text-[#94A3B8]">برای اتصال و دریافت تنظیمات سرویس</span>
                </div>
                <div class="flex items-center gap-2 bg-[#080B12] border border-[#202938] rounded-xl p-3">
                    <input type="text" value="{{ $account->subscription_url ?? '' }}" readonly class="flex-1 bg-transparent text-[#F8FAFC] text-xs font-mono outline-none" dir="ltr">
                    <button onclick="navigator.clipboard.writeText('{{ $account->subscription_url }}')" class="px-4 py-1.5 rounded-lg bg-[#6366F1] hover:bg-[#4F46E5] text-white text-xs font-bold transition">
                        کپی
                    </button>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- RIGHT COLUMN (1/3) - SIDEBAR              --}}
        {{-- ========================================= --}}
        <div class="space-y-6">

            {{-- Expiration Card --}}
            <div class="bg-[#111722] border border-[#202938] rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-[#F59E0B]/10 flex items-center justify-center text-[#F59E0B]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[#F8FAFC]">وضعیت زمانی سرویس</h4>
                        @if($isExpired)
                            <span class="text-sm font-bold text-[#EF4444]">منقضی شده</span>
                        @elseif($isFirstLogin)
                            <span class="text-sm font-bold text-[#3B82F6]">در انتظار اولین اتصال</span>
                        @else
                            <span class="text-2xl font-bold text-[#F8FAFC] font-mono">{{ $daysRemaining }}</span>
                            <span class="text-xs text-[#94A3B8]">روز باقیمانده</span>
                        @endif
                    </div>
                </div>
                <div class="text-xs text-[#94A3B8] font-mono" dir="ltr">
                    انقضا: {{ $isFirstLogin ? 'محاسبه بعد از اولین اتصال' : ($expireDateFormatted ?? 'نامشخص') }}
                </div>
            </div>

            {{-- Customer Information --}}
            @if($customer)
                <div class="bg-[#111722] border border-[#202938] rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-[#6366F1]/10 border border-[#6366F1]/20 flex items-center justify-center text-[#6366F1]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-[#F8FAFC]">اطلاعات مشتری</h4>
                            <p class="text-xs text-[#94A3B8]">متصل به این اکانت</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[#94A3B8]">نام</span>
                            <span class="font-bold text-[#F8FAFC]">{{ $customer->name ?? 'نامشخص' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#94A3B8]">موجودی</span>
                            <span class="font-bold text-[#10B981] font-mono">{{ number_format($customer->wallet ?? 0) }} تومان</span>
                        </div>
                        @if($customer->phone)
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">تماس</span>
                                <span class="font-mono text-[#F8FAFC]" dir="ltr">{{ $customer->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Creator Hierarchy --}}
            <div class="bg-[#111722] border border-[#202938] rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-[#8B5CF6]/10 border border-[#8B5CF6]/20 flex items-center justify-center text-[#8B5CF6]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[#F8FAFC]">زنجیره سازندگان</h4>
                        <p class="text-xs text-[#94A3B8]">مسیر سلسله‌مراتب ساخت اکانت</p>
                    </div>
                </div>

                @php
                    $currentCreator = \App\Models\User::find($account->creator);
                    $level = 1;
                @endphp

                @if($currentCreator)
                    <div class="relative space-y-3 pr-4 before:absolute before:right-1.5 before:top-2 before:bottom-2 before:w-px before:bg-[#202938]">
                        @while($currentCreator)
                            <div class="relative">
                                <div class="absolute -right-4 top-1.5 w-2.5 h-2.5 rounded-full bg-[#8B5CF6] ring-4 ring-[#111722]"></div>
                                <div class="bg-[#080B12] border border-[#202938] rounded-xl p-3 hover:border-[#8B5CF6]/30 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-[#F8FAFC]">{{ $currentCreator->name }}</span>
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-[#202938] text-[#94A3B8]">{{ $currentCreator->role ?? 'نامشخص' }}</span>
                                    </div>
                                    @if($currentCreator->phone)
                                        <div class="text-[10px] text-[#94A3B8] font-mono mt-0.5" dir="ltr">{{ $currentCreator->phone }}</div>
                                    @endif
                                </div>
                            </div>
                            @php
                                $currentCreator = $currentCreator->parentAgent ?? $currentCreator->creatorUser ?? null;
                                $level++;
                            @endphp
                        @endwhile
                    </div>
                @else
                    <div class="py-6 text-center text-xs text-[#94A3B8] bg-[#080B12] border border-[#202938] border-dashed rounded-xl">
                        این اکانت بالادستی ندارد.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 5. QR CODE MODAL                             --}}
    {{-- ============================================ --}}
    @if($showQrModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('showQrModal', false)"></div>
            <div class="relative w-full max-w-sm bg-[#111722] border border-[#202938] rounded-2xl p-6 text-center">
                <button wire:click="$set('showQrModal', false)" class="absolute top-4 left-4 p-2 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <h3 class="text-sm font-bold text-[#F8FAFC] mb-1">اسکن بارکد اتصال</h3>
                <p class="text-xs text-[#94A3B8] mb-4">با دوربین گوشی اسکن کنید</p>
                <div class="p-3 bg-white rounded-xl inline-block mx-auto">
                    <img src="{{ asset('configs/' . ($wgConfigs->first()->profile_name ?? '') . '.png') }}" alt="QR Code" class="w-48 h-48 object-contain">
                </div>
                <button wire:click="$set('showQrModal', false)" class="mt-4 w-full py-2.5 bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] font-bold text-xs rounded-xl transition">
                    بستن
                </button>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 6. RECHARGE MODAL                            --}}
    {{-- ============================================ --}}
    @if($isRechargeModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('isRechargeModalOpen', false)"></div>
            <div class="relative w-full max-w-md bg-[#111722] border border-[#202938] rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-5 border-b border-[#202938]">
                    <h2 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        شارژ و تمدید سرویس
                    </h2>
                    <button wire:click="$set('isRechargeModalOpen', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-5">
                    <div class="mb-4 p-3 rounded-xl bg-[#F59E0B]/10 border border-[#F59E0B]/20 text-[#F59E0B] text-xs font-bold leading-relaxed text-justify">
                        نماینده گرامی، پس از تایید، هزینه پلن انتخابی به صورت سیستمی از موجودی کیف پول شما کسر خواهد شد.
                    </div>

                    <form wire:submit.prevent="confirmRecharge" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-2">انتخاب پلن</label>
                            <select wire:model="selectedGroupId" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl p-3 outline-none focus:ring-1 focus:ring-[#6366F1]">
                                @foreach($availableGroups ?? [] as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }} ({{ number_format($group->getFinalPriceFor(auth()->user())) }} تومان)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-3 border-t border-[#202938]">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" wire:model="pay_from_user_wallet" class="mt-0.5 w-4 h-4 rounded bg-[#080B12] border-[#202938] text-[#6366F1] focus:ring-[#6366F1]/30 focus:ring-offset-0 cursor-pointer">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-[#F8FAFC] group-hover:text-[#6366F1] transition">کسر هزینه از کیف پول کاربر</span>
                                    <span class="text-[10px] text-[#94A3B8] mt-0.5">در صورت غیرفعال بودن، تمدید بدون کسر اعتبار از کیف پول کاربر انجام می‌شود.</span>
                                </div>
                            </label>
                        </div>

                        @error('wallet')
                        <div class="p-3 bg-[#EF4444]/10 border border-[#EF4444]/20 rounded-xl flex items-start gap-2">
                            <svg class="w-4 h-4 text-[#EF4444] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-[#EF4444] text-xs font-bold leading-relaxed">{{ $message }}</span>
                        </div>
                        @enderror

                        <div class="pt-3 flex justify-end gap-3 border-t border-[#202938]">
                            <button type="button" wire:click="$set('isRechargeModalOpen', false)" class="px-5 py-2.5 bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] font-bold text-xs rounded-xl transition">
                                انصراف
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-[#6366F1] hover:bg-[#4F46E5] text-white font-bold text-xs rounded-xl shadow-lg shadow-[#6366F1]/25 flex items-center gap-2 transition">
                                <svg wire:loading wire:target="confirmRecharge" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                <span wire:loading.remove wire:target="confirmRecharge">تأیید و تمدید</span>
                                <span wire:loading wire:target="confirmRecharge">در حال پردازش...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 7. EDIT MODAL                                --}}
    {{-- ============================================ --}}
    @if($isEditModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('isEditModalOpen', false)"></div>
            <div class="relative w-full max-w-lg bg-[#111722] border border-[#202938] rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-5 border-b border-[#202938]">
                    <h2 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#6366F1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        ویرایش اطلاعات اکانت
                    </h2>
                    <button wire:click="$set('isEditModalOpen', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-5">
                    <form wire:submit.prevent="save" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-[#94A3B8] mb-2">نام کاربری</label>
                                <input wire:model="username" type="text" dir="ltr" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl p-3 text-sm font-mono outline-none focus:ring-1 focus:ring-[#6366F1]">
                                @error('username') <span class="text-[#EF4444] text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#94A3B8] mb-2">کلمه عبور</label>
                                <input wire:model="password" type="password" dir="ltr" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl p-3 text-sm font-mono outline-none focus:ring-1 focus:ring-[#6366F1]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#94A3B8] mb-2">نام کامل</label>
                                <input wire:model="name" type="text" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl p-3 text-sm outline-none focus:ring-1 focus:ring-[#6366F1]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#94A3B8] mb-2">شماره تماس</label>
                                <input wire:model="phonenumber" type="text" dir="ltr" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl p-3 text-sm font-mono outline-none focus:ring-1 focus:ring-[#6366F1]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-2">سازنده اکانت</label>
                            <select wire:model="creator" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] rounded-xl p-3 text-sm outline-none focus:ring-1 focus:ring-[#6366F1]">
                                @foreach($creators ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role ?? 'نامشخص' }})</option>
                                @endforeach
                            </select>
                            @error('creator') <span class="text-[#EF4444] text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-3 flex justify-end gap-3 border-t border-[#202938]">
                            <button type="button" wire:click="$set('isEditModalOpen', false)" class="px-5 py-2.5 bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] font-bold text-xs rounded-xl transition">
                                انصراف
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-[#6366F1] hover:bg-[#4F46E5] text-white font-bold text-xs rounded-xl shadow-lg shadow-[#6366F1]/25 transition">
                                <span wire:loading.remove>ذخیره تغییرات</span>
                                <span wire:loading>در حال ذخیره...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 8. CHANGE SERVER MODAL                       --}}
    {{-- ============================================ --}}
    @if($isChangeServerModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('isChangeServerModalOpen', false)"></div>
            <div class="relative w-full max-w-md bg-[#111722] border border-[#202938] rounded-2xl shadow-xl">
                <div class="flex items-center justify-between p-5 border-b border-[#202938]">
                    <h2 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        انتقال به سرور جدید
                    </h2>
                    <button wire:click="$set('isChangeServerModalOpen', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-5">
                    <div class="p-3 rounded-xl bg-[#3B82F6]/10 border border-[#3B82F6]/20 text-[#3B82F6] text-xs leading-relaxed font-medium">
                        کانفیگ کاربر از سرور فعلی حذف شده و با کلید و آی‌پی جدید روی سرور مقصد ساخته می‌شود.
                    </div>

                    <form wire:submit.prevent="changeWgServer" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-2">انتخاب سرور مقصد</label>
                            <select wire:model="newWgServerId" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl p-3 outline-none focus:ring-1 focus:ring-[#3B82F6]">
                                <option value="">یک سرور انتخاب کنید...</option>
                                @foreach($allWgServers ?? [] as $srv)
                                    <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->ipaddress }})</option>
                                @endforeach
                            </select>
                            @error('newWgServerId') <span class="text-[#EF4444] text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-3 flex justify-end gap-3 border-t border-[#202938]">
                            <button type="button" wire:click="$set('isChangeServerModalOpen', false)" class="px-5 py-2.5 bg-[#202938] hover:bg-[#171E2B] text-[#F8FAFC] font-bold text-xs rounded-xl transition">
                                انصراف
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-[#3B82F6] hover:bg-[#2563EB] text-white font-bold text-xs rounded-xl shadow-lg shadow-[#3B82F6]/25 transition">
                                <span wire:loading.remove>انتقال کانفیگ</span>
                                <span wire:loading>در حال انتقال...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
