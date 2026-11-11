@props(['bank'])
<div class="commerce-bank-card">
    <header><span>{{ $bank?->bank_name ?: 'حساب مقصد واریز' }}</span><x-ui.icon name="wallet" /></header>
    @if($bank?->card_number)
        <div class="commerce-card-number"><code dir="ltr">{{ $bank->card_number }}</code><button type="button" aria-label="کپی شماره کارت" data-copy-text="{{ $bank->card_number }}" onclick="window.copyToClipboard(this.dataset.copyText, 'شماره کارت')"><x-ui.icon name="copy" /></button></div>
        <p>به نام {{ $bank->account_name ?: 'صاحب حساب' }}</p>
    @else
        <p class="mt-3">اطلاعات حساب مقصد در دسترس نیست؛ قبل از واریز با پشتیبانی تماس بگیرید.</p>
    @endif
</div>
