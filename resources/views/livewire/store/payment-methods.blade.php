<div class="commerce-type-grid mb-6" role="group" aria-label="روش پرداخت سفارش">
    @if(auth()->check())<button type="button" wire:click="$set('paymentMethod','wallet')" class="commerce-type" aria-pressed="{{ $paymentMethod === 'wallet' ? 'true' : 'false' }}"><x-ui.icon name="wallet" /><strong>کیف پول</strong><small>پرداخت از اعتبار موجود</small></button>@endif
    <button type="button" wire:click="$set('paymentMethod','bank')" class="commerce-type" aria-pressed="{{ $paymentMethod === 'bank' ? 'true' : 'false' }}"><x-ui.icon name="shield" /><strong>تأیید خودکار</strong><small>واریز با پیام بانکی</small></button>
    <button type="button" wire:click="$set('paymentMethod','receipt')" class="commerce-type" aria-pressed="{{ $paymentMethod === 'receipt' ? 'true' : 'false' }}"><x-ui.icon name="ticket" /><strong>فیش بانکی</strong><small>بررسی توسط مدیریت</small></button>
</div>
@if($paymentMethod === 'bank')<div class="commerce-alert mb-6"><x-ui.icon name="shield" /><div><strong>واریز بانکی با تأیید خودکار پیامکی</strong>ابتدا سفارش را ثبت کنید تا مبلغ یکتای واریز مشخص شود. دریافت پیام واقعی بانک، پرداخت را تأیید و صدور اکانت را آغاز می‌کند.</div></div>@endif
