<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName ?? 'سامانه VPN' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 antialiased selection:bg-orange-500 selection:text-white min-h-screen flex flex-col">

<nav class="border-b border-zinc-800/80 bg-zinc-950/80 backdrop-blur-xl sticky top-0 z-50 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.5)]">
    <div class="container mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-orange-500/20 overflow-hidden shrink-0 border border-zinc-700/50">
                @if(!empty($brandLogo))
                    <img src="{{ asset("storage/".$brandLogo) }}" alt="{{ $brandName }}" class="w-full h-full object-cover">
                @else
                    {{ mb_substr($brandName ?? 'S', 0, 1) }}
                @endif
            </div>
            <div class="flex flex-col">
                <span class="text-[9px] sm:text-[10px] font-bold text-zinc-500 uppercase tracking-widest">ارائه‌دهنده سرویس</span>
                <span class="text-sm sm:text-base font-black tracking-tight text-white mt-0.5">
                    {{ $brandName ?? 'سامانه VPN' }}
                </span>
            </div>
        </div>

        @if(!empty($supportId))
            <div>
                <a href="https://t.me/{{ ltrim($supportId, '@') }}" target="_blank" class="px-3 sm:px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 border border-blue-500/20 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.01-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.23.14.33-.01.06-.01.18-.02.26z"/></svg>
                    <span class="hidden sm:inline">پشتیبانی</span>
                </a>
            </div>
        @endif

    </div>
</nav>

<main class="flex-grow">
    {{ $slot }}
</main>

</body>
</html>
