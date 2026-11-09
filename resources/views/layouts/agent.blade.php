<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'پنل نمایندگی | پلتفرم دیجیتال' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .font-mono-digit { font-family: 'IRANSans', sans-serif; font-variant-numeric: tabular-nums; }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar for Modern SaaS Look */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #4B5563; }

        /* Smooth selection */
        ::selection { background: rgba(59,130,246,0.3); color: #fff; }
    </style>
</head>
<body class="ui-shell bg-[#080f1d] text-zinc-100 font-sans antialiased">

<a href="#main-content" class="ui-skip-link">رفتن به محتوای اصلی</a>
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
         class="fixed inset-0 bg-[#080f1d]/80 backdrop-blur-sm z-40 lg:hidden">
    </div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 right-0 z-50 w-[260px] bg-[#0e1829] border-l border-[#243247] flex flex-col shadow-2xl lg:shadow-none lg:static lg:z-40 transition-transform duration-300 ease-in-out transform"
           :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">

        <!-- Sidebar Header / Branding -->
        <div class="h-[72px] flex items-center justify-between px-5 border-b border-[#243247] shrink-0 bg-[#0e1829]">
            <a href="{{ route('reseller.dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                <x-ui.brand :name="setting('SITE_TITLE', 'همراه سیمرغ')" :logo="setting('SITE_LOGO')" subtitle="پنل نمایندگی" />
            </a>

            <button type="button" aria-label="بستن منو" @click="sidebarOpen = false" class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-300 hover:bg-[#111c2e] lg:hidden transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto">

            <!-- Group: Dashboard -->
            <div class="space-y-1.5">
                <a wire:navigate href="{{ route('reseller.dashboard') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.dashboard') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-[#111c2e] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.dashboard'))
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-brand-500 rounded-l-full shadow-[0_0_10px_rgba(59,130,246,0.8)]"></div>
                    @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="text-sm">داشبورد و آمار</span>
                </a>
            </div>

            <!-- Group: Sales & Customers -->
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2">فروش و مشتریان</p>
                <a wire:navigate href="{{ route('reseller.accounts.index') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.accounts.index') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-[#111c2e] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.accounts.index')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-brand-500 rounded-l-full shadow-[0_0_10px_rgba(59,130,246,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    <span class="text-sm">اکانت‌های سرویس</span>
                </a>
                <a wire:navigate href="{{ route('reseller.customers') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.customers') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-[#111c2e] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.customers')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-brand-500 rounded-l-full shadow-[0_0_10px_rgba(59,130,246,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-sm">مشتریان</span>
                </a>
                <a wire:navigate href="{{ route('reseller.store.orders') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.store*') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-[#111c2e] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.store*')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-brand-500 rounded-l-full shadow-[0_0_10px_rgba(59,130,246,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="text-sm">سفارشات</span>
                </a>
            </div>

            <!-- Group: Financial -->
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2">مالی و گزارشات</p>
                <a wire:navigate href="{{ route('reseller.financial') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.financial') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-[#111c2e] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.financial')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-brand-500 rounded-l-full shadow-[0_0_10px_rgba(59,130,246,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm">کیف پول و تراکنش‌ها</span>
                </a>
            </div>

            <!-- Group: Network -->
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2">شبکه فروش</p>
                <a wire:navigate href="{{ route('reseller.sub-agents') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.sub-agents*') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-[#111c2e] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.sub-agents*')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-brand-500 rounded-l-full shadow-[0_0_10px_rgba(59,130,246,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm">زیر‌نمایندگان</span>
                </a>
            </div>

            <!-- Group: System -->
            <div class="space-y-1.5 pb-4">
                <p class="px-3 text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2">سیستم</p>
                <a wire:navigate href="{{ route('reseller.settings') }}" @click="sidebarOpen = false" class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all overflow-hidden {{ request()->routeIs('reseller.settings') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-[#111c2e] hover:text-zinc-200' }}">
                    @if(request()->routeIs('reseller.settings')) <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-brand-500 rounded-l-full shadow-[0_0_10px_rgba(59,130,246,0.8)]"></div> @endif
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-sm">تنظیمات و فروشگاه</span>
                </a>
            </div>

        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

        <!-- Top Header -->
        <header class="h-[72px] bg-[#111c2e]/90 backdrop-blur-md border-b border-[#243247] flex items-center justify-between px-4 lg:px-8 shrink-0 z-30">

            <div class="flex items-center gap-3">
                <button :aria-expanded="sidebarOpen" @click="sidebarOpen = true" class="p-2 rounded-xl text-zinc-400 hover:text-brand-400 hover:bg-[#0e1829] transition-colors lg:hidden focus:outline-none" aria-label="باز کردن منو">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>

            <div class="flex items-center gap-3 sm:gap-5 flex-1 justify-end">

                <!-- Global Search Component -->
                <div class="w-full sm:w-auto max-w-xs">
                    <livewire:agent.global-search />
                </div>

                <!-- Wallet Card (Header) -->
                <a href="{{ route('reseller.financial') }}" wire:navigate class="hidden md:flex items-center gap-2.5 px-3 py-1.5 bg-[#080f1d] border border-[#243247] rounded-xl hover:border-brand-500/40 transition-colors group">
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

                <x-ui.announcements />

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
                    <button :aria-expanded="open" aria-label="منوی حساب کاربری" @click="open = !open" class="flex items-center gap-2.5 p-1.5 pr-3 rounded-xl bg-[#080f1d] border border-[#243247] hover:border-brand-500/40 hover:bg-[#111c2e] transition-all focus:outline-none group">
                        <div class="flex flex-col text-right hidden md:flex">
                            <span class="text-[11px] font-bold text-zinc-200">{{ auth()->user()->name ?? 'نماینده' }}</span>
                            <span class="text-[9px] text-zinc-500">نماینده فروش</span>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-400 font-black flex items-center justify-center text-xs group-hover:bg-brand-500 group-hover:text-white transition-colors border border-brand-500/20">
                            {{ mb_substr(auth()->user()->name ?? 'ن', 0, 1) }}
                        </div>
                    </button>

                    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute left-0 mt-3 w-56 bg-[#111c2e] border border-[#243247] rounded-2xl shadow-2xl shadow-black/50 z-50 overflow-hidden p-1.5">

                        <!-- Mobile User Info Fallback -->
                        <div class="px-3 py-2 border-b border-[#243247] mb-1 md:hidden">
                            <span class="block text-xs font-bold text-white">{{ auth()->user()->name ?? 'نماینده' }}</span>
                            <span class="block text-[10px] text-zinc-500 font-mono-digit mt-0.5">موجودی: {{ number_format(auth()->user()->balance ?? 0) }} تومان</span>
                        </div>

                        <a href="{{ route('reseller.profile.show') ?? '#' }}" class="flex items-center gap-2.5 px-3 py-2.5 text-[11px] font-bold text-zinc-300 hover:bg-[#17253a] hover:text-white rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            تنظیمات حساب و نشست‌ها
                        </a>

                        <div class="h-px bg-[#243247] my-1"></div>

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
        <main id="main-content" tabindex="-1" class="ui-content flex-1 overflow-y-auto p-4 lg:p-8 h-full relative bg-[#080f1d]">
            <div class="max-w-[1400px] mx-auto z-10 relative">
                {{ $slot }}
            </div>
        </main>

    </div>
</div>

@livewireScripts
<script src="{{url('js/chart.js')}}"></script>

@include('layouts.partials.ui-feedback')

</body>
</html>
