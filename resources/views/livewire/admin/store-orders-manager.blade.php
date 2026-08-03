<div class="space-y-6 pb-12 animate-fade-in">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">سفارشات فروشگاه</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">مدیریت فیش‌های واریزی، فروش مستقیم و فروش نمایندگان</p>
        </div>

        <div class="flex items-center gap-3">
            <select wire:model.live="statusFilter" class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-900 dark:text-white rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500/50 outline-none transition">
                <option value="">همه وضعیت‌ها</option>
                <option value="pending">⏳ در انتظار بررسی</option>
                <option value="approved">✅ تایید شده</option>
                <option value="rejected">❌ رد شده</option>
            </select>

            <div class="relative w-64">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="جستجوی نام یا شماره..." class="w-full bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-900 dark:text-white rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-orange-500/50 outline-none transition">
            </div>
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
                    <th class="p-4">مشتری</th>
                    <th class="p-4">فروشنده (نماینده/سایت)</th>
                    <th class="p-4">سرویس و مبلغ</th>
                    <th class="p-4">اکانت صادر شده</th>
                    <th class="p-4">وضعیت</th>
                    <th class="p-4 text-center">جزئیات و فیش</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-sm">
                @forelse($orders as $order)
                    @php
                        $createdAccount = $order->account ?? \App\Models\Accounts::where('creator', $order->account_id)->latest()->first();
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition">
                        <td class="p-4">
                            <span class="block font-bold text-zinc-900 dark:text-white font-mono-digit">#ORD-{{ $order->id }}</span>
                            <span class="text-[11px] text-zinc-500">{{ jdate($order->created_at)->format('Y/m/d H:i') }}</span>
                        </td>

                        <td class="p-4">
                            @if($order->user_id)
                                <a href="{{ route('admin.users.show', $order->user_id) }}" target="_blank" class="group block">
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
                            @if(is_null($order->agent_id))
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 text-[10px] font-black">
                                    🌟 فروش مستقیم (ادمین)
                                </span>
                            @else
                                <a href="{{ route('admin.managers.edit', $order->agent_id) }}" target="_blank" class="flex items-center gap-2 group">
                                    <div class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-600 dark:text-zinc-300 group-hover:bg-orange-500 group-hover:text-white transition">
                                        {{ mb_substr($order->agent->name ?? 'ن', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 group-hover:text-orange-500 transition flex items-center gap-1">
                                            {{ $order->agent->name ?? 'نماینده حذف شده' }}
                                            <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </span>
                                        <span class="block text-[10px] text-zinc-500">نماینده فروش</span>
                                    </div>
                                </a>
                            @endif
                        </td>

                        <td class="p-4">
                            <span class="block text-xs font-bold text-zinc-900 dark:text-white">{{ $order->group->name ?? 'سرویس نامشخص' }}</span>
                            <span class="text-[11px] font-black text-orange-500 font-mono-digit">{{ number_format($order->price) }} تومان</span>
                        </td>

                        <td class="p-4">
                            @if($order->status === 'approved' && $createdAccount)
                                <a href="{{ route('admin.accounts.show', $createdAccount->id) }}" target="_blank" class="group space-y-0.5 block">
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
                        <td colspan="7" class="p-12 text-center text-zinc-500 text-xs">هیچ سفارشی یافت نشد.</td>
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
                                <a href="{{ route('admin.users.show', $selectedOrder->user_id) }}" target="_blank" class="font-bold text-orange-500 hover:underline flex items-center gap-1">
                                    {{ $selectedOrder->user->name ?? 'ناشناس' }} ({{ $selectedOrder->phone }})
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            @else
                                <span class="font-bold text-zinc-900 dark:text-white">{{ $selectedOrder->user->name ?? 'ناشناس' }} ({{ $selectedOrder->phone }})</span>
                            @endif
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 text-xs">بسته درخواستی:</span>
                            <span class="font-bold text-zinc-900 dark:text-white">{{ $selectedOrder->group->name ?? 'نامشخص' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 text-xs">مبلغ سفارش:</span>
                            <span class="font-black text-orange-500 font-mono-digit">{{ number_format($selectedOrder->price) }} تومان</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-zinc-500 text-xs">فروشنده:</span>
                            @if($selectedOrder->agent_id)
                                <a href="{{ route('admin.managers.edit', $selectedOrder->agent_id) }}" target="_blank" class="font-bold text-orange-500 hover:underline flex items-center gap-1">
                                    {{ $selectedOrder->agent->name ?? 'نماینده' }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            @else
                                <span class="font-bold text-zinc-900 dark:text-zinc-300">ادمین (سایت اصلی)</span>
                            @endif
                        </div>
                    </div>

                    @if($selectedOrder->status === 'approved' && $modalAccount)
                        <div class="bg-purple-500/5 border border-purple-500/20 rounded-2xl p-4 space-y-3">
                            <div class="flex items-center justify-between border-b border-purple-500/10 pb-2">
                                <h4 class="text-xs font-black text-purple-600 dark:text-purple-400 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"></path></svg>
                                    مشخصات اکانت فعال شده
                                </h4>
                                <a href="{{ route('admin.accounts.show', $modalAccount->id) }}" target="_blank" class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-500/10 text-purple-500 hover:bg-purple-500 hover:text-white transition uppercase flex items-center gap-1">
                                    <span>مشاهده کامل</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="bg-white dark:bg-zinc-900 p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800">
                                    <span class="text-[10px] text-zinc-400 block">نام کاربری (Username):</span>
                                    <strong class="font-mono text-zinc-900 dark:text-white" dir="ltr">@ {{ $modalAccount->username }}</strong>
                                </div>
                                <div class="bg-white dark:bg-zinc-900 p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800">
                                    <span class="text-[10px] text-zinc-400 block">کلمه عبور (Password):</span>
                                    <strong class="font-mono text-zinc-900 dark:text-white" dir="ltr">{{ $modalAccount->password }}</strong>
                                </div>
                            </div>

                            @if($modalAccount->service_group === 'wireguard')
                                @php
                                    $wg = \App\Models\WireguardUsers::where('user_id', $modalAccount->id)->first();
                                @endphp
                                @if($wg)
                                    <div class="grid grid-cols-2 gap-2 pt-1">
                                        <a href="{{ asset('configs/' . $wg->profile_name . '.conf') }}" download class="py-2 px-3 bg-purple-600 text-white rounded-xl text-[11px] font-bold text-center hover:bg-purple-700 transition">
                                            📥 دانلود کانفیگ .conf
                                        </a>
                                        <a href="{{ asset('configs/' . $wg->profile_name . '.png') }}" target="_blank" class="py-2 px-3 bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 rounded-xl text-[11px] font-bold text-center hover:bg-zinc-300 transition">
                                            📷 مشاهده QR Code
                                        </a>
                                    </div>
                                @endif
                            @elseif($modalAccount->subscription_url)
                                <button onclick="navigator.clipboard.writeText('{{ $modalAccount->subscription_url }}'); alert('لینک کپی شد!');" class="w-full py-2 bg-zinc-900 dark:bg-white text-white dark:text-black rounded-xl text-[11px] font-bold transition">
                                    📋 کپی لینک اتصال (Subscription)
                                </button>
                            @endif
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-bold flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('receipt'))
                        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-sm font-bold flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            {{ session('receipt') }}
                        </div>
                    @endif

                    @if($selectedOrder->status === 'pending')
                        <div class="flex items-center gap-3 pt-2">
                            <button wire:click="rejectOrder({{ $selectedOrder->id }})"
                                    wire:confirm="آیا از رد این فیش مطمئن هستید؟"
                                    wire:loading.attr="disabled"
                                    wire:target="rejectOrder({{ $selectedOrder->id }})"
                                    class="flex-1 py-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-rose-500 font-bold text-sm hover:bg-rose-500 hover:text-white transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">

        <span wire:loading.remove wire:target="rejectOrder({{ $selectedOrder->id }})">
            رد فیش
        </span>

                                <span wire:loading wire:target="rejectOrder({{ $selectedOrder->id }})" class="flex items-center gap-2">
            <svg class="animate-spin w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>در حال رد...</span>
        </span>
                            </button>

                            <button wire:click="approveOrder({{ $selectedOrder->id }})"
                                    wire:confirm="فیش تایید شده و اکانت برای مشتری فعال می‌شود. مطمئن هستید؟"
                                    wire:loading.attr="disabled"
                                    wire:target="approveOrder({{ $selectedOrder->id }})"
                                    class="flex-1 py-3 rounded-xl bg-emerald-500 text-white font-black text-sm hover:bg-emerald-600 shadow-lg shadow-emerald-500/20 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">

        <span wire:loading.remove wire:target="approveOrder({{ $selectedOrder->id }})">
            تایید و فعال‌سازی
        </span>

                                <span wire:loading wire:target="approveOrder({{ $selectedOrder->id }})" class="flex items-center gap-2">
            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>در حال فعال‌سازی...</span>
        </span>
                            </button>
                        </div>
                    @else
                        <div class="p-3 rounded-xl text-center text-xs font-bold {{ $selectedOrder->status === 'approved' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                            این سفارش قبلاً {{ $selectedOrder->status === 'approved' ? 'تایید' : 'رد' }} شده است.
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endif
</div>
