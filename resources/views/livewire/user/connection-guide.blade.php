<div class="space-y-6 md:space-y-8 font-sans pb-12 animate-fade-in my-6 md:my-12 px-4 md:px-6">

    <div class="max-w-7xl mx-auto space-y-6 md:space-y-8">

        <div class="text-center max-w-2xl mx-auto space-y-2 md:space-y-3 px-2">
            <span class="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-500 text-[11px] md:text-xs font-black uppercase tracking-wider inline-block">
                مرکز راهنمایی و آموزش
            </span>
            <h1 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">آموزش‌های اتصال به سرویس</h1>
            <p class="text-xs md:text-sm text-zinc-500 dark:text-zinc-400">دستگاه مورد نظر خود را انتخاب کرده و آموزش‌های اختصاصی آن را مشاهده نمایید.</p>
        </div>

        <div class="relative">
            <div class="flex items-center md:justify-center gap-2 md:gap-3 overflow-x-auto pb-3 pt-1 px-1 no-scrollbar scroll-smooth snap-x">
                @php
                    $platforms = [
                        'Android' => ['title' => 'آندروید', 'icon' => 'M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997 0-.551.4482-.9993.9993-.9993.5511 0 .9993.4483.9993.9993 0 .5511-.4482.9997-.9993.9997zm-11.046 0c-.5511 0-.9993-.4486-.9993-.9997 0-.551.4482-.9993.9993-.9993.5511 0 .9993.4483.9993.9993 0 .5511-.4482.9997-.9993.9997zm11.4045-6.02l1.9973-3.4592c.1252-.2167.051-.4935-.1657-.6187-.2167-.1252-.4935-.051-.6187.1657l-2.0227 3.5032c-1.5039-.6873-3.1979-1.0709-5.0217-1.0709-1.8238 0-3.5178.3836-5.0217 1.0709l-2.0227-3.5032c-.1252-.2167-.402-.2909-.6187-.1657-.2167.1252-.2909.402-.1657.6187l1.9973 3.4592c-3.3283 1.8347-5.5907 5.1582-5.8456 9.0805h23.3551c-.2549-3.9223-2.5173-7.2458-5.8456-9.0805z'],
                        'iOS'     => ['title' => 'آیفون (iOS)', 'icon' => 'M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.19c.68-.83 1.14-1.98.99-3.14-.99.04-2.17.66-2.88 1.49-.64.73-1.2 1.92-1.05 3.06 1.1.09 2.22-.57 2.94-1.41z'],
                        'Windows' => ['title' => 'ویندوز', 'icon' => 'M0 3.449L9.75 2.1v9.451H0m10.949-9.602L24 0v11.4H10.949M0 12.6h9.75v9.451L0 20.699M10.949 12.6H24V24l-13.051-1.851'],
                        'macOS'   => ['title' => 'مک‌بوک', 'icon' => 'M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.19c.68-.83 1.14-1.98.99-3.14-.99.04-2.17.66-2.88 1.49-.64.73-1.2 1.92-1.05 3.06 1.1.09 2.22-.57 2.94-1.41z'],
                    ];
                @endphp

                @foreach($platforms as $key => $item)
                    <button wire:click="setPlatform('{{ $key }}')"
                            class="snap-start shrink-0 px-4 md:px-5 py-2.5 md:py-3 rounded-2xl font-bold text-xs md:text-sm transition-all flex items-center gap-2 border {{ $activePlatform === $key ? 'bg-orange-500 text-white border-orange-500 shadow-lg shadow-orange-500/20 scale-102' : 'bg-white dark:bg-[#111827] text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800 hover:border-orange-500/50' }}">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg>
                        <span class="whitespace-nowrap">{{ $item['title'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        @if($availableProtocols->count() > 1)
            <div class="flex items-center md:justify-center gap-1.5 md:gap-2 overflow-x-auto pb-2 no-scrollbar">
                <button wire:click="$set('selectedProtocol', '')" class="whitespace-nowrap px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0 {{ empty($selectedProtocol) ? 'bg-zinc-800 text-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-400 hover:text-zinc-200' }}">
                    همه پروتکل‌ها
                </button>
                @foreach($availableProtocols as $proto)
                    <button wire:click="$set('selectedProtocol', '{{ $proto }}')" class="whitespace-nowrap px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0 {{ $selectedProtocol === $proto ? 'bg-orange-500 text-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-400 hover:text-zinc-200' }}">
                        {{ $proto }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 my-6">
            @forelse($tutorials as $tutorial)
                @php
                    $attachments = is_string($tutorial->attachments) ? json_decode($tutorial->attachments, true) : ($tutorial->attachments ?? []);
                @endphp

                <div x-data="{ expanded: false, isLong: false }"
                     x-init="$nextTick(() => { isLong = $refs.contentBox.scrollHeight > 150 })"
                     class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl p-4 md:p-6 space-y-4 shadow-sm flex flex-col justify-between overflow-hidden transition-all duration-300">

                    <div class="space-y-3 md:space-y-4">
                        <div class="flex items-start justify-between gap-3 border-b border-zinc-100 dark:border-zinc-800/80 pb-3 md:pb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-500 shrink-0"></span>
                                <h3 class="font-black text-sm md:text-base text-zinc-900 dark:text-white leading-snug">{{ $tutorial->title }}</h3>
                            </div>

                            @if($tutorial->protocol)
                                <span class="shrink-0 px-2.5 py-1 rounded-xl bg-orange-500/10 text-orange-500 border border-orange-500/20 text-[10px] font-black uppercase tracking-wider">
                            {{ $tutorial->protocol }}
                        </span>
                            @endif
                        </div>

                        <div class="relative">
                            <div x-ref="contentBox"
                                 :class="expanded ? 'max-h-none' : 'max-h-36 overflow-hidden'"
                                 class="prose dark:prose-invert max-w-none text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed break-words transition-all duration-500">
                                {!! $tutorial->content !!}
                            </div>

                            <div x-show="!expanded && isLong"
                                 class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white dark:from-[#111827] to-transparent pointer-events-none">
                            </div>
                        </div>

                        <template x-if="isLong">
                            <button @click="expanded = !expanded"
                                    class="text-xs font-bold text-orange-500 hover:text-orange-600 transition flex items-center gap-1 mt-1 focus:outline-none">
                                <span x-text="expanded ? 'بستن متن آموزش' : 'مشاهده کامل آموزش...'"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </template>
                    </div>

                    @if(!empty($attachments) && is_array($attachments))
                        <div class="pt-3 md:pt-4 border-t border-zinc-100 dark:border-zinc-800/80 space-y-2">
                            <span class="text-[10px] font-bold text-zinc-400 block mb-1">فایل‌ها و برنامه‌های مرتبط:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($attachments as $file)
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" download
                                       class="py-2.5 px-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-800/80 text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 border border-zinc-200 dark:border-zinc-800">
                                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span class="truncate">دانلود فایل پیوست</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @empty
                <div class="lg:col-span-2 bg-white dark:bg-[#111827] border border-dashed border-zinc-300 dark:border-zinc-800 rounded-3xl p-8 md:p-12 text-center">
                    <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-900 text-zinc-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">آموزشی برای این سیستم‌عامل ثبت نشده است</h3>
                    <p class="text-xs text-zinc-500 mt-1">به محض ثبت آموزش جدید توسط مدیریت، در این بخش قرار خواهد گرفت.</p>
                </div>
            @endforelse
        </div>

    </div>

</div>
