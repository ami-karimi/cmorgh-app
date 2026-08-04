<header class="h-24 sticky top-0 z-30 bg-zinc-950/80 backdrop-blur-xl border-b border-zinc-800/80 flex items-center justify-between px-6 lg:px-10">

    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = true" class="lg:hidden p-2 text-zinc-400 hover:text-white bg-zinc-900 rounded-lg border border-zinc-800 focus:outline-none transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
        </button>

        <h2 class="hidden sm:block text-xl font-bold text-white tracking-wide">
            {{ $header ?? 'داشبورد مدیریت' }}
        </h2>
    </div>

    <div class="flex items-center gap-4 sm:gap-6">
        <livewire:admin.global-search />

        <button class="relative p-2 text-zinc-400 hover:text-white transition focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-zinc-950 rounded-full animate-pulse"></span>
        </button>

        <livewire:admin.profile-dropdown />
    </div>
</header>
