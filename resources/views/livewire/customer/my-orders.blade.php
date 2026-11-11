<div class="space-y-6 animate-fade-in">
    @if($pendingOrder) @include('livewire.store.bank-order-status') @endif

    <div>
        <h1 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">سفارشات من</h1>
        <p class="text-sm text-zinc-500 mt-1">تاریخچه خریدهای شما از فروشگاه و پیگیری وضعیت فیش‌های واریزی</p>
    </div>

    <div class="bg-white dark:bg-[#111c2e] border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-[10px] font-black uppercase tracking-wider">
                    <th class="p-4">شماره پیگیری / تاریخ</th>
                    <th class="p-4">سرویس خریداری شده</th>
                    <th class="p-4">مبلغ پرداختی</th>
                    <th class="p-4 text-center">وضعیت سفارش</th>
                    <th class="p-4 text-center">مشاهده فیش</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-sm">
                @forelse($orders as $order)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition">

                        <td class="p-4">
                            <span class="block font-bold text-zinc-900 dark:text-white font-mono-digit">#ORD-{{ $order->id }}</span>
                            <span class="text-[11px] text-zinc-500 font-mono-digit">{{ jdate($order->created_at)->format('Y/m/d H:i') }}</span>
                        </td>

                        <td class="p-4">
                            <span class="block text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $order->group->name ?? 'سرویس نامشخص' }}</span>
                        </td>

                        <td class="p-4">
                                <span class="text-xs font-black text-brand-500 dark:text-brand-400 font-mono-digit">
                                    {{ number_format($order->price) }} تومان
                                </span>
                        </td>

                        <td class="p-4 text-center">
                            <x-ui.order-status :order="$order" />
                        </td>

                        <td class="p-4 text-center">
                            <button wire:click="showPayment({{ $order->id }})" class="p-2 text-blue-400">جزئیات پرداخت</button>
                            @if($order->payment_method === 'receipt' && $order->receipt_image)
                                <button wire:click="viewReceipt({{ $order->id }})" class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:text-brand-500 hover:bg-brand-500/10 transition shadow-sm" title="مشاهده فیش واریزی">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </button>
                            @else
                                <span class="text-[10px] text-zinc-400">بدون فیش</span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-zinc-500 text-xs">تا کنون هیچ سفارشی ثبت نکرده‌اید.</td>
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

    @if($isReceiptModalOpen && $selectedReceipt)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in overflow-y-auto">
            <div class="w-full max-w-sm bg-white dark:bg-[#111c2e] border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden my-8">

                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-900/50">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">تصویر فیش واریزی</h3>
                    <button wire:click="$set('isReceiptModalOpen', false)" class="text-zinc-500 hover:text-rose-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-4 bg-zinc-100 dark:bg-[#080f1d] flex items-center justify-center min-h-[300px]">
                    <a href="{{ Storage::disk('public')->url($selectedReceipt) }}" target="_blank" title="برای نمایش در اندازه اصلی کلیک کنید">
                        <img src="{{ Storage::disk('public')->url($selectedReceipt) }}" alt="رسید پرداخت" class="max-h-[400px] rounded-xl shadow-md border border-zinc-300 dark:border-zinc-700 object-contain hover:scale-[1.02] transition duration-300">
                    </a>
                </div>

            </div>
        </div>
    @endif

</div>
