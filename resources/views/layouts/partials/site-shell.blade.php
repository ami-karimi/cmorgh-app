@php
    $siteName = $brand_name ?? 'همراه سیمرغ';
    $siteLogo = $brand_logo ?? null;
    $accountRoute = match(auth()->user()?->role) {
        'admin', 'manager' => 'admin.dashboard',
        'agent', 'sub_agent' => 'reseller.dashboard',
        'customer' => 'customer.dashboard',
        default => 'login',
    };
@endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080f1d">
    <title>{{ $title ?? $siteName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="ui-shell flex flex-col antialiased" x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false">
<a href="#main-content" class="ui-skip-link">رفتن به محتوای اصلی</a>
<header class="ui-header sticky top-0 z-50">
    <div class="ui-container flex h-[72px] items-center justify-between gap-4">
        <a href="{{ route('main') }}" wire:navigate aria-label="صفحه اصلی {{ $siteName }}"><x-ui.brand :name="$siteName" :logo="$siteLogo" /></a>
        <nav class="hidden md:flex items-center gap-1" aria-label="منوی اصلی">
            <a href="{{ route('store.index') }}" wire:navigate class="ui-nav-link" @if(request()->routeIs('store.*')) aria-current="page" @endif>فروشگاه</a>
            <a href="{{ route('tutorials.index') }}" wire:navigate class="ui-nav-link" @if(request()->routeIs('tutorials.*')) aria-current="page" @endif>راهنمای اتصال</a>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route($accountRoute) }}" wire:navigate class="ui-button text-xs sm:text-sm">{{ auth()->check() ? 'حساب کاربری' : 'ورود / ثبت‌نام' }}</a>
            @auth
                <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                    @csrf
                    <button class="ui-icon-button" type="submit" aria-label="خروج از حساب">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 5H5v14h4m5-12 5 5-5 5m-5-5h10"/></svg>
                    </button>
                </form>
            @endauth
            <button type="button" class="ui-icon-button md:hidden" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen" aria-controls="site-mobile-menu" aria-label="منوی اصلی">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <nav id="site-mobile-menu" x-show="mobileMenuOpen" x-cloak x-transition class="md:hidden border-t border-zinc-800 ui-container py-3" aria-label="منوی موبایل">
        <a href="{{ route('store.index') }}" wire:navigate @click="mobileMenuOpen = false" class="ui-nav-link">فروشگاه</a>
        <a href="{{ route('tutorials.index') }}" wire:navigate @click="mobileMenuOpen = false" class="ui-nav-link">راهنمای اتصال</a>
        @auth
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="ui-nav-link text-rose-400 w-full">خروج از حساب</button></form>
        @endauth
    </nav>
</header>
<main id="main-content" tabindex="-1" class="flex-1 ui-content">{{ $slot }}</main>
<footer class="border-t border-zinc-800 mt-auto py-8">
    <div class="ui-container flex flex-col sm:flex-row justify-between gap-6">
        <div><p class="font-bold text-sm">{{ $siteName }}</p><p class="ui-eyebrow mt-2">مدیریت اشتراک و دسترسی به راهنمای اتصال، در یک جا.</p></div>
        <div class="flex flex-wrap items-center gap-5 text-xs text-zinc-400">
            <a href="{{ route('store.index') }}" wire:navigate class="hover:text-brand-300">خرید اشتراک</a>
            <a href="{{ route('tutorials.index') }}" wire:navigate class="hover:text-brand-300">راهنمای اتصال</a>
            <span>© {{ date('Y') }} {{ $siteName }}</span>
        </div>
    </div>
</footer>
@include('layouts.partials.ui-feedback')
@livewireScripts
</body>
</html>
