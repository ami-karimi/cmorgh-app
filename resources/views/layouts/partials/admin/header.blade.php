<header class="h-[72px] sticky top-0 z-30 bg-zinc-950/80 backdrop-blur-xl border-b border-zinc-800/80 flex items-center justify-between px-4 lg:px-8 gap-3">

    <div class="flex items-center gap-4">
        <button type="button" aria-label="باز کردن منو" :aria-expanded="sidebarOpen" @click="sidebarOpen = true" class="lg:hidden p-2 text-zinc-400 hover:text-white bg-zinc-900 rounded-lg border border-zinc-800 focus:outline-none transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
        </button>

        <h2 class="hidden sm:block text-sm font-bold text-white tracking-wide">
            {{ $header ?? 'داشبورد مدیریت' }}
        </h2>
    </div>

    <div class="flex items-center gap-2 sm:gap-4 min-w-0">
        <livewire:admin.global-search />

        <a href="{{ route('admin.announcements') }}" wire:navigate class="ui-icon-button shrink-0" aria-label="مدیریت اطلاعیه‌ها"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg></a>

        <livewire:admin.profile-dropdown />
    </div>
</header>
