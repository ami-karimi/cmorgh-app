<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>{{ $title ?? 'پنل نمایندگی | پلتفرم دیجیتال' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .font-mono-digit { font-family: 'JetBrains Mono', monospace, sans-serif; }
        /* جلوگیری از پرش المان‌های Alpine.js قبل از لود شدن کامل صفحه */
        [x-cloak] { display: none !important; }
        /* استایل اسکرول‌بار سفارشی */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #52525b; }
    </style>
</head>
<body class="bg-[#0b0f19] text-zinc-100 font-sans antialiased selection:bg-orange-500/30">

<div class="flex h-[100dvh] w-full overflow-hidden">

    <div x-cloak
         x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 lg:hidden">
    </div>

    <aside class="fixed inset-y-0 right-0 z-50 w-72 bg-[#111827] border-l border-zinc-800 flex flex-col shadow-2xl lg:shadow-none lg:static lg:z-40 transition-transform duration-300 ease-in-out transform"
           :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">

        <div class="h-20 flex items-center justify-between px-6 border-b border-zinc-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 flex items-center justify-center shadow-lg shadow-orange-500/20 text-white font-black text-lg">
                    S
                </div>
                <div>
                    <span class="block font-black text-base tracking-tight text-white">سیمرغ <span class="text-orange-500">پرو</span></span>
                    <span class="block text-[10px] text-zinc-400 font-bold uppercase tracking-wider">Digital Panel</span>
                </div>
            </div>

            <button @click="sidebarOpen = false" class="p-2 rounded-xl text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800 lg:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            <p class="px-3 text-[11px] font-black text-zinc-500 uppercase tracking-wider mb-2">منوی اصلی</p>

            <a wire:navigate href="{{ route('reseller.dashboard') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all {{ request()->routeIs('reseller.dashboard') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-200' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('reseller.dashboard') ? 'text-white' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="text-sm">داشبورد و آمار</span>
            </a>

            <a wire:navigate href="{{ route('reseller.accounts.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all {{ request()->routeIs('reseller.accounts.index') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-200' }}"><svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg><span class="text-sm">اکانت‌های سرویس</span></a>
            <a wire:navigate href="{{ route('reseller.customers') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all {{ request()->routeIs('reseller.customers') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-200' }}"><svg class="w-5 h-5 {{ request()->routeIs('reseller.customers') ? 'text-white' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span class="text-sm">مدیریت مشتریان</span></a>
            <a href="{{ route('reseller.financial') }}" wire:navigate @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all {{ request()->routeIs('reseller.financial') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-200' }}"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-sm">امور مالی و کیف پول</span></a>
            <a href="{{ route('reseller.store.orders') }}" wire:navigate @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all {{ request()->routeIs('reseller.store*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-200' }}"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg><span class="text-sm">مدیریت سفارشات</span></a>
            <a href="{{ route('reseller.sub-agents') }}" wire:navigate @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all {{ request()->routeIs('reseller.sub-agents*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-200' }}"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><span class="text-sm">مدیریت زیر‌نمایندگان</span></a>
            <a wire:navigate href="{{ route('reseller.settings') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all {{ request()->routeIs('reseller.settings') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-200' }}"><svg class="w-5 h-5 {{ request()->routeIs('reseller.settings') ? 'text-white' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span class="text-sm">تنظیمات و فروشگاه</span></a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

        <header class="h-20 bg-[#111827]/80 backdrop-blur-md border-b border-zinc-800 flex items-center justify-between px-4 lg:px-8 shrink-0 z-30">

            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="p-2.5 rounded-xl bg-zinc-800 text-zinc-300 hover:text-orange-500 transition-colors lg:hidden" title="باز کردن منو">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <h1 class="hidden sm:block text-lg font-black tracking-tight text-white">پنل مدیریت نماینده</h1>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 flex-1 sm:flex-none justify-end">

                <livewire:agent.global-search />

                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="relative p-2.5 rounded-xl bg-zinc-800 text-zinc-300 hover:text-orange-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-2 right-2.5 w-2 h-2 bg-rose-500 rounded-full animate-ping"></span>
                        <span class="absolute top-2 right-2.5 w-2 h-2 bg-rose-500 rounded-full"></span>
                    </button>

                    <div x-cloak x-show="open" x-transition.origin.top.left class="absolute left-0 mt-3 w-72 bg-[#111827] border border-zinc-800 rounded-2xl shadow-xl z-50 overflow-hidden">
                        <div class="p-4 border-b border-zinc-800 flex justify-between items-center bg-zinc-900/50">
                            <h3 class="text-xs font-black text-zinc-200">اعلان‌های سیستم</h3>
                            <span class="text-[10px] bg-orange-500/10 text-orange-500 px-2 py-0.5 rounded-full font-bold">2 جدید</span>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-zinc-800/80">
                            <a href="#" class="block p-4 hover:bg-zinc-800/50 transition-colors">
                                <p class="text-xs font-bold text-zinc-300">تمدید موفقیت‌آمیز اکانت</p>
                                <p class="text-[10px] text-zinc-500 mt-1">اکانت user_102 با موفقیت به مدت 30 روز تمدید شد.</p>
                            </a>
                            <a href="#" class="block p-4 hover:bg-zinc-800/50 transition-colors">
                                <p class="text-xs font-bold text-zinc-300">هشدار موجودی</p>
                                <p class="text-[10px] text-zinc-500 mt-1">موجودی کیف پول شما کمتر از 50 هزار تومان است.</p>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center gap-2 p-1.5 pr-3 rounded-xl bg-zinc-900/80 border border-zinc-800 hover:bg-zinc-800 transition-all focus:outline-none">
                        <span class="text-xs font-bold text-zinc-300 hidden md:block">{{ auth()->user()->name ?? 'نماینده' }}</span>
                        <div class="w-8 h-8 rounded-lg bg-orange-500/10 text-orange-500 font-bold flex items-center justify-center text-xs">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                    </button>

                    <div x-cloak x-show="open" x-transition.origin.top.left class="absolute left-0 mt-3 w-56 bg-[#111827] border border-zinc-800 rounded-2xl shadow-xl z-50 overflow-hidden p-1.5">
                        <a href="{{ route('reseller.profile.show') ?? '#' }}" class="flex items-center gap-2 px-3 py-2.5 text-xs font-bold text-zinc-300 hover:bg-zinc-800 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            تنظیمات حساب و نشست‌ها
                        </a>

                        <div class="h-px bg-zinc-800 my-1"></div>

                        <form method="POST" action="{{ route('reseller.logout') }}" onsubmit="return confirm('آیا برای خروج از حساب کاربری مطمئن هستید؟');">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 text-xs font-bold text-rose-500 hover:bg-rose-500/10 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                خروج از سیستم
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-8 h-full relative">
            <div class="max-w-7xl mx-auto z-10 relative">
                {{ $slot }}
            </div>
        </main>

    </div>
</div>
@livewireScripts
</body>
</html>
