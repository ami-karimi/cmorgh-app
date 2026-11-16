@component('layouts.partials.site-shell', ['brand_name' => $storeData['brand_name'], 'brand_logo' => $storeData['logo'], 'title' => $storeData['brand_name'] . ' | خرید و مدیریت اشتراک'])
<section class="home-hero relative overflow-hidden">
    <div class="home-hero-grid" aria-hidden="true"></div>
    <div class="home-orb home-orb-one" aria-hidden="true"></div>
    <div class="home-orb home-orb-two" aria-hidden="true"></div>

    <div class="ui-container relative grid lg:grid-cols-[1.05fr_.95fr] items-center gap-12 lg:gap-16 py-16 sm:py-24 lg:py-28">
        <div class="home-hero-copy" data-reveal>
            <div class="home-kicker"><span class="home-kicker-dot"></span> تجربه‌ای سریع، امن و بدون مرز</div>
            <h1 class="home-title">اتصال بهتر،<br><span>آزادی بیشتر.</span></h1>
            <p class="home-lead">با {{ $storeData['brand_name'] }} اشتراک مناسب خود را انتخاب کنید و همه‌چیز را از یک پنل ساده، سریع و حرفه‌ای مدیریت کنید.</p>
            <div class="flex flex-wrap gap-3 mt-8">
                <a href="#pricing" class="ui-button home-primary-button">مشاهده اشتراک‌ها <span aria-hidden="true">←</span></a>
                <a href="{{ route('tutorials.index') }}" wire:navigate class="ui-button ui-button-secondary home-secondary-button">راهنمای اتصال <span aria-hidden="true">↗</span></a>
            </div>
            <div class="home-trust-row">
                <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg> فعال‌سازی سریع</span>
                <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 5 6v5c0 4.5 2.9 8.5 7 10 4.1-1.5 7-5.5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg> مدیریت امن</span>
                <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg> پشتیبانی همراه</span>
            </div>
        </div>

        <div class="home-visual" data-reveal data-reveal-delay="150" aria-label="نمایش اتصال امن">
            <div class="home-visual-glow"></div>
            <div class="home-visual-topbar"><span class="home-status"><i></i> اتصال پایدار</span><span class="home-live">LIVE</span></div>
            <svg viewBox="0 0 520 380" class="w-full home-network" fill="none" aria-hidden="true">
                <defs>
                    <linearGradient id="homeLine" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#60a5fa" stop-opacity=".1"/><stop offset=".5" stop-color="#60a5fa"/><stop offset="1" stop-color="#a78bfa" stop-opacity=".15"/></linearGradient>
                    <filter id="homeGlow"><feGaussianBlur stdDeviation="7" result="blur"/><feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                </defs>
                <circle cx="260" cy="190" r="142" stroke="#334155" stroke-opacity=".65" stroke-dasharray="3 10"/>
                <circle cx="260" cy="190" r="102" stroke="#334155" stroke-opacity=".6"/>
                <path class="network-line network-line-one" d="M83 93 260 190 437 93M83 287l177-97 177 97M260 31v318" stroke="url(#homeLine)" stroke-width="1.5"/>
                <path class="network-line network-line-two" d="M83 93 260 190 437 287M437 93 260 190 83 287" stroke="#60a5fa" stroke-opacity=".18" stroke-width="1"/>
                <g class="home-core" filter="url(#homeGlow)"><circle cx="260" cy="190" r="48" fill="#172b4b" stroke="#60a5fa" stroke-width="1.5"/><circle cx="260" cy="190" r="37" stroke="#93c5fd" stroke-opacity=".25"/><path d="m260 161-25 15 25 15 25-15-25-15Zm-25 29 25 15 25-15m-50 14 25 15 25-15" stroke="#bfdbfe" stroke-width="2" stroke-linejoin="round"/></g>
                <g class="home-nodes"><circle cx="83" cy="93" r="7"/><circle cx="437" cy="93" r="7"/><circle cx="83" cy="287" r="7"/><circle cx="437" cy="287" r="7"/><circle cx="260" cy="31" r="7"/><circle cx="260" cy="349" r="7"/></g>
            </svg>
            <div class="home-visual-caption"><span class="home-caption-icon">✦</span><span><strong>یک پنل؛ همه‌چیز در دسترس</strong><small>اشتراک، سفارش‌ها و راهنمای اتصال</small></span></div>
        </div>
    </div>

    <div class="ui-container relative home-stats" data-reveal data-reveal-delay="250">
        <div><strong>ساده و سریع</strong><span>از انتخاب تا اتصال</span></div>
        <div><strong>پشتیبانی واقعی</strong><span>همراه شما در هر مرحله</span></div>
        <div><strong>مدیریت یکپارچه</strong><span>همه‌چیز در یک حساب</span></div>
    </div>
