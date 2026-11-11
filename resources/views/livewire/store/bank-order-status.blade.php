@php
    $paid = (bool)$pendingOrder->paid_at || $pendingOrder->status === 'approved';
    $delivered = $pendingOrder->status === 'approved';
    $review = $paid && ($pendingOrder->fulfillment_state === 'requires_review' || $pendingOrder->fulfillment_error);
    $awaitingBank = !$paid && $pendingOrder->status === 'pending' && $pendingOrder->bankPayment?->status === 'pending';
    $refunded = $pendingOrder->status === 'refunded';
    $title = $delivered ? 'سرویس شما آماده است' : ($refunded ? 'هزینه به کیف پول برگشت داده شد' : ($review ? 'پرداخت ثبت شد؛ صدور نیاز به بررسی دارد' : ($paid ? 'در حال آماده‌سازی سرویس شما' : ($awaitingBank ? 'یک قدم تا فعال‌شدن سرویس' : ($pendingOrder->status === 'pending' ? 'سفارش منتظر بررسی فیش است' : 'درخواست پرداخت بسته شد')))));
@endphp
<div wire:poll.5s="checkOrderStatus" class="commerce-page commerce-payment-layout">
    <section class="commerce-panel">
        <header class="commerce-status-hero"><span class="commerce-mark"><x-ui.icon :name="$delivered || $refunded ? 'check' : ($review ? 'alert' : 'wallet')" /></span><h2>{{ $title }}</h2><p>{{ $delivered ? 'اکانت شما با موفقیت ساخته شد. مشخصات اتصال در پنل کاربری آماده است.' : ($review ? 'دوباره پرداخت نکنید. مبلغ سفارش محفوظ است و پشتیبانی وضعیت صدور را بررسی می‌کند.' : ($refunded ? 'اعتبار برگشتی برای خرید جدید در کیف پول قابل استفاده است.' : 'وضعیت این سفارش به‌صورت خودکار در همین صفحه به‌روزرسانی می‌شود.')) }}</p><span class="commerce-badge commerce-badge-muted mt-4">سفارش #{{ $pendingOrder->id }}</span></header>
        <div class="commerce-body">
            <div class="commerce-steps" aria-label="مراحل سفارش"><div class="commerce-step is-complete"><span><x-ui.icon name="check" /></span>ثبت سفارش</div><div class="commerce-step {{ $paid ? 'is-complete' : 'is-current' }}"><span>@if($paid)<x-ui.icon name="check" />@else۲@endif</span>تأیید پرداخت</div><div class="commerce-step {{ $delivered ? 'is-complete' : ($paid && !$review && !$refunded ? 'is-current' : '') }}"><span>@if($delivered)<x-ui.icon name="check" />@else۳@endif</span>دریافت سرویس</div></div>
            <div class="commerce-payment-columns">
                <div>
                    @if($awaitingBank)
                        <div class="commerce-pay-amount"><span>مبلغ دقیق واریز به ریال</span><strong>{{ number_format($pendingOrder->bankPayment->payable_amount) }} <small>ریال</small></strong><p>شناسه یکتای واریز داخل این مبلغ لحاظ شده است.</p></div>
                        <x-ui.bank-card :bank="$bankDetails" />
                        <div class="commerce-deadline"><span><x-ui.icon name="clock" />مهلت پرداخت</span><time class="commerce-numeric">{{ \App\Support\JalaliDateTime::display($pendingOrder->bankPayment->expires_at) }}</time></div>
                        <div class="commerce-alert"><x-ui.icon name="alert" /><div><strong>مبلغ پلن را جداگانه واریز نکنید</strong>دقیقاً مبلغ ریالی بالا را پرداخت کنید. تأیید پس از دریافت پیام واقعی بانک انجام می‌شود و کد ورود پیامکی، پرداخت را تأیید نمی‌کند.</div></div>
                        @if($pendingOrder->bankPayment->expires_at?->isPast())<p role="alert" class="commerce-field-error mt-3">مهلت واریز گذشته است؛ اگر پرداخت کرده‌اید، قبل از لغو با پشتیبانی تماس بگیرید.</p>@endif
                        <button type="button" wire:click="cancelBankOrder" wire:loading.attr="disabled" wire:confirm="اگر واریز کرده‌اید لغو نکنید. سفارش لغو شود؟" class="commerce-button commerce-button-danger w-full mt-4">لغو درخواست پرداخت</button>
                    @elseif($delivered)
                        <div class="commerce-alert commerce-alert-success"><x-ui.icon name="check" /><div><strong>پرداخت تأیید شد و اکانت شما ساخته شد.</strong>مشخصات اتصال و مدیریت اشتراک را از بخش سرویس‌های من ببینید.</div></div><a href="{{ route('customer.dashboard') }}" class="commerce-button w-full mt-4">مشاهده سرویس‌های من<x-ui.icon name="arrow" /></a>
                    @elseif($refunded)<div class="commerce-alert commerce-alert-success"><x-ui.icon name="wallet" /><div><strong>{{ number_format($pendingOrder->price) }} تومان به کیف پول برگشت داده شد.</strong>برای خرید دوباره می‌توانید از موجودی کیف پول استفاده کنید.</div></div><a href="{{ route('customer.wallet') }}" class="commerce-button w-full mt-4">مشاهده کیف پول</a>
                    @elseif($review)<div class="commerce-alert"><x-ui.icon name="alert" /><div><strong>هزینه سفارش محفوظ است</strong>پشتیبانی باید وضعیت سرور و صدور سرویس را بررسی کند. پرداخت مجدد لازم نیست.</div></div>
                    @elseif($paid)<div class="commerce-alert"><x-ui.icon name="clock" /><div><strong>پرداخت تأیید شد؛ اکانت در حال صدور است.</strong>پس از تکمیل صدور، لینک سرویس در همین صفحه نمایش داده می‌شود.</div></div>
                    @elseif($pendingOrder->status === 'pending')<div class="commerce-alert"><x-ui.icon name="clock" /><div><strong>فیش سفارش ثبت شد</strong>بعد از تأیید مدیریت، صدور اکانت آغاز می‌شود.</div></div>
                    @else<div class="commerce-alert"><x-ui.icon name="alert" /><div><strong>{{ ['cancelled'=>'درخواست لغو شد','rejected'=>'درخواست رد شد'][$pendingOrder->status] ?? 'درخواست بسته شد' }}</strong>برای خرید جدید به فروشگاه برگردید.</div></div>@endif
                </div>
                <aside class="commerce-payment-aside"><h3 class="text-xs font-bold mb-5">خلاصه سفارش</h3><dl class="commerce-summary-rows"><div><dt>شناسه سفارش</dt><dd>#{{ $pendingOrder->id }}</dd></div><div><dt>مبلغ قبل از تخفیف</dt><dd>{{ number_format($pendingOrder->original_price ?? $pendingOrder->price) }} تومان</dd></div><div><dt>تخفیف</dt><dd class="!text-emerald-400">{{ number_format($pendingOrder->discount_amount) }} تومان</dd></div></dl><hr class="commerce-divider"><dl class="commerce-summary-rows"><div><dt>مبلغ نهایی</dt><dd class="!text-brand-300">{{ number_format($pendingOrder->price) }} تومان</dd></div>@if($pendingOrder->paid_at)<div><dt>زمان تأیید</dt><dd class="commerce-numeric text-[10px]">{{ \App\Support\JalaliDateTime::display($pendingOrder->paid_at) }}</dd></div>@endif</dl><p class="commerce-hint mt-5">مبلغ یکتای واریز بانکی ریالی و شامل شناسه پرداخت است؛ قیمت سفارش در این خلاصه برحسب تومان نمایش داده می‌شود.</p></aside>
            </div>
        </div>
        <footer class="commerce-form-footer"><a href="{{ route('customer.orders') }}" class="commerce-button commerce-button-secondary">پیگیری سفارش‌ها</a><button type="button" wire:click="resetWizard" class="commerce-button commerce-button-quiet">بازگشت به فروشگاه</button></footer>
    </section>
</div>
