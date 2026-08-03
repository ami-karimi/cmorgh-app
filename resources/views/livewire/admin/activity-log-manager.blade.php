<div wire:key="activity-log-wrapper">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide flex items-center gap-3">
                گزارش سیستم و رخدادها
                @if($unreadCount > 0)
                <span class="px-2 py-0.5 bg-orange-500/20 text-orange-400 border border-orange-500/30 rounded-lg text-xs font-bold animate-pulse">
                        {{ $unreadCount }} رخداد جدید
                    </span>
                @endif
            </h1>
            <p class="text-xs text-zinc-500 mt-1">رهگیری کامل فعالیت‌های نمایندگان و تغییرات اعمال شده روی کاربران</p>
        </div>

        @if($unreadCount > 0)
        <button wire:click="markAllAsRead" onclick="confirm('آیا همه رخدادها خوانده شوند؟') || event.stopImmediatePropagation()" class="px-5 py-3 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-white font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            تیک‌خوردن تمام رخدادهای جدید
        </button>
        @endif
    </div>

    @if (session()->has('message'))
    <div class="p-4 mb-6 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">{{ session('message') }}</div>
    @endif

    <div class="mb-6 bg-zinc-900/40 border border-zinc-800/80 p-4 rounded-3xl shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="col-span-1 sm:col-span-2 relative">
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white text-xs rounded-xl pr-10 p-3.5 focus:ring-1 focus:ring-orange-500" placeholder="جستجو در متن رخداد، نماینده، یوزرنیم...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <select wire:model.live="filterStatus" class="bg-zinc-950 border border-zinc-800 text-zinc-300 text-xs rounded-xl p-3.5 focus:ring-1 focus:ring-orange-500">
                <option value="">همه رخدادها</option>
                <option value="unread">فقط رخدادهای جدید 🔴</option>
                <option value="read">خوانده شده‌ها 🟢</option>
            </select>

            <div x-data x-init="
                $($refs.dateFrom).persianDatepicker({
                    format: 'YYYY/MM/DD',
                    initialValue: false,
                    autoClose: true,
                    persianDigit: false,
                    cssClass: 'persian-datepicker-cheetah',
                    onSelect: function(unix){ $wire.set('filterDateFrom', $refs.dateFrom.value, true); }
                });
            ">
                <input x-ref="dateFrom" wire:model="filterDateFrom" type="text" readonly placeholder="از تاریخ..." class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-xs rounded-xl p-3.5 focus:ring-1 focus:ring-orange-500 font-mono text-center cursor-pointer">
            </div>

            <div x-data x-init="
                $($refs.dateTo).persianDatepicker({
                    format: 'YYYY/MM/DD',
                    initialValue: false,
                    autoClose: true,
                    persianDigit: false,
                    cssClass: 'persian-datepicker-cheetah',
                    onSelect: function(unix){ $wire.set('filterDateTo', $refs.dateTo.value, true); }
                });
            ">
                <input x-ref="dateTo" wire:model="filterDateTo" type="text" readonly placeholder="تا تاریخ..." class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-xs rounded-xl p-3.5 focus:ring-1 focus:ring-orange-500 font-mono text-center cursor-pointer">
            </div>

            <select wire:model.live="perPage" class="bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs rounded-xl p-3.5 focus:ring-1 focus:ring-orange-500 font-mono">
                <option value="25">25 ردیف</option>
                <option value="50">50 ردیف</option>
                <option value="100">100 ردیف</option>
            </select>
        </div>
    </div>

    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-zinc-950/80 text-zinc-400 font-bold border-b border-zinc-800/80">
                <tr>
                    <th class="p-4 pl-2">جزئیات رخداد (متن سیستم)</th>
                    <th class="p-4 w-48">کاربر اعمال‌کننده (مدیر/نماینده)</th>
                    <th class="p-4 w-48">اکانت هدف</th>
                    <th class="p-4 w-40">تاریخ و زمان</th>
                    <th class="p-4 w-24 text-center">وضعیت</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                @forelse($logs as $log)
                <tr wire:key="log-{{ $log->id }}" class="transition-colors hover:bg-zinc-800/40 {{ !$log->admin_view ? 'bg-orange-500/5 border-r-4 border-r-orange-500' : 'border-r-4 border-r-transparent' }}">

                    <td class="p-4 pl-2">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 {{ !$log->admin_view ? 'text-orange-500' : 'text-zinc-500' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-sm font-medium {{ !$log->admin_view ? 'text-white' : 'text-zinc-300' }} leading-relaxed">
                                        {{ $log->content }}
                                    </span>
                        </div>
                    </td>

                    <td class="p-4">
                        @if($log->causer)
                        <div class="font-bold text-white">{{ $log->causer->name }}</div>
                        <div class="text-[10px] text-zinc-500 font-mono mt-0.5">ID: {{ $log->by }}</div>
                        @else
                        <div class="font-bold text-zinc-500">سیستم / ناشناس</div>
                        <div class="text-[10px] text-zinc-600 font-mono mt-0.5">ID: {{ $log->by }}</div>
                        @endif
                    </td>

                    <td class="p-4">
                        @if($log->account)
                        <div class="font-bold text-blue-400" dir="ltr">{{ $log->account->username }}</div>
                        @else
                        <div class="font-bold text-zinc-500">حذف شده / نامشخص</div>
                        @endif
                        <div class="text-[10px] text-zinc-600 font-mono mt-0.5">Acc_ID: {{ $log->user_id }}</div>
                    </td>

                    <td class="p-4">
                        <div class="font-mono text-zinc-300" dir="ltr">
                            {{ \Morilog\Jalali\Jalalian::forge($log->created_at)->format('Y/m/d') }}
                        </div>
                        <div class="font-mono text-[10px] text-zinc-500 mt-0.5" dir="ltr">
                            {{ \Morilog\Jalali\Jalalian::forge($log->created_at)->format('H:i:s') }}
                        </div>
                    </td>

                    <td class="p-4 text-center">
                        @if(!$log->admin_view)
                        <button wire:click="markAsRead({{ $log->id }})" title="علامت‌گذاری به عنوان خوانده شده" class="px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-[10px] font-bold transition shadow-md whitespace-nowrap">
                            تایید
                        </button>
                        @else
                        <span class="inline-flex items-center justify-center text-zinc-600" title="خوانده شده">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                        @endif
                    </td>

                </tr>
                @empty
                <tr><td colspan="5" class="p-12 text-center text-zinc-500 font-bold">هیچ رخدادی یافت نشد.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-zinc-950/40 border-t border-zinc-800/60">
            {{ $logs->links() }}
        </div>
    </div>

</div>
