@component('layouts.partials.site-shell', ['brand_name' => $storeData['brand_name'], 'brand_logo' => $storeData['logo'], 'title' => $storeData['brand_name'] . ' | خرید و مدیریت اشتراک'])
<section class="ui-hero py-14 sm:py-20 lg:py-24">
    <div class="ui-container grid lg:grid-cols-2 items-center gap-12 lg:gap-16">
        <div>
            <span class="inline-flex items-center gap-2 text-brand-300 bg-brand-500/10 border border-brand-500/20 rounded-lg px-3 py-1.5 text-xs mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-400"></span>اینترنت، به سبک شما
            </span>
            <h1 class="text-3xl sm:text-5xl font-bold text-zinc-50 leading-[1.65] mb-6">دنیای شما،<br><span class="text-brand-300">بدون مرز.</span></h1>
            <p class="text-zinc-400 leading-8 max-w-lg">با {{ $storeData['brand_name'] }} اشتراک مناسب خود را انتخاب کنید و سرویس‌ها، تمدید و اطلاعات اتصال را از یک پنل ساده مدیریت کنید.</p>
            <div class="flex flex-wrap gap-3 mt-8">
                <a href="#pricing" class="ui-button">انتخاب اشتراک <span aria-hidden="true">←</span></a>
                <a href="{{ route('tutorials.index') }}" wire:navigate class="ui-button ui-button-secondary">راهنمای اتصال</a>
            </div>
            <div class="flex flex-wrap gap-5 mt-8 text-xs text-zinc-400">
                <span>مدیریت آسان اشتراک</span><span class="border-r border-zinc-700 pr-5">راهنمای گام‌به‌گام اتصال</span>
            </div>
        </div>
        <div class="ui-network-art p-6 sm:p-10 relative" aria-hidden="true">
            <svg viewBox="0 0 420 290" class="w-full" fill="none">
                <circle cx="210" cy="145" r="120" stroke="#243247"/>
                <circle cx="210" cy="145" r="87" stroke="#243247" stroke-dasharray="4 8"/>
                <path d="m80 85 130 60 130-60M80 205l130-60 130 60M210 25v240" stroke="#3b82f6" stroke-opacity=".5" stroke-width="1.5"/>
                <rect x="169" y="104" width="82" height="82" rx="22" fill="#17253a" stroke="#60a5fa"/>
                <path d="m210 121-23 13 23 13 23-13-23-13Zm-23 25 23 13 23-13m-46 12 23 13 23-13" stroke="#93c5fd" stroke-width="2" stroke-linejoin="round"/>
                <g fill="#17253a" stroke="#334155"><rect x="54" y="62" width="52" height="46" rx="12"/><rect x="314" y="62" width="52" height="46" rx="12"/><rect x="54" y="182" width="52" height="46" rx="12"/><rect x="314" y="182" width="52" height="46" rx="12"/></g>
                <g fill="#60a5fa"><circle cx="80" cy="85" r="5"/><circle cx="340" cy="85" r="5"/><circle cx="80" cy="205" r="5"/><circle cx="340" cy="205" r="5"/><circle cx="210" cy="25" r="5"/><circle cx="210" cy="265" r="5"/></g>
            </svg>
            <div class="text-center text-xs text-zinc-400 mt-3">یک پنل؛ تمام سرویس‌های شما</div>
        </div>
    </div>
</section>
<section id="features" class="py-14 border-y border-zinc-800">
    <div class="ui-container">
        <div class="mb-8"><h2 class="ui-section-title">از انتخاب تا اتصال، ساده و روشن</h2><p class="ui-muted mt-2">هر چیزی که برای مدیریت اشتراک نیاز دارید.</p></div>
        <div class="grid md:grid-cols-3 gap-5">
            @foreach([
                ['01', 'انتخاب متناسب با نیاز', 'پلن‌ها و تعرفه‌ها را مقایسه کنید و اشتراک مناسب خود را انتخاب کنید.'],
                ['02', 'اطلاعات در دسترس', 'اطلاعات سرویس و سفارش‌های خود را از پنل کاربری مشاهده کنید.'],
                ['03', 'راهنمای قدم‌به‌قدم', 'برای راه‌اندازی روی دستگاه خود، از آموزش‌های اتصال استفاده کنید.'],
            ] as [$number, $heading, $description])
                <article class="ui-card"><span class="text-brand-300 text-xs font-mono">{{ $number }}</span><h3 class="font-bold text-base mt-4 mb-3">{{ $heading }}</h3><p class="text-sm text-zinc-400 leading-8">{{ $description }}</p></article>
            @endforeach
        </div>
    </div>
</section>
<section id="pricing" class="py-16 sm:py-20 scroll-mt-24">
    <div class="ui-container">
        <div class="text-center mb-10"><h2 class="ui-section-title">اشتراک مناسب شما</h2><p class="ui-muted mt-3">مشخصات و قیمت هر پلن را بررسی کنید و سپس سفارش دهید.</p></div>
        @livewire('store.package-list')
    </div>
</section>
@if(!empty($storeData['support_link']) && $storeData['support_link'] !== '#')
    <section class="ui-container mb-14"><div class="ui-card flex flex-col sm:flex-row sm:items-center justify-between gap-5"><div><h2 class="font-bold">برای انتخاب سرویس راهنمایی می‌خواهید؟</h2><p class="ui-muted mt-2">با پشتیبانی فروشگاه در ارتباط باشید.</p></div><a href="{{ $storeData['support_link'] }}" target="_blank" rel="noopener noreferrer" class="ui-button ui-button-secondary">ارتباط با پشتیبانی</a></div></section>
@endif
@endcomponent
