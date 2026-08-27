<!DOCTYPE html>
<html lang="fa" class="dark" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? $brand_name }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
        /* جلوگیری از پرش محتوای Alpine.js قبل از لود شدن */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 antialiased selection:bg-orange-500 selection:text-white min-h-screen flex flex-col relative" x-data="{ mobileMenuOpen: false }">

<!-- Ambient Premium Network Background -->
<div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
    <!-- Subtle Tech Grid -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff03_1px,transparent_1px),linear-gradient(to_bottom,#ffffff03_1px,transparent_1px)] bg-[size:32px_32px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
    <!-- Ambient Glows -->
    <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-orange-600/10 rounded-full blur-[120px] mix-blend-screen animate-pulse" style="animation-duration: 10s;"></div>
    <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] bg-red-600/5 rounded-full blur-[100px] mix-blend-screen animate-pulse" style="animation-duration: 15s;"></div>
</div>

<!-- Premium Navbar -->
<nav class="sticky top-0 z-50 w-full bg-zinc-950/70 backdrop-blur-2xl border-b border-white/[0.05] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.7)] transition-all duration-300">
    <div class="max-w-7xl mx-auto px-5 lg:px-8 h-[72px] flex justify-between items-center relative">

        <!-- Logo & Brand -->
        <a href="/" class="flex items-center gap-3 group relative z-10" wire:navigate>
            <div class="w-11 h-11 bg-gradient-to-br from-orange-500/10 to-red-500/5 border border-orange-500/20 rounded-2xl flex items-center justify-center group-hover:from-orange-500/20 group-hover:to-red-500/10 transition-all duration-300 overflow-hidden shrink-0 shadow-[0_0_15px_rgba(249,115,22,0.1)] group-hover:shadow-[0_0_25px_rgba(249,115,22,0.2)]">
                @if($brand_logo)
                    <img src="{{ $brand_logo }}" alt="{{ $brand_name }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-6 h-6 text-orange-500 transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 22l10-6 10 6L12 2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V9"></path>
                    </svg>
                @endif
            </div>
            <span class="text-xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-l from-orange-400 via-orange-500 to-red-500">
                    {{ $brand_name }}
                </span>
        </a>

        <!-- Desktop Navigation Capsule -->
        <div class="hidden md:flex items-center gap-1 bg-zinc-900/40 p-1.5 rounded-2xl border border-white/[0.05] shadow-inner relative z-10">
            <a href="{{route('store.index')}}" class="px-5 py-2 text-[13px] font-bold text-zinc-300 hover:text-orange-400 hover:bg-orange-500/10 rounded-xl transition-all duration-200" wire:navigate>
                فروشگاه
            </a>
            <a href="{{route('tutorials.index')}}" class="px-5 py-2 text-[13px] font-bold text-zinc-300 hover:text-orange-400 hover:bg-orange-500/10 rounded-xl transition-all duration-200" wire:navigate>
                آموزش‌ها
            </a>
            <div class="w-px h-5 bg-white/[0.08] mx-1"></div>
            <!-- Status Badge inside Nav -->
            <a href="/status" class="flex items-center gap-2.5 px-5 py-2 text-[13px] font-bold text-zinc-300 hover:text-emerald-400 hover:bg-emerald-500/10 rounded-xl transition-all duration-200" wire:navigate>
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" style="animation-duration: 2s;"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                    </span>
                شبکه پایدار
            </a>
        </div>

        <!-- Auth / User Capsule (Desktop) -->
        <div class="hidden md:flex items-center relative z-10">
            @auth
                <div class="flex items-center gap-1 bg-zinc-900/40 p-1.5 rounded-2xl border border-white/[0.05] shadow-inner">
                    <a href="{{ route('login') }}" class="flex items-center gap-2 px-4 py-2 text-[13px] font-bold text-white hover:text-orange-400 rounded-xl hover:bg-orange-500/10 transition-all duration-200" wire:navigate>
                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        حساب کاربری
                    </a>
                    <div class="w-px h-4 bg-white/[0.08] mx-1"></div>
                    <form action="/logout" method="POST" class="inline m-0 p-0">
                        @csrf
                        <button type="submit" class="p-2 text-zinc-500 hover:text-red-400 hover:bg-red-500/10 rounded-xl transition-all duration-200" title="خروج از حساب">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="px-6 py-2.5 text-[13px] font-bold text-white bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl border border-orange-400/20 shadow-[0_0_20px_rgba(249,115,22,0.15)] hover:shadow-[0_0_25px_rgba(249,115,22,0.3)] hover:from-orange-400 hover:to-orange-500 transition-all duration-300 hover:-translate-y-0.5" wire:navigate>
                    ورود / ثبت‌نام
                </a>
            @endauth
        </div>

        <!-- Mobile Hamburger Menu Button -->
        <div class="flex items-center md:hidden relative z-10">
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-2 text-zinc-400 hover:text-white hover:bg-white/5 rounded-xl transition-colors focus:outline-none" aria-label="منوی کاربری">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden absolute top-[72px] left-0 w-full bg-zinc-950/95 backdrop-blur-3xl border-b border-white/[0.05] shadow-2xl"
         x-cloak>
        <div class="px-5 py-6 flex flex-col gap-2">
            <a href="{{route('store.index')}}" class="px-4 py-3 text-sm font-bold text-zinc-300 hover:text-orange-400 hover:bg-orange-500/10 rounded-xl transition-colors" wire:navigate @click="mobileMenuOpen = false">فروشگاه</a>
            <a href="{{route('tutorials.index')}}" class="px-4 py-3 text-sm font-bold text-zinc-300 hover:text-orange-400 hover:bg-orange-500/10 rounded-xl transition-colors" wire:navigate @click="mobileMenuOpen = false">آموزش‌ها</a>
            <a href="/status" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-zinc-300 hover:text-emerald-400 hover:bg-emerald-500/10 rounded-xl transition-colors" wire:navigate @click="mobileMenuOpen = false">
                وضعیت سرورها
                <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
            </a>

            <div class="h-px w-full bg-white/[0.05] my-2"></div>

            @auth
                <a href="{{ route('login') }}" class="px-4 py-3 text-sm font-bold text-white bg-white/5 rounded-xl hover:bg-white/10 transition-colors flex items-center justify-between" wire:navigate @click="mobileMenuOpen = false">
                    حساب کاربری
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <form action="/logout" method="POST" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full text-right px-4 py-3 text-sm font-bold text-red-400 hover:bg-red-500/10 rounded-xl transition-colors">خروج از حساب</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mt-2 text-center w-full px-6 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl shadow-[0_0_20px_rgba(249,115,22,0.2)]" wire:navigate @click="mobileMenuOpen = false">
                    ورود / ثبت‌نام
                </a>
            @endauth
        </div>
    </div>
