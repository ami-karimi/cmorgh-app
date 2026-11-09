<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button type="button" class="ui-icon-button" @click="open = !open" :aria-expanded="open" aria-label="آخرین اطلاعیه‌ها">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
    </button>
    <section x-show="open" x-cloak x-transition class="absolute left-0 mt-3 w-[min(320px,calc(100vw-2rem))] rounded-xl border border-zinc-800 bg-zinc-900 shadow-xl z-50 overflow-hidden" aria-label="اطلاعیه‌ها">
        <div class="p-4 border-b border-zinc-800 flex justify-between items-center">
            <h3 class="font-bold text-sm">آخرین اطلاعیه‌ها</h3>
            <span class="text-xs text-zinc-400">{{ $announcements->count() }} اطلاعیه</span>
        </div>
        <div class="max-h-80 overflow-y-auto divide-y divide-zinc-800">
            @forelse($announcements as $announcement)
                <article class="p-4">
                    <h4 class="text-sm font-bold text-zinc-100">{{ $announcement->title }}</h4>
                    <p class="text-xs text-zinc-400 mt-2 leading-7 whitespace-pre-line">{{ $announcement->content }}</p>
                </article>
            @empty
                <div class="p-8 text-center"><p class="text-sm text-zinc-300">اطلاعیه‌ای منتشر نشده است.</p><p class="ui-eyebrow mt-2">اطلاعیه‌های جدید در این بخش نمایش داده می‌شوند.</p></div>
            @endforelse
        </div>
    </section>
</div>
