@component('layouts.partials.site-shell', ['title' => $item->title . ' | مرکز دانلود', 'brand_name' => setting('SITE_TITLE', 'همراه سیمرغ'), 'brand_logo' => setting('SITE_LOGO')])
<div class="ui-container py-12 space-y-8">
    <a href="{{ route('downloads.index') }}" wire:navigate class="text-sm text-brand-300">بازگشت به مرکز دانلود ←</a>
    <div class="grid lg:grid-cols-[1.4fr_1fr] gap-8 items-start">
        <article class="rounded-3xl border border-zinc-800 bg-zinc-900 overflow-hidden">
            @if($item->cover_url)<img src="{{ $item->cover_url }}" alt="{{ $item->title }}" class="w-full aspect-video object-cover">@endif
            <div class="p-6 sm:p-8"><span class="text-brand-300 text-xs">{{ $item->category->name }}</span><h1 class="text-2xl sm:text-3xl font-bold text-white mt-3">{{ $item->title }}</h1><p class="text-zinc-400 leading-8 mt-4">{{ $item->short_description }}</p><h2 class="text-lg font-bold text-white mt-8 mb-4">توضیحات و راهنمای استفاده</h2><div class="text-sm leading-8 text-zinc-300 whitespace-pre-wrap break-words">{{ $item->description ?: 'توضیح تکمیلی ثبت نشده است.' }}</div></div>
        </article>
        <aside class="ui-card space-y-6 lg:sticky lg:top-24"><h2 class="text-xl font-bold text-white">دریافت فایل</h2><dl class="space-y-4 text-sm"><div class="flex justify-between"><dt class="text-zinc-500">پلتفرم</dt><dd>{{ $item->platform ?: 'عمومی' }}</dd></div><div class="flex justify-between"><dt class="text-zinc-500">نسخه</dt><dd>{{ $item->version ?: '—' }}</dd></div><div class="flex justify-between"><dt class="text-zinc-500">حجم فایل</dt><dd dir="ltr">{{ $item->file_size_label }}</dd></div><div class="flex justify-between"><dt class="text-zinc-500">تعداد دانلود</dt><dd>{{ number_format($item->downloads) }}</dd></div><div class="flex justify-between"><dt class="text-zinc-500">آخرین به‌روزرسانی</dt><dd>{{ \Morilog\Jalali\Jalalian::fromCarbon($item->updated_at)->format('Y/m/d') }}</dd></div></dl><p class="text-xs text-zinc-500 break-all" dir="ltr">{{ $item->file_name }}</p><a href="{{ route('downloads.file', $item->slug) }}" class="ui-button w-full">دانلود فایل ↓</a><p class="text-xs leading-6 text-zinc-500">قبل از نصب، سازگاری نسخه با دستگاه خود و اعتبار فایل را بررسی کنید.</p></aside>
    </div>
</div>
@endcomponent
