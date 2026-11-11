<div class="commerce-page">
    <header class="commerce-heading"><div class="commerce-heading-main"><span class="commerce-mark"><x-ui.icon name="wallet" /></span><div><h1>کیف پول و شارژ خودکار</h1><p>اعتبارتان را مدیریت کنید؛ واریز بانکی بدون ارسال فیش تأیید می‌شود.</p></div></div><a href="{{ route('store.index') }}" class="commerce-button commerce-button-secondary">خرید سرویس<x-ui.icon name="arrow" /></a></header>
    <div class="commerce-wallet-grid">
        <section class="commerce-panel">
            <div class="commerce-panel-head"><div><h2>{{ $topup ? 'پیگیری درخواست شارژ' : 'افزایش موجودی کیف پول' }}</h2><p>{{ $topup ? 'نتیجه واریز در همین صفحه به‌روزرسانی می‌شود.' : 'مبلغ را انتخاب کنید و مشخصات واریز را دریافت کنید.' }}</p></div><span class="commerce-badge"><x-ui.icon name="shield" />تأیید بانکی</span></div>
            @if(!$topup)
                <form wire:submit="createRequest" class="commerce-body">
                    <div class="commerce-field"><label for="wallet-charge-amount">مبلغ شارژ <span class="commerce-optional">تومان</span></label><input id="wallet-charge-amount" wire:model.live.debounce.300ms="amount" type="number" min="1000" max="100000000" dir="ltr" class="commerce-input commerce-amount-input commerce-numeric">@error('amount')<p role="alert" class="commerce-field-error">{{ $message }}</p>@enderror</div>
                    <div class="commerce-quick-amounts" role="group" aria-label="انتخاب سریع مبلغ شارژ">@foreach([100000,250000,500000] as $quick)<button type="button" wire:click="$set('amount', {{ $quick }})" aria-pressed="{{ (int)$amount === $quick ? 'true' : 'false' }}">{{ number_format($quick) }} تومان</button>@endforeach</div>
                    @if($bonusPreview)<div class="commerce-alert commerce-alert-success mb-5"><x-ui.icon name="gift" /><div><strong>{{ number_format($bonusPreview) }} تومان اعتبار هدیه</strong>بونوس قطعی هنگام ثبت درخواست مشخص می‌شود و بعد از تأیید واریز به کیف پول اضافه خواهد شد.</div></div>@endif
                    <button type="submit" wire:loading.attr="disabled" class="commerce-button w-full"><span wire:loading.remove wire:target="createRequest">دریافت مبلغ و مشخصات واریز</span><span wire:loading wire:target="createRequest">در حال ثبت درخواست…</span><x-ui.icon name="arrow" /></button>
                    <p class="commerce-hint text-center mt-3">حداقل شارژ ۱٬۰۰۰ تومان · مهلت واریز ۲۰ دقیقه</p>
                </form>
            @else
                <div wire:poll.5s="checkStatus" class="commerce-body">
                    <div class="flex items-center justify-between gap-3 mb-5"><span class="text-[11px] text-zinc-500">درخواست #{{ $topup->id }}</span><span class="commerce-badge {{ $topup->status === 'completed' ? 'commerce-badge-success' : ($topup->status === 'pending' ? 'commerce-badge-warning' : 'commerce-badge-muted') }}">{{ ['pending'=>'در انتظار پرداخت','completed'=>'تأیید شده','rejected'=>'رد شده','expired'=>'لغو / منقضی شده'][$topup->status] ?? 'در حال بررسی' }}</span></div>
                    @if($topup->status === 'pending')
                        <div class="commerce-pay-amount"><span>مبلغ دقیق واریز</span><strong>{{ number_format($topup->payable_amount / 10) }} <small>تومان</small></strong><p>شناسه یکتای واریز در این مبلغ لحاظ شده است.</p></div>
                        <x-ui.bank-card :bank="$bank" />
                        <div class="commerce-deadline"><span><x-ui.icon name="clock" />مهلت پرداخت</span><time class="commerce-numeric">{{ \App\Support\JalaliDateTime::display($topup->expires_at) }}</time></div>
                        @if($topup->expires_at?->isPast())<p role="alert" class="commerce-field-error mb-3">مهلت واریز گذشته است؛ اگر قبلاً پرداخت کرده‌اید، قبل از لغو با پشتیبانی تماس بگیرید.</p>@endif
                        <div class="commerce-alert"><x-ui.icon name="alert" /><div><strong>دقیقاً همین مبلغ را واریز کنید</strong>تأیید بعد از دریافت پیام واقعی بانک انجام می‌شود. نیازی به ارسال فیش یا واردکردن کد پیامکی نیست.</div></div>
                        <button type="button" wire:click="cancelRequest" wire:loading.attr="disabled" wire:confirm="درخواست لغو شود؟ اگر واریز کرده‌اید لغو نکنید." class="commerce-button commerce-button-danger w-full mt-4">لغو درخواست</button>
                    @else
                        <div class="commerce-status-hero border-0"><span class="commerce-mark"><x-ui.icon :name="$topup->status === 'completed' ? 'check' : 'alert'" /></span><h2>{{ $topup->status === 'completed' ? 'کیف پول شما شارژ شد' : 'درخواست شارژ بسته شد' }}</h2><p>{{ $topup->status === 'completed' ? 'اعتبار و بونوس تأییدشده به موجودی اضافه شده است. می‌توانید سرویس جدید بخرید.' : ($topup->rejection_reason ?: 'برای شارژ مجدد، یک درخواست تازه ثبت کنید.') }}</p></div>
                        <button type="button" wire:click="newRequest" class="commerce-button w-full">ثبت درخواست جدید<x-ui.icon name="plus" /></button>
                    @endif
                </div>
            @endif
        </section>
        <aside class="grid gap-5">
            <section class="commerce-panel commerce-balance commerce-body"><span class="commerce-badge commerce-badge-muted"><x-ui.icon name="wallet" />اعتبار قابل استفاده</span><div class="commerce-balance-amount">{{ number_format(auth()->user()->balance) }} <small>تومان</small></div><p class="commerce-hint">موجودی تأییدشده برای خرید سرویس‌های جدید</p><hr class="commerce-divider"><dl class="commerce-summary-rows"><div><dt>{{ $topup ? 'مبلغ شارژ این درخواست' : 'مبلغ انتخاب‌شده' }}</dt><dd>{{ number_format($topup?->requested_amount ?? (is_numeric($amount) ? $amount : 0)) }} تومان</dd></div><div><dt>{{ $topup ? 'بونوس این درخواست' : 'بونوس احتمالی' }}</dt><dd class="!text-emerald-400">{{ number_format($topup?->bonus_amount ?? $bonusPreview) }} تومان</dd></div><div><dt>اعتبار {{ $topup?->status === 'completed' ? 'اضافه‌شده' : 'پس از تأیید' }}</dt><dd>{{ number_format(($topup?->requested_amount ?? (is_numeric($amount) ? $amount : 0)) + ($topup?->bonus_amount ?? $bonusPreview)) }} تومان</dd></div></dl></section>
            <div class="commerce-alert"><x-ui.icon name="shield" /><div><strong>تأیید پرداخت، نه کد ورود</strong>پیام واریز بانک با مبلغ، زمان و حساب مقصد بررسی می‌شود. کد ورود پیامکی جایگزین تأیید پرداخت نیست.</div></div>
        </aside>
    </div>
    <section class="commerce-panel"><div class="commerce-panel-head"><div><h2>تراکنش‌های اخیر</h2><p>آخرین ۳۰ تغییر اعتبار کیف پول شما</p></div><span class="commerce-badge commerce-badge-muted">{{ $transactions->count() }} تراکنش</span></div>
        @if($transactions->isEmpty())<div class="commerce-empty"><span class="commerce-mark"><x-ui.icon name="wallet" /></span><strong>هنوز تراکنشی ثبت نشده است</strong><p>اولین شارژ یا خرید شما در این قسمت نمایش داده می‌شود.</p></div>@else
        <div class="commerce-table-wrap"><table class="commerce-table"><thead><tr><th>شرح تراکنش</th><th>نوع</th><th>مبلغ (تومان)</th><th>وضعیت</th><th>تاریخ شمسی</th></tr></thead><tbody>@foreach($transactions as $transaction)<tr wire:key="wallet-transaction-{{ $transaction->id }}"><td><strong>{{ $transaction->description ?: 'تراکنش کیف پول' }}</strong><small>#{{ $transaction->id }}</small></td><td>{{ ['plus'=>'افزایش اعتبار','minus'=>'کسر اعتبار','plus_amn'=>'افزایش اعتبار امانی','minus_amn'=>'کسر اعتبار امانی'][$transaction->type] ?? 'تغییر اعتبار' }}</td><td><strong class="{{ $transaction->type === 'plus' ? '!text-emerald-400' : '!text-zinc-300' }}">{{ number_format($transaction->price) }}</strong></td><td><span class="commerce-badge {{ $transaction->approved ? 'commerce-badge-success' : 'commerce-badge-warning' }}">{{ $transaction->approved ? 'تأیید شده' : 'در انتظار تأیید' }}</span></td><td class="whitespace-nowrap commerce-numeric">{{ \App\Support\JalaliDateTime::display($transaction->created_at) }}</td></tr>@endforeach</tbody></table></div>@endif
    </section>
</div>
