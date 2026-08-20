<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? $brand_name }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 antialiased selection:bg-orange-500 selection:text-white min-h-screen flex flex-col">

<nav class="border-b border-zinc-800/80 bg-zinc-950/80 backdrop-blur-xl sticky top-0 z-50 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.5)]">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">

        <a href="/" class="flex items-center gap-3 group" wire:navigate>
            <div class="w-10 h-10 bg-orange-500/10 rounded-xl flex items-center justify-center group-hover:bg-orange-500/20 transition duration-300 overflow-hidden shrink-0">
                @if($brand_logo)
                    <img src="{{ $brand_logo }}" alt="{{ $brand_name }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 22l10-6 10 6L12 2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V9"></path>
                    </svg>
                @endif
            </div>

            <span class="text-xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-l from-orange-400 to-red-500">
                {{ $brand_name }}
            </span>
        </a>

        <div class="hidden md:flex items-center gap-8 bg-zinc-900/50 px-6 py-2.5 rounded-2xl border border-zinc-800/50">
            <a href="{{route('store.index')}}" class="text-sm font-bold text-zinc-300 hover:text-orange-400 transition" wire:navigate>
                فروشگاه
            </a>
            <a href="{{route('tutorials.index')}}"  class="text-sm font-bold text-zinc-300 hover:text-orange-400 transition" wire:navigate>
                آموزشات
            </a>
            <a href="/status" class="flex items-center gap-2 text-sm font-bold text-zinc-300 hover:text-orange-400 transition" wire:navigate>
                وضعیت سرورها
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                </span>
            </a>
        </div>

        <div class="flex items-center gap-4">
            @auth
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-zinc-200 hover:text-orange-400 transition" wire:navigate>داشبورد من</a>
                    <div class="w-px h-5 bg-zinc-800"></div>
                    <form action="/logout" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-zinc-500 hover:text-red-500 transition px-3 py-1.5 rounded-lg hover:bg-red-500/10">خروج</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="px-6 py-2.5 text-sm font-bold bg-zinc-900 border border-zinc-700 hover:border-orange-500/50 text-white hover:text-orange-400 rounded-xl transition shadow-lg" wire:navigate>
                    ورود / ثبت‌نام
                </a>
            @endauth
        </div>

    </div>
</nav>
<main class="flex-grow">
    {{ $slot }}
</main>
</body>
</html>
