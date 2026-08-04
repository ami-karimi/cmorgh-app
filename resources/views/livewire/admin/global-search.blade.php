<div class="relative w-full md:w-64 lg:w-80 group z-50" x-data="{ open: true }" @click.outside="open = false">

    <input wire:model.live.debounce.300ms="search"
           @focus="open = true"
           @keydown.escape="open = false"
           type="text"
           placeholder="جستجو در کاربران، اکانت‌ها و..."
           class="w-full bg-zinc-100 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-900 dark:text-white rounded-xl py-2 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-orange-500/50 transition-all font-mono-digit placeholder:font-sans shadow-inner">

    <div class="absolute right-0 top-0 h-full w-10 flex items-center justify-center text-zinc-400">
        <svg wire:loading.remove wire:target="search" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <svg wire:loading wire:target="search" class="w-4 h-4 animate-spin text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
    </div>

    @if(strlen(trim($search)) >= 2)
        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute top-full mt-2 w-full md:w-[28rem] lg:w-[32rem] right-0 bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden">

            @if($users->isEmpty() && $resellers->isEmpty() && $accounts->isEmpty())
                <div class="p-6 text-center text-xs text-zinc-500 dark:text-zinc-400">
                    نتیجه‌ای برای "<span class="font-bold text-zinc-700 dark:text-zinc-300">{{ $search }}</span>" یافت نشد.
                </div>
            @else
                <div class="max-h-[70vh] overflow-y-auto custom-scrollbar">

                    @if($resellers->count() > 0)
                        <div class="bg-zinc-50 dark:bg-zinc-900/80 px-3 py-1.5 text-[10px] font-black text-orange-500 uppercase tracking-wider border-b border-zinc-100 dark:border-zinc-800/50 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V11m0 0h5m-5 0h-5"></path></svg>
                            نمایندگان سیستم
                        </div>
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                            @foreach($resellers as $reseller)
                                <a href="{{ route('admin.managers.edit', $reseller->id) }}" wire:navigate class="flex items-center justify-between p-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition group">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-orange-500/10 text-orange-500 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ mb_substr($reseller->name ?? 'N', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 group-hover:text-orange-500 transition truncate">{{ $reseller->name }}</p>
                                            <p class="text-[10px] text-zinc-500 font-mono-digit mt-0.5 truncate">{{ $reseller->phone ?? $reseller->email }}</p>
                                        </div>
                                    </div>
                                    <span class="shrink-0 text-[9px] px-2 py-0.5 rounded font-bold bg-orange-500/10 text-orange-600 dark:text-orange-400">
                                        {{ $reseller->role === 'sub_agent' ? 'زیرنماینده' : 'نماینده' }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($users->count() > 0)
                        <div class="bg-zinc-50 dark:bg-zinc-900/80 px-3 py-1.5 text-[10px] font-black text-blue-500 uppercase tracking-wider border-b border-zinc-100 dark:border-zinc-800/50 flex items-center gap-1.5 border-t {{ $resellers->count() > 0 ? 'border-zinc-200 dark:border-zinc-800' : '' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            کاربران (مشتریان)
                        </div>
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                            @foreach($users as $user)
                                <a href="{{ route('admin.users.show', $user->id) }}" wire:navigate class="flex items-center gap-3 p-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                    <div class="w-8 h-8 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ mb_substr($user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ $user->name ?: $user->username }}</p>
                                        <p class="text-[10px] text-zinc-500 font-mono-digit mt-0.5 truncate">{{ $user->phone ?? $user->email }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($accounts->count() > 0)
                        <div class="bg-zinc-50 dark:bg-zinc-900/80 px-3 py-1.5 text-[10px] font-black text-emerald-500 uppercase tracking-wider border-b border-zinc-100 dark:border-zinc-800/50 flex items-center gap-1.5 border-t {{ ($resellers->count() > 0 || $users->count() > 0) ? 'border-zinc-200 dark:border-zinc-800' : '' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"></path></svg>
                            اکانت‌های سرویس
                        </div>
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                            @foreach($accounts as $acc)
                                @php
                                    // محاسبه حجم (GB)
                                    $maxGb = $acc->max_usage > 0 ? round($acc->max_usage / 1073741824, 2) : 0;
                                    $usedGb = round(($acc->download_usage ?? 0) / 1073741824, 2);

                                    // منطق هوشمند محاسبه تاریخ انقضا
                                    $expireText = '';
                                    $expireClass = '';

                                    if (!$acc->expire_date) {
                                        $expireText = 'اولین اتصال';
                                        $expireClass = 'text-zinc-500 bg-zinc-100 dark:text-zinc-400 dark:bg-zinc-800';
                                    } else {
                                        $expireDate = \Carbon\Carbon::parse($acc->expire_date);
                                        if ($expireDate->isPast()) {
                                            $expireText = 'منقضی شده';
                                            $expireClass = 'text-rose-500 bg-rose-500/10';
                                        } else {
                                            $daysLeft = now()->diffInDays($expireDate, false);
                                            $expireText = $daysLeft > 0 ? $daysLeft . ' روز مانده' : 'امروز منقضی می‌شود';
                                            $expireClass = $daysLeft <= 3 ? 'text-amber-500 bg-amber-500/10' : 'text-emerald-500 bg-emerald-500/10';
                                        }
                                    }
                                @endphp

                                <a href="{{ route('admin.accounts.show', $acc->id) }}" wire:navigate class="flex items-start md:items-center gap-3 p-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0 mt-1 md:mt-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-xs font-bold font-mono text-zinc-800 dark:text-zinc-200 truncate dir-ltr text-right">{{ $acc->username }}</p>
                                            <p class="text-[9px] text-zinc-400 truncate hidden md:block">ساخته شده توسط: {{ $acc->panelUser->name ?? '-' }}</p>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-1.5 mt-2 md:mt-1.5">
                                            <span class="text-[9px] text-zinc-500 uppercase">{{ $acc->service_group }}</span>

                                            <span class="text-zinc-300 dark:text-zinc-700">|</span>

                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $expireClass }}">
                                                {{ $expireText }}
                                            </span>

                                            @if($maxGb > 0)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold font-mono text-blue-600 dark:text-blue-400 bg-blue-500/10" dir="ltr">
                                                    {{ $usedGb }} / {{ $maxGb }} GB
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10">
                                                    نامحدود ∞
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                </div>
            @endif
        </div>
    @endif
</div>
