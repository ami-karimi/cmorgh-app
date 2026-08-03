<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">لیست اکانت‌های VPN</h2>
            <p class="text-xs text-zinc-400 mt-1">مدیریت جامع اکانت‌ها، فیلترهای پیشرفته و نظارت بر نمایندگان زیرمجموعه</p>
        </div>
        <a href="{{ route('reseller.accounts.create') }}" wire:navigate class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            صدور اکانت جدید
        </a>
    </div>

    <div class="bg-[#09090b] border border-zinc-800/80 rounded-2xl p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="relative">
            <label class="block text-[10px] text-zinc-500 mb-1 font-bold">جستجوی اکانت</label>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Username..." class="w-full bg-zinc-900 border border-zinc-800 text-white text-xs rounded-xl py-2.5 pl-4 pr-10 focus:ring-1 focus:ring-orange-500 font-mono transition">
            <svg class="w-4 h-4 text-zinc-500 absolute right-3 top-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <div>
            <label class="block text-[10px] text-zinc-500 mb-1 font-bold">فیلتر تاریخ انقضا</label>
            <select wire:model.live="expireFilter" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs rounded-xl p-2.5 focus:ring-1 focus:ring-orange-500">
                <option value="all">همه زمان‌ها</option>
                <option value="expiring_5_days">⚠️ ۵ روز مانده به انقضا (نیازمند تمدید)</option>
                <option value="expired">❌ منقضی شده</option>
                <option value="expired_week_ago">🗑 بیشتر از یک هفته منقضی شده</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] text-zinc-500 mb-1 font-bold">وضعیت اتصال</label>
            <select wire:model.live="onlineFilter" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs rounded-xl p-2.5 focus:ring-1 focus:ring-orange-500">
                <option value="all">همه اتصالات</option>
                <option value="online">🟢 در حال حاضر آنلاین</option>
                <option value="offline">⚪ آفلاین</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] text-zinc-500 mb-1 font-bold">وضعیت سیستم</label>
            <select wire:model.live="statusFilter" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs rounded-xl p-2.5 focus:ring-1 focus:ring-orange-500">
                <option value="all">همه وضعیت‌ها</option>
                <option value="active">فعال و مجاز</option>
                <option value="disabled">مسدود (غیرفعال)</option>
            </select>
        </div>
    </div>

    <div class="bg-[#09090b] border border-zinc-800/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="text-zinc-500 bg-zinc-900/50 border-b border-zinc-800/80">
                <tr>
                    <th class="p-4 font-bold">نام کاربری / وضعیت</th>
                    <th class="p-4 font-bold">بالادستی (سازنده)</th>
                    <th class="p-4 font-bold">تعرفه</th>
                    <th class="p-4 font-bold">مصرف / حجم کل</th>
                    <th class="p-4 font-bold">تاریخ انقضا</th>
                    <th class="p-4 font-bold text-center">وضعیت</th>
                    <th class="p-4 font-bold text-left">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                @forelse($accounts as $acc)
                    <tr class="hover:bg-zinc-900/40 transition">

                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <div class="relative flex h-2.5 w-2.5">
                                    @if($acc->is_online)
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                    @else
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-zinc-600"></span>
                                    @endif
                                </div>
                                <span class="font-mono text-sm font-bold text-zinc-100">{{ $acc->username }}</span>
                            </div>
                            <div class="text-[9px] text-zinc-500 uppercase mt-0.5 font-mono mr-4">
                                @if($acc->service_group === 'wireguard')
                                    <span class="text-purple-400">WireGuard</span>
                                @else
                                    <span class="text-orange-400">{{ $acc->service_group }}</span>
                                @endif
                            </div>
                        </td>

                        <td class="p-4">
                            @if($acc->panelUser)
                                <a href="{{ route('reseller.users.show', ['id' => $acc->panelUser->id]) }}"
                                   class="text-[11px] font-bold text-blue-400 hover:text-blue-300 hover:underline flex items-center gap-1 transition-colors">
                                    👤 {{ $acc->panelUser->name ?? $acc->panelUser->username }}
                                    <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            @else
                                <span class="text-[11px] font-bold text-zinc-500">
            👤 بدون کاربر / سیستم
        </span>
                            @endif

                            <div class="text-[9px] text-zinc-500 mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3 text-orange-500/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>سازنده: <strong class="text-zinc-400 font-normal">{{ $acc->panelUser->parentAgent->name ?? 'خودم / سیستم' }}</strong></span>
                            </div>
                        </td>
                        <td class="p-4">
                                <span class="text-[10px] font-bold bg-zinc-800/50 px-2 py-1 rounded text-zinc-300 border border-zinc-700/50">
                                    {{ $acc->group->name ?? 'نامشخص' }}
                                </span>
                        </td>

                        <td class="p-4 font-mono text-[10px]">
                            @php
                                $downloadGb = round(($acc->download_usage ?? 0) / 1073741824, 2);
                                $maxGb = round(($acc->max_usage ?? 0) / 1073741824, 2);
                            @endphp

                            @if($maxGb <= 0)
                                <span class="text-emerald-400">{{ $downloadGb }} GB</span> <span class="text-zinc-500">از</span>
                                <span class="bg-emerald-500/10 text-emerald-400 px-1.5 py-0.5 rounded text-[9px] font-sans font-bold">نامحدود ∞</span>
                            @else
                                <span class="text-emerald-400">{{ $downloadGb }} GB</span> <span class="text-zinc-500">از</span> <span class="text-zinc-300">{{ $maxGb }} GB</span>
                            @endif
                        </td>

                        <td class="p-4">
                            @if($acc->expire_date)
                                @php
                                    $expireDate = \Carbon\Carbon::parse($acc->expire_date);
                                    $daysLeft = now()->diffInDays($expireDate, false);
                                @endphp

                                @if($expireDate->isPast())
                                    <span class="text-red-400 font-mono text-[11px] bg-red-500/10 px-2 py-1 rounded border border-red-500/20">منقضی: {{ jdate($acc->expire_date)->format('Y/m/d') }}</span>
                                @elseif($daysLeft <= 5)
                                    <span class="text-amber-400 font-mono text-[11px] bg-amber-500/10 px-2 py-1 rounded border border-amber-500/20">انقضا: {{ jdate($acc->expire_date)->format('Y/m/d') }}</span>
                                @else
                                    <span class="text-zinc-300 font-mono text-[11px]">{{ jdate($acc->expire_date)->format('Y/m/d H:i') }}</span>
                                @endif
                            @else
                                <span class="text-zinc-500 text-[10px]">در انتظار اولین اتصال</span>
                            @endif
                        </td>

                        <td class="p-4 text-center">
                            <button wire:click="toggleStatus({{ $acc->id }})" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $acc->is_enabled ? 'bg-emerald-500' : 'bg-zinc-700' }}">
                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $acc->is_enabled ? 'translate-x-1' : 'translate-x-4' }}"></span>
                            </button>
                        </td>

                        <td class="p-4 text-left">
                            <a href="{{ route('reseller.accounts.show', $acc->id) }}" wire:navigate class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-800 text-zinc-400 hover:text-white hover:bg-orange-500 transition shadow-sm" title="مشاهده جزئیات">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-zinc-500 text-xs">هیچ اکانتی با این فیلترها یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($accounts->hasPages())
            <div class="p-4 border-t border-zinc-800/80 bg-zinc-900/30">
                {{ $accounts->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>
</div>
