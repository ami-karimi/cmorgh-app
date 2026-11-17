@if ($paginator->hasPages())
    <nav dir="rtl" role="navigation" aria-label="صفحه‌بندی" class="flex flex-wrap items-center justify-between gap-3">

        <p class="text-xs text-zinc-500">
            نمایش
            <span class="font-bold text-zinc-300">{{ $paginator->firstItem() }}</span>
            تا
            <span class="font-bold text-zinc-300">{{ $paginator->lastItem() }}</span>
            از
            <span class="font-bold text-zinc-300">{{ $paginator->total() }}</span>
            مورد
        </p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-zinc-800 text-zinc-700">›</span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-zinc-800 bg-zinc-900 text-zinc-300 transition hover:border-brand-500/50 hover:text-white">›</button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="flex h-9 min-w-9 items-center justify-center rounded-xl border border-zinc-800 px-2 text-xs text-zinc-600">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="flex h-9 min-w-9 items-center justify-center rounded-xl bg-brand-500 px-2 text-xs font-bold text-white">{{ $page }}</span>
                        @else
                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                                    class="flex h-9 min-w-9 items-center justify-center rounded-xl border border-zinc-800 bg-zinc-900 px-2 text-xs text-zinc-400 transition hover:border-brand-500/50 hover:text-white">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-zinc-800 bg-zinc-900 text-zinc-300 transition hover:border-brand-500/50 hover:text-white">‹</button>
            @else
                <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-zinc-800 text-zinc-700">‹</span>
            @endif
        </div>
    </nav>
@endif
