<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>همراه سیمرغ ایران | پرواز بدون محدودیت در دنیای آزاد</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
        html { scroll-behavior: smooth; }
    </style>
    @livewireStyles
</head>
<body class="bg-zinc-950 text-zinc-200 antialiased selection:bg-orange-500 selection:text-white overflow-x-hidden">

<div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-gradient-to-b from-orange-600/10 to-transparent rounded-full blur-[140px] -z-10"></div>

<nav class="fixed w-full z-50 bg-zinc-950/70 backdrop-blur-md border-b border-zinc-900 transition-all">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <svg class="w-9 h-9 text-orange-500 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 22l10-6 10 6L12 2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V9"></path>
            </svg>
            <div class="text-xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-l from-orange-400 to-red-500">
                همراه سیمرغ ایران
            </div>
        </div>

        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-sm font-medium text-zinc-400 hover:text-orange-400 transition">ویژگی‌های شبکه</a>
            <a href="#pricing" class="text-sm font-medium text-zinc-400 hover:text-orange-400 transition">تعرفه‌ها و اشتراک</a>
            <a href="{{route('tutorials.index')}}"  class="text-sm font-medium text-zinc-400 hover:text-orange-400 transition">آموزش اتصال</a>
        </div>

        <div class="flex items-center gap-4">
            @auth
                <a  href="{{ route('login') }}" wire:navigate  class="px-5 py-2 text-sm font-bold bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-full transition">داشبورد کاربری</a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-zinc-400 hover:text-orange-400 transition" wire:navigate>ورود</a>
                <a  href="{{ route('login') }}" wire:navigate  class="px-5 py-2.5 text-sm font-bold bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 rounded-full transition shadow-lg shadow-orange-900/30 text-white" wire:navigate>ثبت نام</a>
            @endauth
        </div>
    </div>
</nav>

<header class="relative pt-36 pb-24 lg:pt-52 lg:pb-36">
    <div class="container mx-auto px-6 text-center">

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-zinc-900 border border-zinc-800 text-orange-400 text-xs font-semibold mb-8 shadow-inner">
                <span class="flex h-2 w-2 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                </span>
            مجهز به پروتکل‌های بهینه‌سازی شده ۱۴۰۵
        </div>

        <h1 class="text-4xl md:text-7xl font-black mb-8 leading-[1.2] text-zinc-100">
            عبور از محدودیت‌ها، با بال‌های <span class="text-transparent bg-clip-text bg-gradient-to-l from-orange-400 to-red-500">سیمرغ</span>
        </h1>

        <p class="text-base md:text-xl text-zinc-400 mb-12 max-w-3xl mx-auto leading-relaxed">
            شبکه خصوصی و فوق‌العاده پایدار <span class="text-zinc-200 font-semibold">همراه سیمرغ ایران</span> با روتینگ پیشرفته، امن‌ترین کانال ارتباطی را برای وب‌گردی، استریم با کیفیت 4K و پینگ طلایی در بازی‌های آنلاین فراهم می‌سازد.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="#pricing" class="px-8 py-4 text-base font-bold bg-zinc-100 text-zinc-950 hover:bg-white rounded-full transition shadow-[0_0_30px_rgba(249,115,22,0.25)]">
                تهیه اشتراک فوری
            </a>
            <a href="#features" class="px-8 py-4 text-base font-bold bg-zinc-900 text-zinc-300 border border-zinc-800 hover:bg-zinc-800 rounded-full transition">
                بررسی ویژگی‌های فنی
            </a>
        </div>
    </div>
</header>

<section id="features" class="py-24 bg-zinc-900/20 border-y border-zinc-900 relative">
    <div class="container mx-auto px-6">
        <div class="text-center mb-20">
            <h2 class="text-3xl font-bold text-zinc-100 mb-4">پایداری بی‌افزایش، سرعت بی‌مرز</h2>
            <p class="text-zinc-500 max-w-md mx-auto text-sm">چرا سرویس‌های همراه سیمرغ ایران انتخاب اول کاربران حرفه‌ای است؟</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-8 bg-zinc-900/60 rounded-3xl border border-zinc-800/80 hover:border-orange-500/40 transition-all duration-300 group hover:-translate-y-1">
                <div class="w-14 h-14 bg-orange-600/10 rounded-2xl flex items-center justify-center text-orange-500 text-2xl mb-6 group-hover:scale-110 transition duration-300">⚡</div>
                <h3 class="text-xl font-bold mb-3 text-zinc-100">تونلینگ پیشرفته و اختصاصی</h3>
                <p class="text-zinc-400 text-sm leading-relaxed">بهره‌مندی از سرورهای اختصاصی با پورت‌های پرسرعت جهت تضمین ثبات کانکشن در سخت‌ترین شرایط شبکه.</p>
            </div>

            <div class="p-8 bg-zinc-900/60 rounded-3xl border border-zinc-800/80 hover:border-red-500/40 transition-all duration-300 group hover:-translate-y-1">
                <div class="w-14 h-14 bg-red-600/10 rounded-2xl flex items-center justify-center text-red-500 text-2xl mb-6 group-hover:scale-110 transition duration-300">🛡️</div>
                <h3 class="text-xl font-bold mb-3 text-zinc-100">حریم خصوصی تایید شده</h3>
                <p class="text-zinc-400 text-sm leading-relaxed">استفاده از الگوریتم‌های رمزنگاری سطح بالا برای ناشناس ماندن کامل. اطلاعات شما کاملاً محفوظ می‌ماند.</p>
            </div>

            <div class="p-8 bg-zinc-900/60 rounded-3xl border border-zinc-800/80 hover:border-yellow-500/40 transition-all duration-300 group hover:-translate-y-1">
                <div class="w-14 h-14 bg-yellow-600/10 rounded-2xl flex items-center justify-center text-yellow-500 text-2xl mb-6 group-hover:scale-110 transition duration-300">🎮</div>
                <h3 class="text-xl font-bold mb-3 text-zinc-100">پینگ طلایی و ثابت</h3>
                <p class="text-zinc-400 text-sm leading-relaxed">روتینگ هوشمند بهینه‌سازی شده برای کاهش چشمگیر پینگ بازی‌های آنلاین محبوب شما.</p>
            </div>
        </div>
    </div>
</section>

<section id="pricing" class="py-28 relative">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-black text-zinc-100 mb-4">تعرفه‌های اشتراک پریمیوم</h2>
            <p class="text-zinc-500 text-sm">تحویل مشخصات اتصال بلافاصله پس از پرداخت آنلاین موفق</p>
        </div>

        @livewire('store.package-list')

    </div>
</section>

<footer class="bg-zinc-950 border-t border-zinc-900 py-12">
    <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-2">
            <span class="text-zinc-400 text-sm">© ۲۰۲۶ تمامی حقوق محفوظ و متعلق به <strong>همراه سیمرغ ایران</strong> می‌باشد.</span>
        </div>
        <div class="flex gap-6 text-zinc-500 text-sm">
            <a href="#" class="hover:text-orange-400 transition">پشتیبانی تلگرام</a>
            <a href="#" class="hover:text-orange-400 transition">شرایط استفاده</a>
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>
