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
@php
    $user = auth()->user();

    $navItem = 'inline-flex items-center gap-2 px-3 py-2.5 rounded-xl text-[13px] font-bold transition-all duration-200';
    $navActive = 'bg-brand-500/10 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300';
    $navInactive = 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-400 dark:hover:bg-zinc-800/80 dark:hover:text-white';

    $mobileItem = 'flex items-center gap-3 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all';
@endphp

<header
    dir="rtl"
    x-data="{
        mobileMenuOpen: false,
        profileOpen: false
    }"
    @keydown.escape.window="mobileMenuOpen = false; profileOpen = false"
    class="sticky top-0 z-50 border-b border-zinc-200/80 bg-white/90 shadow-sm backdrop-blur-xl transition-colors dark:border-zinc-800/80 dark:bg-[#111c2e]/90 dark:shadow-none"
>
    <div class="mx-auto flex h-20 max-w-[1440px] items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">

        {{-- سمت راست: منوی موبایل، برند و ناوبری --}}
        <div class="flex min-w-0 items-center gap-3 lg:gap-7">

            {{-- دکمه منوی موبایل --}}
            <button
                type="button"
                aria-label="منوی اصلی"
                :aria-expanded="mobileMenuOpen"
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white md:hidden"
            >
                <svg x-show="!mobileMenuOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>

                <svg x-show="mobileMenuOpen" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- برند --}}
            <a href="/" wire:navigate class="shrink-0 transition-opacity hover:opacity-80">
                <x-ui.brand
                    :name="$brand_name ?? 'همراه سیمرغ'"
                    :logo="$brand_logo ?? null"
                    subtitle="پنل کاربری"
                />
            </a>

            {{-- ناوبری دسکتاپ --}}
            <nav class="hidden items-center gap-1 lg:flex">
                <a
                    href="{{ route('customer.dashboard') }}"
                    wire:navigate
                    class="{{ $navItem }} {{ request()->routeIs('customer.dashboard') ? $navActive : $navInactive }}"
                >
                    <svg class="h-4 w-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    سرویس‌های من
                </a>

                <a
                    href="{{ route('customer.orders') }}"
                    wire:navigate
                    class="{{ $navItem }} {{ request()->routeIs('customer.orders') ? $navActive : $navInactive }}"
                >
                    <svg class="h-4 w-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    سفارشات من
                </a>

                <a
                    href="{{ route('customer.wallet') }}"
                    wire:navigate
                    class="{{ $navItem }} {{ request()->routeIs('customer.wallet') ? $navActive : $navInactive }}"
                >
                    <svg class="h-4 w-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7.5A2.5 2.5 0 015.5 5H19a2 2 0 012 2v10a2 2 0 01-2 2H5.5A2.5 2.5 0 013 16.5v-9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 13h2"/>
                    </svg>
                    کیف پول
                </a>

                <a
                    href="{{ route('support.index') }}"
                    wire:navigate
                    class="{{ $navItem }} {{ request()->routeIs('support.*') ? $navActive : $navInactive }}"
                >
                    <svg class="h-4 w-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 10a6 6 0 10-12 0v1a3 3 0 003 3h1v-4H7m11 0h-1v4h1a3 3 0 003-3v-1"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20h2a3 3 0 003-3v-1"/>
                    </svg>
                    پشتیبانی
                </a>

                <a
                    href="{{ route('downloads.index') }}"
                    wire:navigate
                    class="{{ $navItem }} {{ request()->routeIs('downloads.*') ? $navActive : $navInactive }}"
                >
                    <svg class="h-4 w-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0l4-4m-4 4l-4-4M5 21h14"/>
                    </svg>
                    مرکز دانلود
                </a>
            </nav>
        </div>

        {{-- سمت چپ: خرید، اعلان و پروفایل --}}
        <div class="flex shrink-0 items-center gap-2 sm:gap-3">

            @if(session()->has('impersonating_admin_id'))
                <form method="POST" action="{{ route('impersonation.leave') }}" class="hidden xl:block">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-500/20 dark:text-amber-300"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        بازگشت به مدیریت
                    </button>
                </form>
            @endif

            <livewire:shared.notification-bell />

            <a
                href="{{ route('store.index') }}"
                wire:navigate
                class="hidden h-10 items-center gap-2 rounded-xl bg-brand-600 px-3.5 text-xs font-bold text-white shadow-sm shadow-brand-600/20 transition hover:bg-brand-700 hover:shadow-md sm:inline-flex"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                خرید سرویس
            </a>

            {{-- پروفایل --}}
            <div class="relative" @click.away="profileOpen = false">
                <button
                    type="button"
                    aria-label="منوی حساب کاربری"
                    :aria-expanded="profileOpen"
                    @click="profileOpen = !profileOpen"
                    class="flex h-11 items-center gap-2 rounded-2xl border border-zinc-200 bg-zinc-50 px-1.5 pl-2.5 transition hover:border-zinc-300 hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-zinc-700/80 dark:bg-zinc-800/60 dark:hover:border-zinc-600 dark:hover:bg-zinc-800"
                >
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600 dark:text-brand-300">
                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>

                    <span class="hidden max-w-28 truncate text-xs font-bold text-zinc-800 dark:text-zinc-100 sm:block">
                        {{ $user->name ?? 'مشتری' }}
                    </span>

                    <svg class="hidden h-4 w-4 text-zinc-400 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/>
                    </svg>
                </button>

                <div
                    x-show="profileOpen"
                    x-cloak
                    x-transition.origin.top.right
                    class="absolute left-0 top-14 z-50 w-64 overflow-hidden rounded-2xl border border-zinc-200 bg-white p-2 shadow-2xl shadow-zinc-900/10 dark:border-zinc-700 dark:bg-[#111c2e] dark:shadow-black/30"
                >
                    <div class="mb-1 flex items-center gap-3 rounded-xl bg-zinc-50 px-3 py-3 dark:bg-zinc-800/70">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600 dark:text-brand-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-zinc-900 dark:text-white">
                                {{ $user->name ?? 'مشتری' }}
                            </p>
                            <p class="mt-0.5 text-[11px] text-zinc-500 dark:text-zinc-400">
                                حساب کاربری
                            </p>
                        </div>
                    </div>

                    <div class="my-2 h-px bg-zinc-100 dark:bg-zinc-800"></div>

                    <form method="POST" action="{{ route('customer.logout') }}" onsubmit="return confirm('آیا مایل به خروج هستید؟');">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-xs font-bold text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-500/10"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            خروج از حساب کاربری
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- لایه تاریک پشت منوی موبایل --}}
    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition.opacity
        @click="mobileMenuOpen = false"
        class="fixed inset-0 top-20 z-40 bg-zinc-950/30 backdrop-blur-[2px] md:hidden"
    ></div>

    {{-- منوی موبایل --}}
    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-3"
        class="absolute inset-x-0 top-20 z-50 border-b border-zinc-200 bg-white p-4 shadow-xl dark:border-zinc-800 dark:bg-[#111c2e] md:hidden"
    >
        <nav class="space-y-1.5">

            <a
                href="{{ route('customer.dashboard') }}"
                wire:navigate
                @click="mobileMenuOpen = false"
                class="{{ $mobileItem }} {{ request()->routeIs('customer.dashboard') ? $navActive : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
            >
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-current/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </span>
                سرویس‌های من
            </a>

            <a
                href="{{ route('customer.orders') }}"
                wire:navigate
                @click="mobileMenuOpen = false"
                class="{{ $mobileItem }} {{ request()->routeIs('customer.orders') ? $navActive : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
            >
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-current/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </span>
                سفارشات من
            </a>

            <a
                href="{{ route('customer.wallet') }}"
                wire:navigate
                @click="mobileMenuOpen = false"
                class="{{ $mobileItem }} {{ request()->routeIs('customer.wallet') ? $navActive : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
            >
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-current/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 015.5 5H19a2 2 0 012 2v10a2 2 0 01-2 2H5.5A2.5 2.5 0 013 16.5v-9z"/>
                    </svg>
                </span>
                کیف پول و شارژ خودکار
            </a>

            <a
                href="{{ route('support.index') }}"
                wire:navigate
                @click="mobileMenuOpen = false"
                class="{{ $mobileItem }} {{ request()->routeIs('support.*') ? $navActive : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
            >
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-current/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M18 10a6 6 0 10-12 0v1a3 3 0 003 3h1v-4H7m11 0h-1v4h1a3 3 0 003-3v-1"/>
                    </svg>
                </span>
                پشتیبانی
            </a>

            <a
                href="{{ route('downloads.index') }}"
                wire:navigate
                @click="mobileMenuOpen = false"
                class="{{ $mobileItem }} {{ request()->routeIs('downloads.*') ? $navActive : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
            >
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-current/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4-4m-4 4l-4-4M5 21h14"/>
                    </svg>
                </span>
                مرکز دانلود
            </a>

            <div class="my-3 h-px bg-zinc-100 dark:bg-zinc-800"></div>

            <a
                href="{{ route('store.index') }}"
                wire:navigate
                @click="mobileMenuOpen = false"
                class="flex items-center justify-center gap-2 rounded-2xl bg-brand-600 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                خرید سرویس جدید
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
