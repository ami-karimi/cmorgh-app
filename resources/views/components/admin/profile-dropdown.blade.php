<div class="relative cursor-pointer" x-data="{ open: false }" @click.outside="open = false">

    <button @click="open = !open" class="cursor-pointer flex items-center gap-3 pl-2  border-zinc-800 focus:outline-none transition hover:opacity-80">
        <div class="hidden sm:block text-left">
            <p class="text-sm font-bold text-white leading-none">{{ auth()->user()->name ?? 'مدیر سیستم' }}</p>
            <p class="text-xs text-zinc-500 mt-1 uppercase tracking-wider">Admin</p>
        </div>
        <img class="w-10 h-10 rounded-full border-2 border-orange-500/50 object-cover"
             src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=f97316&color=fff&font-size=0.33"
             alt="Avatar">
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
         class="absolute left-0 mt-4 w-52 bg-zinc-900 border border-zinc-800 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] py-2 z-50 overflow-hidden"
         x-cloak>

        <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-300 hover:text-white hover:bg-zinc-800/80 transition-colors">
            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            مدیریت حساب
        </a>

        <div class="h-px bg-zinc-800/80 my-1"></div>

        <button wire:click="logout" class="cursor-pointer w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-500/10 transition-colors text-right focus:outline-none group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            خروج از سیستم
        </button>
    </div>
</div>
