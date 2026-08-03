<div class="space-y-6 pb-12 animate-fade-in font-sans">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">سفارشات مشتریان من</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">مدیریت فیش‌های واریزی و درخواست‌های خرید مشتریان شما</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl p-5 text-white shadow-lg shadow-blue-500/20 relative overflow-hidden">
            <svg class="absolute -left-4 -bottom-4 w-24 h-24 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            <div class="relative z-10">
                <span class="text-blue-100 text-xs font-bold mb-1 block">موجودی کیف پول شما</span>
                <h3 class="text-2xl font-black font-mono-digit truncate">{{ number_format($stats['walletBalance']) }} <span class="text-sm font-normal">تومان</span></h3>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl p-5 text-white shadow-lg shadow-emerald-500/20 relative overflow-hidden">
            <svg class="absolute -left-4 -bottom-4 w-24 h-24 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            <div class="relative z-10">
                <span class="text-emerald-100 text-xs font-bold mb-1 block">فروش امروز شما</span>
                <h3 class="text-2xl font-black font-mono-digit truncate">{{ number_format($stats['todaySales']) }} <span class="text-sm font-normal">تومان</span></h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-purple-500/10 text-purple-500 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-zinc-500 dark:text-zinc-400 text-xs font-bold">کل گردش مالی (فروش)</span>
            </div>
            <h3 class="text-xl font-black text-zinc-900 dark:text-white font-mono-digit truncate">{{ number_format($stats['totalSales']) }} <span class="text-xs font-normal text-zinc-500">تومان</span></h3>
        </div>

        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-zinc-500 dark:text-zinc-400 text-xs font-bold">در انتظار بررسی</span>
            </div>
            <h3 class="text-xl font-black {{ $stats['pendingOrders'] > 0 ? 'text-amber-500' : 'text-zinc-900 dark:text-white' }} font-mono-digit">
                {{ $stats['pendingOrders'] }} <span class="text-xs font-normal text-zinc-500">فیش واریزی</span>
            </h3>
        </div>
    </div>
    <div class="flex flex-col md:flex-row md:items-center justify-end gap-3 bg-zinc-50 dark:bg-zinc-900/50 p-3 rounded-2xl border border-zinc-200 dark:border-zinc-800">
        <select wire:model.live="statusFilter" class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-700 text-sm text-zinc-900 dark:text-white rounded-xl px-4 py-2 focus:ring-2 focus:ring-orange-500/50 outline-none transition">
            <option value="">همه وضعیت‌ها</option>
            <option value="pending">⏳ در انتظار بررسی</option>
            <option value="approved">✅ تایید شده</option>
            <option value="rejected">❌ رد شده</option>
        </select>

        <div class="relative w-full md:w-64">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="جستجوی مشتری یا شماره..." class="w-full bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-700 text-sm text-zinc-900 dark:text-white rounded-xl py-2 px-4 focus:ring-2 focus:ring-orange-500/50 outline-none transition">
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-[10px] font-black uppercase tracking-wider">
                    <th class="p-4">کد / تاریخ</th>
                    <th class="p-4">مشتری شما</th>
                    <th class="p-4">سرویس و مبلغ</th>
                    <th class="p-4">اکانت صادر شده</th>
                    <th class="p-4">وضعیت</th>
                    <th class="p-4 text-center">جزئیات و فیش</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-sm">
                @forelse($orders as $order)
                    @php
                        $createdAccount = $order->account ?? \App\Models\Accounts::where('creator', $order->user_id)->latest()->first();
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition">
                        <td class="p-4">
                            <span class="block font-bold text-zinc-900 dark:text-white font-mono-digit">#ORD-{{ $order->id }}</span>
                            <span class="text-[11px] text-zinc-500">{{ jdate($order->created_at)->format('Y/m/d H:i') }}</span>
                        </td>

                        <td class="p-4">
                            @if($order->user_id)
                                <a href="{{ route('reseller.users.show', $order->user_id) }}" target="_blank" class="group block">
                                    <span class="font-bold text-zinc-900 dark:text-zinc-200 group-hover:text-orange-500 transition flex items-center gap-1">
                                        {{ $order->user->name ?? 'مشتری ناشناس' }}
                                        <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </span>
                                </a>
                            @else
                                <span class="block font-bold text-zinc-900 dark:text-zinc-200">{{ $order->user->name ?? 'مشتری ناشناس' }}</span>
                            @endif
                            <span class="text-[11px] text-zinc-500 font-mono-digit" dir="ltr">{{ $order->phone }}</span>
                        </td>

                        <td class="p-4">
                            <span class="block text-xs font-bold text-zinc-900 dark:text-white">{{ $order->group->name ?? 'سرویس نامشخص' }}</span>
                            <span class="text-[11px] font-black text-orange-500 font-mono-digit">{{ number_format($order->price) }} تومان</span>
                        </td>

                        <td class="p-4">
                            @if($order->status === 'approved' && $createdAccount)
                                <a href="{{ route('reseller.accounts.show', $createdAccount->id) }}" target="_blank" class="group space-y-0.5 block">
                                    <span class="font-mono text-xs font-bold text-purple-600 dark:text-purple-400 group-hover:underline flex items-center gap-1" dir="ltr">
                                        @ {{ $createdAccount->username }}
                                        <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </span>
                                    <span class="inline-block text-[9px] font-bold px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 uppercase">
                                        {{ $createdAccount->service_group }}
                                    </span>
                                </a>
                            @elseif($order->status === 'approved')
                                <span class="text-[11px] text-amber-500 font-bold">اکانت یافت نشد</span>
                            @else
                                <span class="text-[11px] text-zinc-400">—</span>
                            @endif
                        </td>

                        <td class="p-4">
                            @if($order->status === 'pending')
                                <span class="inline-flex px-2 py-1 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold animate-pulse">
                                    در انتظار بررسی
                                </span>
                            @elseif($order->status === 'approved')
                                <span class="inline-flex px-2 py-1 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">
                                    تایید شده
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded-md bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-bold">
                                    رد شده
                                </span>
                            @endif
                        </td>

                        <td class="p-4 text-center">
                            <button wire:click="viewReceipt({{ $order->id }})" class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:text-orange-500 hover:bg-orange-500/10 transition shadow-sm" title="مشاهده فیش و بررسی">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-zinc-500 text-xs">هیچ سفارشی یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    @if($isReceiptModalOpen && $selectedOrder)
        @php
            $modalAccount = $selectedOrder->account ?? \App\Models\Accounts::where('creator', $selectedOrder->user_id)->latest()->first();
        @endphp

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in overflow-y-auto">
            <div class="w-full max-w-lg bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden my-8 flex flex-col max-h-[90vh]">

                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-900/50 shrink-0">
                    <h3 class="text-base font-black text-zinc-900 dark:text-white">بررسی فیش و جزئیات سفارش</h3>
                    <button wire:click="$set('isReceiptModalOpen', false)" class="text-zinc-500 hover:text-rose-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-5">

                    <div class="p-4 bg-zinc-100 dark:bg-[#09090b] rounded-2xl flex items-center justify-center border border-zinc-200 dark:border-zinc-800">
                        @if($selectedOrder->receipt_image && $selectedOrder->receipt_image !== 'wallet_payment')
                            <a href="{{ asset('storage/' . $selectedOrder->receipt_image) }}" target="_blank">
                                <img src="{{ asset('storage/' . $selectedOrder->receipt_image) }}" class="max-h-64 rounded-xl shadow-md border border-zinc-300 dark:border-zinc-800 object-contain hover:scale-105 transition duration-300">
                            </a>
                        @elseif($selectedOrder->receipt_image === 'wallet_payment')
                            <div class="text-center py-4 text-emerald-500 font-bold text-xs flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <span>پرداخت مستقیم از کیف پول الکترونیکی</span>
                            </div>
                        @else
                            <span class="text-zinc-500 text-xs">تصویر فیش موجود نیست</span>
                        @endif
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 text-sm space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-zinc-500 text-xs">مشتری:</span>
                            @if($selectedOrder->user_id)
                                <a href="{{ route('reseller.users.show', $selectedOrder->user_id) }}" target="_blank" class="font-bold text-orange-500 hover:underline flex items-center gap-1">
                                    {{ $selectedOrder->user->name ?? 'ناشناس' }} ({{ $selectedOrder->phone }})
                                </a>
                            @endif
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 text-xs">بسته درخواستی:</span>
                            <span class="font-bold text-zinc-900 dark:text-white">{{ $selectedOrder->group->name ?? 'نامشخص' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 text-xs">مبلغ پرداخت شده:</span>
                            <span class="font-black text-orange-500 font-mono-digit">{{ number_format($selectedOrder->price) }} تومان</span>
                        </div>
                    </div>

                    @if($selectedOrder->status === 'approved' && $modalAccount)
                        <div class="bg-purple-500/5 border border-purple-500/20 rounded-2xl p-4 space-y-3">
                            <div class="flex items-center justify-between border-b border-purple-500/10 pb-2">
                                <h4 class="text-xs font-black text-purple-600 dark:text-purple-400">مشخصات اکانت فعال شده</h4>
                                <a href="{{ route('reseller.accounts.show', $modalAccount->id) }}" target="_blank" class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-500/10 text-purple-500 hover:bg-purple-500 hover:text-white transition uppercase">
                                    مشاهده کامل
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="bg-white dark:bg-zinc-900 p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800">
                                    <span class="text-[10px] text-zinc-400 block">نام کاربری:</span>
                                    <strong class="font-mono text-zinc-900 dark:text-white" dir="ltr">@ {{ $modalAccount->username }}</strong>
                                </div>
                                <div class="bg-white dark:bg-zinc-900 p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800">
                                    <span class="text-[10px] text-zinc-400 block">کلمه عبور:</span>
                                    <strong class="font-mono text-zinc-900 dark:text-white" dir="ltr">{{ $modalAccount->password }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($selectedOrder->status === 'pending')
                        <div class="flex items-center gap-3 pt-2">
                            <button wire:click="rejectOrder({{ $selectedOrder->id }})" wire:confirm="آیا از رد این فیش مطمئن هستید؟" class="flex-1 py-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-rose-500 font-bold text-sm hover:bg-rose-500 hover:text-white transition">
                                رد فیش
                            </button>
                            <button wire:click="approveOrder({{ $selectedOrder->id }})" wire:confirm="فیش تایید شده و هزینه عمده از کیف پول شما کسر می‌گردد. مطمئن هستید؟" class="flex-1 py-3 rounded-xl bg-emerald-500 text-white font-black text-sm hover:bg-emerald-600 shadow-lg shadow-emerald-500/20 transition">
                                تایید و فعال‌سازی
                            </button>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endif
</div>