</section>

<section id="features" class="home-features py-16 sm:py-24">
    <div class="ui-container">
        <div class="home-section-heading" data-reveal><div><span class="home-section-label">چرا {{ $storeData['brand_name'] }}؟</span><h2 class="ui-section-title mt-2">اتصال، بدون پیچیدگی</h2></div><p class="ui-muted max-w-md">تمام ابزارهایی که برای تجربه‌ای روان و مطمئن نیاز دارید، در یک فضای ساده و کاربردی.</p></div>
        <div class="grid md:grid-cols-3 gap-5 mt-10">
            @foreach([
                ['01', 'انتخاب هوشمندانه', 'پلن‌ها و تعرفه‌ها را مقایسه کنید و مناسب‌ترین اشتراک را برای نیاز خود انتخاب کنید.', '◈'],
                ['02', 'مدیریت در یک نگاه', 'اطلاعات سرویس، سفارش‌ها و وضعیت اشتراک را همیشه در پنل خود در دسترس داشته باشید.', '⌁'],
                ['03', 'اتصال قدم‌به‌قدم', 'با راهنماهای ساده و دقیق، سرویس خود را در کمترین زمان روی دستگاهتان راه‌اندازی کنید.', '↗'],
            ] as [$number, $heading, $description, $icon])
                <article class="home-feature-card" data-reveal data-reveal-delay="{{ (int) $number * 80 }}"><div class="home-feature-icon">{{ $icon }}</div><div class="flex items-center justify-between mt-7"><h3>{{ $heading }}</h3><span class="home-feature-number">{{ $number }}</span></div><p>{{ $description }}</p><div class="home-card-line"></div></article>
            @endforeach
        </div>
    </div>
</section>

<section id="pricing" class="home-pricing py-16 sm:py-24 scroll-mt-24">
    <div class="ui-container">
        <div class="text-center mb-10" data-reveal><span class="home-section-label">شروع کنید</span><h2 class="ui-section-title mt-2">اشتراک مناسب شما</h2><p class="ui-muted mt-3">پلن موردنظرتان را انتخاب کنید و سریع به دنیای بدون مرز وصل شوید.</p></div>
        <div data-reveal data-reveal-delay="100">@livewire('store.package-list')</div>
    </div>
</section>

@if(!empty($storeData['support_link']) && $storeData['support_link'] !== '#')
    <section class="ui-container mb-16" data-reveal><div class="home-support"><div><span class="home-section-label">نیاز به راهنمایی دارید؟</span><h2 class="font-bold text-xl mt-2 text-white">ما برای انتخاب بهتر کنار شما هستیم.</h2><p class="ui-muted mt-2">اگر در انتخاب سرویس یا راه‌اندازی آن سوالی دارید، با پشتیبانی در ارتباط باشید.</p></div><a href="{{ $storeData['support_link'] }}" target="_blank" rel="noopener noreferrer" class="ui-button home-secondary-button">ارتباط با پشتیبانی <span>↗</span></a></div></section>
@endif
<section class="ui-container mb-16"><div class="home-support"><div><span class="home-section-label">ابزارهای موردنیاز شما</span><h2 class="font-bold text-xl mt-2 text-white">همه فایل‌ها؛ یک مرکز دانلود</h2><p class="ui-muted mt-2">برنامه‌های اتصال، راهنماها و فایل‌های موردنیاز را بر اساس دسته‌بندی پیدا و دانلود کنید.</p></div><a href="{{ route('downloads.index') }}" wire:navigate class="ui-button">ورود به مرکز دانلود ↓</a></div></section>
@endcomponent
