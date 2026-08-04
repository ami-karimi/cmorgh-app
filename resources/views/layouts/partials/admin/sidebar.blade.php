<div x-show="sidebarOpen"
     x-transition.opacity
     class="fixed inset-0 z-40 bg-zinc-950/80 backdrop-blur-sm lg:hidden"
     @click="sidebarOpen = false"
     x-cloak></div>

<aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 right-0 z-50 w-72 bg-zinc-900/90 backdrop-blur-2xl border-l border-zinc-800/80 transition-transform duration-300 ease-in-out flex flex-col shadow-[0_0_30px_rgba(0,0,0,0.3)]">

    <!-- هدر سایدبار -->
    <div class="h-24 flex items-center justify-center border-b border-zinc-800/80 px-8 relative overflow-hidden flex-shrink-0">
        <div class="absolute inset-0 bg-gradient-to-r from-orange-500/10 to-red-500/10 opacity-50"></div>
        <div class="flex items-center gap-3 relative z-10 w-full">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center shadow-lg shadow-orange-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-white tracking-widest">سیمرغ</h1>
                <span class="text-xs text-orange-500 font-bold tracking-widest uppercase">Admin Core</span>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden absolute left-4 text-zinc-400 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- ناوبری اصلی -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5 custom-scrollbar">

        <!-- داشبورد پیشخوان -->
        <a  href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl bg-orange-500/10 text-orange-500 font-bold border border-orange-500/20 transition-all mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            داشبورد پیشخوان
        </a>

        <!-- مدیریت اکانت‌ها -->
        <div x-data="{ open: {{ request()->routeIs('admin.accounts.*') ? 'true' : 'false' }} }" class="mb-1">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100 transition-all focus:outline-none">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    <span class="font-medium text-sm">مدیریت اکانت‌ها</span>
                </div>
                <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            <div x-show="open" x-collapse x-cloak class="pr-10 py-1 space-y-1">
                <a href="{{route('admin.accounts.list')}}" wire:navigate class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.accounts.list') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.accounts.list') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>لیست اکانت‌ها</span>
                </a>
                <a href="{{route('admin.accounts.logs')}}" wire:navigate class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.accounts.logs') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.accounts.logs') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>رخدادها (Logs)</span>
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30 transition-all">
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span>
                    <span>اکانت‌های منقضی شده</span>
                </a>
                <a href="{{route('admin.accounts.create')}}" wire:navigate class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.accounts.create') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.accounts.create') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>ایجاد اکانت جدید</span>
                </a>
            </div>
        </div>

        <!-- مدیریت سیستم -->
        <div x-data="{ open: {{ request()->routeIs('admin.nas.*', 'admin.charge.*', 'admin.groups.*') ? 'true' : 'false' }} }" class="mb-1">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100 transition-all focus:outline-none">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 01-2-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    <span class="font-medium text-sm">مدیریت سیستم</span>
                </div>
                <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            <div x-show="open" x-collapse x-cloak class="pr-10 py-1 space-y-1">
                <a href="{{route('admin.nas.list')}}" wire:navigate class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.nas.list') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.nas.list') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>مدیریت سرورها</span>
                </a>
                <a href="{{route('admin.groups.list')}}" wire:navigate class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.groups.list') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.groups.list') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>مدیریت گروه‌ها</span>
                </a>
                <a href="{{route('admin.charge.list')}}" wire:navigate class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.charge.list') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.charge.list') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>مدیریت شارژها</span>
                </a>
            </div>
        </div>

        <!-- مدیریت کاربران -->
        <div x-data="{ open: {{ request()->routeIs('admin.users.*', 'admin.managers.*') ? 'true' : 'false' }} }" class="mb-1">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100 transition-all focus:outline-none">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="font-medium text-sm">مدیریت کاربران</span>
                </div>
                <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            <div x-show="open" x-collapse x-cloak class="pr-10 py-1 space-y-1">
                <a wire:navigate href="{{route('admin.managers.list')}}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.managers.list') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.managers.list') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>لیست مدیران و نمایندگان</span>
                </a>
                <a wire:navigate href="{{route('admin.users.index')}}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.users.index') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.users.index') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>لیست مشتریان</span>
                </a>
            </div>
        </div>

        <!-- امور مالی -->
        <div x-data="{ open: {{ request()->routeIs('admin.financial*', 'admin.payment.*') ? 'true' : 'false' }} }" class="mb-1">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100 transition-all focus:outline-none">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium text-sm">امور مالی</span>
                </div>
                <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            <div x-show="open" x-collapse x-cloak class="pr-10 py-1 space-y-1">
                <a href="{{ route('admin.financial') }}" wire:navigate class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.financial') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.financial') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>تراکنش‌های مالی</span>
                </a>
                <a href="{{ route('admin.payment.methods') }}" wire:navigate class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.payment.methods') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.payment.methods') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>شیوه‌های پرداخت</span>
                </a>
            </div>
        </div>

        <!-- سفارشات فروشگاه -->
        <a href="{{ route('admin.store.orders') }}" wire:navigate
           class="flex items-center justify-between px-4 py-3 rounded-xl font-bold transition-all duration-300 {{ request()->routeIs('admin.store.orders') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100' }}">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.store.orders') ? 'text-white' : 'text-zinc-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span class="text-sm">سفارشات فروشگاه</span>
            </div>

            @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-black font-mono-digit rounded-full bg-rose-500 text-white shadow-sm shadow-rose-500/30 animate-pulse">
                    {{ $pendingOrdersCount }}
                </span>
            @endif
        </a>

        <!-- اطلاع‌رسانی‌ها و رخداد -->
        <div x-data="{ open: {{ request()->routeIs('admin.announcements*', 'admin.system*') ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl font-bold transition-all duration-300 {{ request()->routeIs('admin.announcements*', 'admin.system*') ? 'bg-zinc-800/80 text-orange-400' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100' }} focus:outline-none group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 transition-colors {{ request()->routeIs('admin.announcements*', 'admin.system*') ? 'text-orange-400' : 'text-zinc-500 group-hover:text-zinc-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="text-sm">اطلاع‌رسانی‌ها و رخداد</span>
                </div>

                <svg :class="open ? 'rotate-90 text-orange-400' : 'text-zinc-600'" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <div x-show="open" x-collapse x-cloak class="pr-10 py-1 space-y-1">
                <a href="{{ route('admin.announcements') }}" wire:navigate
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.announcements*') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.announcements*') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>اطلاعیه‌ها</span>
                </a>

                <a href="{{ route('admin.system.monitor') }}" wire:navigate
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.system*') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.system*') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>وضعیت سرورها</span>
                </a>
                <a href="{{ route('admin.tutorial') }}" wire:navigate
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.tutorial*') ? 'text-orange-400 bg-orange-500/10 border-r-2 border-orange-400' : 'text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.system*') ? 'bg-orange-400' : 'bg-zinc-600' }}"></span>
                    <span>آموزشات</span>
                </a>
            </div>
        </div>

        <!-- مدیریت سایت -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-100 transition-all">
            <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
            <span class="font-medium text-sm">مدیریت سایت</span>
        </a>

    </nav>
</aside>
