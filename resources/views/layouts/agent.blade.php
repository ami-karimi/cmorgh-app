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
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar for Modern SaaS Look */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #4B5563; }

        /* Smooth selection */
        ::selection { background: rgba(99,102,241,0.3); color: #fff; }
    </style>
</head>
<body class="bg-[#080B12] text-zinc-100 font-sans antialiased">

<div class="flex h-[100dvh] w-full overflow-hidden">

    <!-- Mobile Sidebar Overlay -->
    <div x-cloak
         x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-[#080B12]/80 backdrop-blur-sm z-40 lg:hidden">
    </div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 right-0 z-50 w-[260px] bg-[#0D111A] border-l border-[#202938] flex flex-col shadow-2xl lg:shadow-none lg:static lg:z-40 transition-transform duration-300 ease-in-out transform"
           :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">

        <!-- Sidebar Header / Branding -->
        <div class="h-[72px] flex items-center justify-between px-5 border-b border-[#202938] shrink-0 bg-[#0D111A]">
            <a href="{{ route('reseller.dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-[0_0_15px_rgba(99,102,241,0.3)] text-white font-black text-lg group-hover:scale-105 transition-transform">
                    S
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-sm tracking-tight text-white leading-tight">سیمرغ <span class="text-indigo-400">پرو</span></span>
                    <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">Reseller Panel</span>
                </div>
            </a>

            <button @click="sidebarOpen = false" class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-300 hover:bg-[#111722] lg:hidden transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto">

            <!-- Group: Dashboard -->
            <div class="space-y-1.5">
                <a wire:navigate href="{{ route('reseller.dashboard') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.dashboard') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-[#111722] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.dashboard'))
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-indigo-500 rounded-l-full shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div>
                    @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="text-sm">داشبورد و آمار</span>
                </a>
            </div>

            <!-- Group: Sales & Customers -->
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2">فروش و مشتریان</p>
                <a wire:navigate href="{{ route('reseller.accounts.index') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.accounts.index') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-[#111722] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.accounts.index')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-indigo-500 rounded-l-full shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    <span class="text-sm">اکانت‌های سرویس</span>
                </a>
                <a wire:navigate href="{{ route('reseller.customers') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.customers') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-[#111722] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.customers')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-indigo-500 rounded-l-full shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-sm">مشتریان</span>
                </a>
                <a wire:navigate href="{{ route('reseller.store.orders') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.store*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-[#111722] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.store*')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-indigo-500 rounded-l-full shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="text-sm">سفارشات</span>
                </a>
            </div>

            <!-- Group: Financial -->
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2">مالی و گزارشات</p>
                <a wire:navigate href="{{ route('reseller.financial') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.financial') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-[#111722] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.financial')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-indigo-500 rounded-l-full shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm">کیف پول و تراکنش‌ها</span>
                </a>
            </div>

            <!-- Group: Network -->
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2">شبکه فروش</p>
                <a wire:navigate href="{{ route('reseller.sub-agents') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.sub-agents*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-[#111722] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.sub-agents*')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-indigo-500 rounded-l-full shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm">زیر‌نمایندگان</span>
                </a>
            </div>

            <!-- Group: System -->
            <div class="space-y-1.5 pb-4">
                <p class="px-3 text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2">سیستم</p>
                <a wire:navigate href="{{ route('reseller.settings') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.settings') ? 'bg-indigo-500/10 text-indigo-400' : 'text-zinc-400 hover:bg-[#111722] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.settings')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-indigo-500 rounded-l-full shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-sm">تنظیمات و فروشگاه</span>
                </a>
            </div>

        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

        <!-- Top Header -->
        <header class="h-[72px] bg-[#111722]/90 backdrop-blur-md border-b border-[#202938] flex items-center justify-between px-4 lg:px-8 shrink-0 z-30">

            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="p-2 rounded-xl text-zinc-400 hover:text-indigo-400 hover:bg-[#0D111A] transition-colors lg:hidden focus:outline-none" aria-label="باز کردن منو">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>

            <div class="flex items-center gap-3 sm:gap-5 flex-1 justify-end">

                <!-- Global Search Component -->
                <div class="w-full sm:w-auto max-w-xs">
                    <livewire:agent.global-search />
                </div>

                <!-- Wallet Card (Header) -->
                <a href="{{ route('reseller.financial') }}" wire:navigate class="hidden md:flex items-center gap-2.5 px-3 py-1.5 bg-[#080B12] border border-[#202938] rounded-xl hover:border-indigo-500/40 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex flex-col text-right pr-1">
                        <span class="text-[9px] text-zinc-500 font-bold tracking-wide uppercase">کیف پول شما</span>
                        <span class="text-xs font-black text-white font-mono-digit tracking-tight group-hover:text-emerald-400 transition-colors">
                            {{ number_format(auth()->user()->balance ?? 0) }} <span class="text-[9px] text-zinc-400 font-sans font-medium">تومان</span>
                        </span>
                    </div>
                </a>

                <!-- Notifications Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="relative p-2 rounded-xl bg-[#080B12] border border-[#202938] text-zinc-400 hover:text-indigo-400 hover:border-indigo-500/50 transition-all focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1.5 right-2 w-2 h-2 bg-rose-500 rounded-full animate-ping"></span>
                        <span class="absolute top-1.5 right-2 w-2 h-2 bg-rose-500 rounded-full border-2 border-[#111722]"></span>
                    </button>

                    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute left-0 mt-3 w-80 bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl shadow-black/50 z-50 overflow-hidden">
                        <div class="p-4 border-b border-[#202938] flex justify-between items-center bg-[#0D111A]">
                            <h3 class="text-xs font-black text-white">اعلان‌های سیستم</h3>
                            <span class="text-[10px] bg-indigo-500/10 text-indigo-400 px-2 py-0.5 rounded-lg font-bold border border-indigo-500/20">2 جدید</span>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-[#202938] bg-[#111722]">
                            <a href="#" class="flex gap-3 p-4 hover:bg-[#171E2B] transition-colors">
                                <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-zinc-200">تمدید موفقیت‌آمیز اکانت</p>
                                    <p class="text-[10px] text-zinc-500 mt-1.5 leading-relaxed">اکانت <span class="font-mono-digit text-zinc-400">user_102</span> با موفقیت به مدت 30 روز تمدید شد.</p>
                                </div>
                            </a>
                            <a href="#" class="flex gap-3 p-4 hover:bg-[#171E2B] transition-colors">
                                <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-zinc-200">هشدار موجودی</p>
                                    <p class="text-[10px] text-zinc-500 mt-1.5 leading-relaxed">موجودی کیف پول شما کمتر از 50 هزار تومان است.</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center gap-2.5 p-1.5 pr-3 rounded-xl bg-[#080B12] border border-[#202938] hover:border-indigo-500/40 hover:bg-[#111722] transition-all focus:outline-none group">
                        <div class="flex flex-col text-right hidden md:flex">
                            <span class="text-[11px] font-bold text-zinc-200">{{ auth()->user()->name ?? 'نماینده' }}</span>
                            <span class="text-[9px] text-zinc-500">مدیر سیستم</span>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 font-black flex items-center justify-center text-xs group-hover:bg-indigo-500 group-hover:text-white transition-colors border border-indigo-500/20">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                    </button>

                    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute left-0 mt-3 w-56 bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl shadow-black/50 z-50 overflow-hidden p-1.5">

                        <!-- Mobile User Info Fallback -->
                        <div class="px-3 py-2 border-b border-[#202938] mb-1 md:hidden">
                            <span class="block text-xs font-bold text-white">{{ auth()->user()->name ?? 'نماینده' }}</span>
                            <span class="block text-[10px] text-zinc-500 font-mono-digit mt-0.5">موجودی: {{ number_format(auth()->user()->balance ?? 0) }} تومان</span>
                        </div>

                        <a href="{{ route('reseller.profile.show') ?? '#' }}" class="flex items-center gap-2.5 px-3 py-2.5 text-[11px] font-bold text-zinc-300 hover:bg-[#171E2B] hover:text-white rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            تنظیمات حساب و نشست‌ها
                        </a>

                        <div class="h-px bg-[#202938] my-1"></div>

                        <form method="POST" action="{{ route('reseller.logout') }}" onsubmit="return confirm('آیا برای خروج از حساب کاربری مطمئن هستید؟');">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 text-[11px] font-bold text-rose-500 hover:bg-rose-500/10 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                خروج از پلتفرم
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        <!-- Main Content Slot Wrapper -->
        <main class="flex-1 overflow-y-auto p-4 lg:p-8 h-full relative bg-[#080B12]">
            <div class="max-w-[1400px] mx-auto z-10 relative">
                {{ $slot }}
            </div>
        </main>

    </div>
</div>

@livewireScripts
<script src="{{url('js/chart.js')}}"></script>

<script>
    window.addEventListener('toast', event => {
        const { type, title, message } = event.detail;
        // نمایش toast با کتابخانه دلخواه یا کد دستی
        // در اینجا از Alpine.js استفاده شده است
    });

    function copyToClipboard(text, label = 'متن') {
        if (!text) {
            alert('اطلاعاتی برای کپی وجود ندارد.');
            return;
        }

        // کپی کردن
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('success', 'کپی شد', label + ' با موفقیت کپی شد.');
            }).catch(() => {
                fallbackCopy(text, label);
            });
        } else {
            fallbackCopy(text, label);
        }
    }

    function fallbackCopy(text, label) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showToast('success', 'کپی شد', label + ' با موفقیت کپی شد.');
        } catch (err) {
            showToast('error', 'خطا', 'کپی ناموفق بود. لطفاً دستی کپی کنید.');
        }
        document.body.removeChild(textarea);
    }

    function showToast(type, title, message) {
        // می‌توانید از کتابخانه Toastr یا Alpine.js استفاده کنید
        alert(title + ': ' + message);
    }

</script>
</body>
</html>
