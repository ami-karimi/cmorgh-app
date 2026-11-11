<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'پنل کاربری من' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .font-mono-digit { font-family: 'IRANSans', sans-serif; font-variant-numeric: tabular-nums; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="ui-shell bg-zinc-50 dark:bg-[#080f1d] text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-300">

<a href="#main-content" class="ui-skip-link">رفتن به محتوای اصلی</a>
<!-- Navigation / Header -->
<header class="bg-white/80 dark:bg-[#111c2e]/80 backdrop-blur-xl border-b border-zinc-200 dark:border-zinc-800/80 sticky top-0 z-50 transition-all shadow-sm dark:shadow-none" x-data="{ mobileMenuOpen: false, profileOpen: false }" @keydown.escape.window="mobileMenuOpen = false; profileOpen = false">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-[72px] flex items-center justify-between gap-4">

        <!-- Right: Logo & Links -->
        <div class="flex items-center gap-6">
            <!-- Mobile Menu Toggle -->
            <button type="button" aria-label="منوی اصلی" :aria-expanded="mobileMenuOpen" @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 -mr-2 rounded-xl text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition md:hidden focus:outline-none">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Brand -->
            <a href="/" wire:navigate class="flex items-center gap-2.5">
                <x-ui.brand :name="$brand_name ?? 'همراه سیمرغ'" :logo="$brand_logo ?? null" subtitle="پنل کاربری" />
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center gap-1.5">
                <a href="{{ route('customer.dashboard') }}" wire:navigate class="px-3 py-2 rounded-lg text-sm font-bold transition-all {{ request()->routeIs('customer.dashboard') ? 'bg-zinc-100 dark:bg-zinc-800/80 text-brand-600 dark:text-brand-300' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                    سرویس‌های من
                </a>
                <a href="{{ route('customer.orders') }}" wire:navigate class="px-3 py-2 rounded-lg text-sm font-bold transition-all {{ request()->routeIs('customer.orders') ? 'bg-zinc-100 dark:bg-zinc-800/80 text-brand-600 dark:text-brand-300' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                    سفارشات من
                </a>

                <a href="{{ route('customer.wallet') }}" wire:navigate class="px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('customer.wallet') ? 'text-brand-400 bg-brand-500/10' : 'text-zinc-500' }}">کیف پول و شارژ خودکار</a>
            </nav>
        </div>

        <!-- Left: CTA & Profile -->
        <div class="flex items-center gap-3">
            <a href="{{ route('store.index') }}" wire:navigate class="ui-button hidden sm:flex text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                خرید سرویس جدید
            </a>

            <!-- Profile Dropdown -->
            <div class="relative" @click.away="profileOpen = false">
                <button type="button" aria-label="منوی حساب کاربری" :aria-expanded="profileOpen" @click="profileOpen = !profileOpen" class="flex items-center gap-2 p-1.5 pr-3 rounded-xl bg-zinc-100 dark:bg-zinc-800/50 hover:bg-zinc-200 dark:hover:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700/50 transition-colors focus:outline-none">
                    <span class="text-xs font-bold hidden sm:inline">{{ auth()->user()->name ?? 'مشتری' }}</span>
                    <div class="w-7 h-7 rounded-lg bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </button>

                <div x-show="profileOpen" x-transition x-cloak class="absolute top-12 left-0 w-56 bg-white dark:bg-[#111c2e] border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl z-50 overflow-hidden py-2">
                    <div class="px-4 py-2 border-b border-zinc-100 dark:border-zinc-800 mb-1 sm:hidden">
                        <p class="text-xs font-bold text-zinc-900 dark:text-white">{{ auth()->user()->name ?? 'مشتری' }}</p>
                    </div>
                    <form method="POST" action="{{ route('customer.logout') }}" onsubmit="return confirm('آیا مایل به خروج هستید؟');">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            خروج از حساب کاربری
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden absolute top-[72px] left-0 w-full bg-white dark:bg-[#111c2e] border-b border-zinc-200 dark:border-zinc-800 shadow-xl z-40">
        <nav class="flex flex-col p-4 space-y-1.5 text-sm font-bold">
            <a href="{{ route('store.index') }}" wire:navigate @click="mobileMenuOpen = false" class="ui-button mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                خرید سرویس جدید
            </a>
            <a href="{{ route('customer.wallet') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-brand-400">کیف پول و شارژ خودکار</a>
            <a href="{{ route('customer.dashboard') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('customer.dashboard') ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-300' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                سرویس‌های من
            </a>
            <a href="{{ route('customer.orders') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('customer.orders') ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-300' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                سفارشات من
            </a>

        </nav>
    </div>
</header>

<main id="main-content" tabindex="-1" class="ui-content flex-1 w-full max-w-6xl mx-auto p-4 sm:p-6 lg:py-8 z-10 relative">
    {{ $slot }}
</main>

@include('layouts.partials.ui-feedback')
@livewireScripts
</body>
</html>
