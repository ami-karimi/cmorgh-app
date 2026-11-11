<div class="fixed inset-0 z-40 flex justify-end" x-show="drawerOpen" x-cloak>
    <button class="fixed inset-0 bg-[#080f1d]/60" wire:click="closeDrawer" aria-label="بستن جزئیات"></button>
    <div role="dialog" aria-modal="true" aria-label="جزئیات پرداخت" class="relative w-full max-w-2xl bg-[#111c2e] border-r border-[#243247] shadow-2xl overflow-y-auto">
        <div class="sticky top-0 bg-[#111c2e] border-b border-[#243247] p-4 flex justify-between">
            <h3 class="text-sm font-bold">{{ $selectedRequest ? 'درخواست #'.$selectedRequest->id : ($selectedMessage ? 'پیام بانکی #'.$selectedMessage->id : 'رکورد یافت نشد') }}</h3>
            <button wire:click="closeDrawer" class="p-2 rounded-lg bg-[#243247]">بستن</button>
        </div>
        <div class="p-6 space-y-5 text-sm">
            @if($selectedRequest)
                <dl class="grid grid-cols-2 gap-4">
                    <div><dt>نماینده</dt><dd>{{ $selectedRequest->user?->name ?? 'نامشخص' }}</dd></div>
                    <div><dt>وضعیت</dt><dd>{{ $statusLabels[$selectedRequest->status] ?? $selectedRequest->status }}</dd></div>
                    <div><dt>شارژ کیف پول</dt><dd>{{ number_format($selectedRequest->requested_amount) }} تومان</dd></div>
                    <div><dt>مبلغ پرداخت</dt><dd>{{ number_format($selectedRequest->payable_amount / 10) }} تومان</dd></div>
                    <div><dt>ثبت درخواست</dt><dd>{{ $this->toJalaliDateTime($selectedRequest->created_at) }}</dd></div>
                    <div><dt>مهلت پرداخت</dt><dd>{{ $this->toJalaliDateTime($selectedRequest->expires_at) }}</dd></div>
                    <div><dt>نوع تطبیق</dt><dd>{{ ['manual'=>'دستی', 'automatic'=>'خودکار'][$selectedRequest->match_type] ?? '—' }}</dd></div>
                    <div><dt>زمان تطبیق</dt><dd>{{ $this->toJalaliDateTime($selectedRequest->matched_at) }}</dd></div>
                    <div><dt>زمان تأیید</dt><dd>{{ $this->toJalaliDateTime($selectedRequest->settled_at) }}</dd></div>
                </dl>
                @if($selectedRequest->rejection_reason)
                    <p class="text-red-400">علت رد: {{ $selectedRequest->rejection_reason }}</p>
                @endif
                @if($selectedRequest->matchedBankMessage)
                    <section class="border-t border-[#243247] pt-4 space-y-2">
                        <h4 class="font-bold">پیام بانکی مرتبط #{{ $selectedRequest->matchedBankMessage->id }}</h4>
                        <p>{{ number_format($selectedRequest->matchedBankMessage->deposit_amount / 10) }} تومان · {{ $selectedRequest->matchedBankMessage->account_number }}</p>
                        <pre class="whitespace-pre-wrap break-words text-xs bg-[#080f1d] p-3 rounded-xl">{{ $selectedRequest->matchedBankMessage->raw_message }}</pre>
                    </section>
                @endif
                <section class="border-t border-[#243247] pt-4 space-y-3">
                    <h4 class="font-bold">تاریخچه عملیات</h4>
                    @forelse($selectedRequest->auditLogs as $log)
                        <div class="text-xs">
                            <p>{{ ['approve_top_up'=>'تأیید دستی', 'reject_top_up'=>'رد درخواست', 'manual_match'=>'تطبیق دستی و شارژ', 'automatic_match'=>'تطبیق خودکار و شارژ'][$log->action] ?? $log->action }}</p>
                            <p class="text-[#94A3B8]">{{ $this->toJalaliDateTime($log->created_at) }} · {{ $log->user?->name ?? 'سیستم' }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-[#94A3B8]">برای این رکورد قدیمی تاریخچه‌ای ثبت نشده است.</p>
                    @endforelse
                </section>
            @elseif($selectedMessage)
                <dl class="grid grid-cols-2 gap-4">
                    <div><dt>شماره حساب</dt><dd dir="ltr">{{ $selectedMessage->account_number }}</dd></div>
                    <div><dt>واریز</dt><dd>{{ number_format($selectedMessage->deposit_amount / 10) }} تومان</dd></div>
                    <div><dt>مانده</dt><dd>{{ number_format($selectedMessage->balance / 10) }} تومان</dd></div>
                    <div><dt>تاریخ تراکنش</dt><dd>{{ $this->toJalaliDateTime($selectedMessage->transaction_datetime) }}</dd></div>
                    <div><dt>وضعیت</dt><dd>{{ $selectedMessage->matchedRequest ? 'تطبیق و تأیید شده' : ($selectedMessage->processed ? 'بررسی شده، بدون تطبیق' : 'در انتظار بررسی') }}</dd></div>
                </dl>
                @if($selectedMessage->processing_error)
                    <p class="text-amber-400">نتیجه بررسی: {{ $selectedMessage->processing_error }}</p>
                @endif
                @if($selectedMessage->matchedRequest)
                    <button wire:click="showRequestDetail({{ $selectedMessage->matchedRequest->id }})" class="text-blue-400">درخواست مرتبط #{{ $selectedMessage->matchedRequest->id }} · {{ $selectedMessage->matchedRequest->user?->name }}</button>
                @else
                    <button wire:click="reprocessMessage({{ $selectedMessage->id }})" wire:loading.attr="disabled" wire:confirm="پردازش مجدد ممکن است کیف پول نماینده را شارژ کند. ادامه می‌دهید؟" class="bg-[#243247] px-4 py-2 rounded-xl">پردازش مجدد</button>
                @endif
                <pre class="whitespace-pre-wrap break-words text-xs bg-[#080f1d] p-3 rounded-xl">{{ $selectedMessage->raw_message }}</pre>
            @endif
            @error('operation') <p role="alert" class="text-red-400">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
