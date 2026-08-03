<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'فروشگاه اینترنت آزاد' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .font-mono-digit { font-family: 'JetBrains Mono', monospace, sans-serif; }
    </style>
</head>
<body class="bg-[#09090b] text-zinc-100 font-sans antialiased min-h-screen flex flex-col relative selection:bg-orange-500/30">

<div class="absolute top-0 inset-x-0 h-[500px] bg-gradient-to-b from-orange-500/10 via-transparent to-transparent -z-10 pointer-events-none blur-3xl"></div>

<header class="w-full border-b border-zinc-800/80 bg-[#09090b]/80 backdrop-blur-xl sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div id="store-branding" class="flex items-center gap-3">
            @yield('branding')
        </div>

        <nav class="hidden md:flex items-center gap-6 text-sm font-bold text-zinc-400">
            <a href="#" class="hover:text-white transition">تعرفه‌ها</a>
            <a href="#" class="hover:text-white transition">آموزش اتصال</a>
            <a href="#" class="hover:text-white transition">پشتیبانی</a>
        </nav>

        <div>
            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-white text-black font-black text-sm hover:bg-zinc-200 transition shadow-[0_0_20px_rgba(255,255,255,0.1)]">
                ورود به پنل کاربری
            </a>
        </div>
    </div>
</header>

<main class="flex-1 w-full max-w-7xl mx-auto p-6 md:p-12 z-10">
    {{ $slot }}
</main>

<footer class="border-t border-zinc-800/80 py-8 text-center text-zinc-500 text-xs mt-auto">
    <p>تمامی حقوق برای @yield('footer_brand', 'فروشگاه') محفوظ است.</p>
</footer>

@livewireScripts
</body>
</html>
