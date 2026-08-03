<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'پنل کاربری من' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .font-mono-digit { font-family: 'JetBrains Mono', monospace, sans-serif; }
    </style>
</head>
<body class="bg-zinc-50 dark:bg-[#09090b] text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-300">

<header class="bg-white/80 dark:bg-[#111827]/80 backdrop-blur-xl border-b border-zinc-200 dark:border-zinc-800/80 sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

        <div class="flex items-center gap-4 sm:gap-6">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 -ml-2 rounded-xl text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800/50 transition md:hidden">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="mobileMenuOpen" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="flex items-center gap-2">
                @if(isset($brand_logo) && $brand_logo)
                    <img src="{{ $brand_logo }}" alt="{{ $brand_name ?? 'برند' }}" class="w-8 h-8 rounded-lg object-cover border border-zinc-200 dark:border-zinc-800 shadow-sm">
                @else
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-black text-sm shadow-sm">
                        {{ mb_substr($brand_name ?? 'C', 0, 1) }}
                    </div>
                @endif
                <span class="font-black tracking-tight text-sm text-zinc-900 dark:text-white hidden sm:block">
                        {{ $brand_name ?? 'پنل کاربری' }}
                    </span>
            </div>

            <nav class="hidden md:flex items-center gap-4 text-sm font-bold">
                <a href="{{ route('customer.dashboard') }}" wire:navigate class="{{ request()->routeIs('customer.dashboard') ? 'text-zinc-900 dark:text-white border-b-2 border-emerald-500 pb-1' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition' }}">
                    سرویس‌های من
                </a>

                <a href="{{ route('customer.orders') }}" wire:navigate class="{{ request()->routeIs('customer.orders') ? 'text-zinc-900 dark:text-white border-b-2 border-emerald-500 pb-1' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition' }}">
                    سفارشات من
                </a>

                <a href="#" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition">پشتیبانی</a>
            </nav>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">

            <a href="{{ route('store.index') }}" wire:navigate class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 hover:scale-[1.02] active:scale-[0.98] transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>سفارش سرویس جدید</span>
            </a>

            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="flex items-center gap-2 p-1.5 pr-3 rounded-xl bg-zinc-100 dark:bg-zinc-900/50 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition border border-zinc-200/50 dark:border-zinc-800/50">
                    <span class="text-xs font-bold hidden sm:inline">{{ auth()->user()->name ?? 'مشتری عزیز' }}</span>
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" style="display: none;" class="absolute top-12 left-0 w-48 bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl z-50 overflow-hidden p-1.5">
                    <form method="POST" action="{{route('customer.logout')}}" onsubmit="return confirm('آیا مایل به خروج هستید؟');">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-bold text-rose-500 hover:bg-rose-500/10 rounded-xl transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            خروج از حساب
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div x-show="mobileMenuOpen"
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden absolute top-16 left-0 w-full bg-white dark:bg-[#111827] border-b border-zinc-200 dark:border-zinc-800 shadow-xl z-40">
        <nav class="flex flex-col p-4 space-y-2 text-sm font-bold">

            <a href="{{ route('customer.dashboard') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-500/20 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                سفارش سرویس جدید
            </a>

            <a href="{{ route('customer.dashboard') }}" wire:navigate @click="mobileMenuOpen = false" class="px-4 py-3 rounded-xl {{ request()->routeIs('customer.dashboard') ? 'bg-zinc-100 dark:bg-zinc-800 text-emerald-600 dark:text-emerald-400' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                سرویس‌های من
            </a>

            <a href="{{ route('customer.orders') }}" wire:navigate @click="mobileMenuOpen = false" class="px-4 py-3 rounded-xl {{ request()->routeIs('customer.orders') ? 'bg-zinc-100 dark:bg-zinc-800 text-emerald-600 dark:text-emerald-400' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                سفارشات من
            </a>

            <a href="#" @click="mobileMenuOpen = false" class="px-4 py-3 rounded-xl text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                پشتیبانی
            </a>
        </nav>
    </div>
</header>

<main class="flex-1 w-full max-w-6xl mx-auto p-4 sm:p-6 lg:py-8 z-10 relative">
    {{ $slot }}
</main>

@livewireScripts
</body>
</html>
