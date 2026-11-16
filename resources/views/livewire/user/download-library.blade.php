<div class="ui-container py-10 sm:py-16 space-y-8" dir="rtl">
    <section class="relative overflow-hidden rounded-3xl border border-brand-500/20 bg-gradient-to-bl from-brand-950 via-zinc-900 to-zinc-950 p-7 sm:p-12">
        <div class="absolute -left-10 -top-10 w-64 h-64 rounded-full bg-brand-500/10 blur-3xl pointer-events-none"></div>
        <span class="text-xs font-bold text-brand-300">ابزارهای اتصال؛ همیشه در دسترس</span>
        <h1 class="text-3xl sm:text-5xl font-black text-white mt-4">مرکز <span class="text-brand-300">دانلود</span></h1>
        <p class="text-zinc-400 text-sm leading-8 mt-4 max-w-xl">برنامه‌ها، راهنماها و فایل‌های موردنیازتان را در یک کتابخانه مرتب پیدا کنید؛ نسخه مناسب دستگاه خود را انتخاب کنید و شروع کنید.</p>
        <div class="mt-7 flex flex-col sm:flex-row gap-3 max-w-2xl">
            <input wire:model.live.debounce.300ms="search" aria-label="جست‌وجوی فایل" placeholder="نام برنامه یا فایل را جست‌وجو کنید…" class="flex-1 min-w-0 bg-zinc-950/70 border border-zinc-700 rounded-xl px-4 py-3 text-sm">
            <select wire:model.live="sort" aria-label="مرتب‌سازی" class="bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-sm"><option value="latest">جدیدترین و ویژه‌ها</option><option value="popular">بیشترین دانلود</option></select>
        </div>
    </section>
    <div class="flex flex-wrap gap-2" aria-label="دسته‌بندی فایل‌ها">
        <button wire:click="$set('category','')" class="ui-button {{ $category === '' ? '' : 'ui-button-secondary' }}">همه فایل‌ها</button>
        @foreach($categories as $cat)<button wire:click="$set('category','{{ $cat->id }}')" class="ui-button {{ $category == $cat->id ? '' : 'ui-button-secondary' }}">{{ $cat->icon }} {{ $cat->name }} <span class="text-xs opacity-60">{{ $cat->items_count }}</span></button>@endforeach
    </div>
    <div wire:loading wire:target="search,category,sort" role="status" class="text-sm text-brand-300">در حال بارگذاری فایل‌ها…</div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($items as $item)
        <article wire:key="download-{{ $item->id }}" class="group rounded-2xl overflow-hidden border border-zinc-800 bg-zinc-900 shadow-lg transition duration-300 hover:border-brand-500/50 motion-safe:hover:-translate-y-1">
            <a href="{{ route('downloads.show', $item->slug) }}" wire:navigate class="block relative aspect-[16/9] bg-gradient-to-br from-brand-950 to-zinc-950 overflow-hidden" aria-label="جزئیات {{ $item->title }}">
                @if($item->cover_url)<img src="{{ $item->cover_url }}" alt="{{ $item->title }}" loading="lazy" class="w-full h-full object-cover transition duration-500 motion-safe:group-hover:scale-105">@else<div class="h-full grid place-items-center text-6xl text-brand-400/40">⇩</div>@endif
                @if($item->is_featured)<span class="absolute top-3 left-3 rounded-lg bg-amber-400 text-zinc-950 px-3 py-1 text-xs font-bold">پیشنهاد ویژه</span>@endif
            </a>
            <div class="p-6"><span class="text-xs text-brand-300">{{ $item->category->name }} · {{ $item->platform ?: 'عمومی' }}</span><h2 class="font-bold text-lg text-white mt-3">{{ $item->title }}</h2><p class="text-sm leading-7 text-zinc-400 mt-2 line-clamp-2 min-h-14">{{ $item->short_description }}</p><div class="flex gap-3 mt-4 text-xs text-zinc-500"><span dir="ltr">{{ $item->file_size_label }}</span><span>{{ number_format($item->downloads) }} دانلود</span>@if($item->version)<span>نسخه {{ $item->version }}</span>@endif</div><div class="mt-6 flex gap-2"><a href="{{ route('downloads.file', $item->slug) }}" class="ui-button flex-1">دانلود فایل ↓</a><a href="{{ route('downloads.show', $item->slug) }}" wire:navigate class="ui-button ui-button-secondary">جزئیات</a></div></div>
        </article>
        @empty<div class="col-span-full rounded-2xl border border-dashed border-zinc-700 text-center p-16"><span class="text-4xl text-zinc-600">⇩</span><h2 class="text-white font-bold mt-4">فایلی پیدا نشد</h2><p class="text-sm text-zinc-500 mt-2">دسته‌بندی یا عبارت جست‌وجو را تغییر دهید؛ فایل‌های منتشرشده اینجا نمایش داده می‌شوند.</p></div>@endforelse
    </div>
    {{ $items->links() }}
</div>