</nav>

<!-- Main Content Area -->
<main class="flex-grow relative z-10 w-full flex flex-col">
    {{ $slot }}
</main>

<!-- Premium Footer -->
<footer class="relative z-10 border-t border-white/[0.05] bg-zinc-950/60 backdrop-blur-xl pt-12 pb-8 mt-auto">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-8 mb-8">

            <div class="flex flex-col items-center md:items-start gap-4">
                <div class="flex items-center gap-2 opacity-80 hover:opacity-100 transition-opacity">
                    @if($brand_logo)
                        <img src="{{ $brand_logo }}" alt="{{ $brand_name }}" class="w-6 h-6 object-cover rounded-md">
                    @else
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 22l10-6 10 6L12 2z"></path>
                        </svg>
                    @endif
                    <span class="text-lg font-black text-white">{{ $brand_name }}</span>
                </div>
                <p class="text-zinc-500 text-[13px] text-center md:text-right max-w-sm font-medium leading-relaxed">
                    شبکه‌ای سریع، پایدار و امن برای تجربه بهتر اینترنت و Gaming. طراحی شده برای پایداری در شرایط مختلف.
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-6 text-[13px] font-bold text-zinc-400">
                <a href="{{route('store.index')}}" class="hover:text-orange-400 transition-colors" wire:navigate>فروشگاه</a>
                <a href="{{route('tutorials.index')}}" class="hover:text-orange-400 transition-colors" wire:navigate>آموزش‌ها</a>
                <a href="/status" class="hover:text-orange-400 transition-colors" wire:navigate>وضعیت سرورها</a>
                <!-- اگر در آینده صفحه پشتیبانی اضافه شد لینک آن را در زیر قرار دهید -->
                <!-- <a href="#" class="hover:text-orange-400 transition-colors">پشتیبانی</a> -->
            </div>
        </div>

        <div class="pt-6 border-t border-white/[0.05] flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[11px] font-medium text-zinc-600">
                &copy; {{ date('Y') }} {{ $brand_name }}. تمامی حقوق محفوظ است.
            </p>
            <div class="flex items-center gap-2 text-[11px] font-bold text-zinc-500 bg-white/[0.02] px-3 py-1.5 rounded-lg border border-white/[0.02]">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-50"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                    </span>
                Premium Network Active
            </div>
        </div>
    </div>
</footer>

</body>
</html>
