<div class="relative min-h-screen pb-24 w-full flex flex-col items-center selection:bg-orange-500/30 selection:text-white" dir="rtl">

    <!-- Ambient Premium Background -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff03_1px,transparent_1px),linear-gradient(to_bottom,#ffffff03_1px,transparent_1px)] bg-[size:32px_32px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
        <div class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-orange-600/10 rounded-full blur-[120px] mix-blend-screen animate-pulse" style="animation-duration: 10s;"></div>
    </div>

    <div class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 animate-fade-in">

        <!-- 1. Hero Section -->
        <div class="text-center pt-16 pb-10 flex flex-col items-center">
            <!-- Brand Logo -->
            <div class="relative group cursor-default">
                <div class="absolute inset-0 bg-orange-500/20 blur-xl rounded-full group-hover:bg-orange-500/30 transition-all duration-500"></div>
                @if(!empty($storeData['logo']))
                    <img src="{{ $storeData['logo'] }}" alt="{{ $storeData['brand_name'] }}" class="relative w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-3xl object-cover shadow-2xl shadow-black/50 border border-white/10 mb-6">
                @else
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-3xl bg-gradient-to-br from-orange-500/10 to-red-500/5 border border-orange-500/20 flex items-center justify-center text-orange-500 font-black text-4xl shadow-2xl shadow-black/50 mb-6">
                        {{ mb_substr($storeData['brand_name'], 0, 1) }}
                    </div>
                @endif
            </div>

            <!-- Brand Name & Desc -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-[10px] font-bold uppercase tracking-widest w-max mb-4 shadow-[0_0_15px_rgba(249,115,22,0.1)]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                شبکه‌ای برای Gaming بهتر
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-l from-white via-zinc-200 to-zinc-400 tracking-tight mb-4">
                {{ $storeData['brand_name'] }}
            </h1>
            <p class="text-zinc-400 text-sm sm:text-base max-w-lg mx-auto leading-relaxed font-medium">
                {{ $storeData['description'] }}
            </p>
        </div>

        <!-- 2. Mini Features Card -->
        @if(!session()->has('success_order'))
            <div class="flex flex-wrap justify-center gap-3 sm:gap-6 mb-14">
                <div class="bg-zinc-900/40 border border-white/5 rounded-2xl px-5 py-2.5 flex items-center gap-2.5 backdrop-blur-md shadow-lg shadow-black/20 hover:bg-zinc-900/60 transition-colors">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span class="text-xs font-bold text-zinc-300">پینگ بهینه</span>
                </div>
                <div class="bg-zinc-900/40 border border-white/5 rounded-2xl px-5 py-2.5 flex items-center gap-2.5 backdrop-blur-md shadow-lg shadow-black/20 hover:bg-zinc-900/60 transition-colors">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="text-xs font-bold text-zinc-300">اتصال پایدار</span>
                </div>
                <div class="bg-zinc-900/40 border border-white/5 rounded-2xl px-5 py-2.5 flex items-center gap-2.5 backdrop-blur-md shadow-lg shadow-black/20 hover:bg-zinc-900/60 transition-colors">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    <span class="text-xs font-bold text-zinc-300">سرورهای اختصاصی</span>
                </div>
            </div>
        @endif

    <!-- 3. Premium Stepper -->
        @if(!session()->has('success_order'))
            <div class="max-w-2xl mx-auto mb-14" dir="rtl">
                <div class="flex items-center justify-between relative px-2">
                    <!-- Lines -->
                    <div class="absolute right-0 top-5 -translate-y-1/2 w-full h-[2px] bg-zinc-800/80 rounded-full z-0"></div>
                    <div class="absolute right-0 top-5 -translate-y-1/2 h-[2px] bg-gradient-to-l from-orange-500 to-orange-400 rounded-full z-0 transition-all duration-700 ease-in-out shadow-[0_0_10px_rgba(249,115,22,0.5)]" style="width: {{ ($step - 1) * 50 }}%"></div>

                    <!-- Step 1 -->
                    <div class="relative z-10 flex flex-col items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm transition-all duration-500 {{ $step >= 1 ? 'bg-orange-500 text-white shadow-[0_0_20px_rgba(249,115,22,0.4)] scale-110' : 'bg-zinc-900 border border-zinc-700 text-zinc-500' }}">
                            @if($step > 1)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                01
                            @endif
                        </div>
                        <span class="text-[11px] font-bold transition-colors duration-300 {{ $step >= 1 ? 'text-white' : 'text-zinc-600' }}">انتخاب سرویس</span>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative z-10 flex flex-col items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm transition-all duration-500 {{ $step >= 2 ? ($step > 2 ? 'bg-emerald-500 text-white shadow-[0_0_20px_rgba(16,185,129,0.4)]' : 'bg-orange-500 text-white shadow-[0_0_20px_rgba(249,115,22,0.4)] scale-110') : 'bg-zinc-900 border border-zinc-700 text-zinc-500' }}">
                            @if($step > 2)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                02
                            @endif
                        </div>
                        <span class="text-[11px] font-bold transition-colors duration-300 {{ $step >= 2 ? 'text-white' : 'text-zinc-600' }}">تایید مشخصات</span>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative z-10 flex flex-col items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm transition-all duration-500 {{ $step >= 3 ? 'bg-orange-500 text-white shadow-[0_0_20px_rgba(249,115,22,0.4)] scale-110' : 'bg-zinc-900 border border-zinc-700 text-zinc-500' }}">
                            03
                        </div>
                        <span class="text-[11px] font-bold transition-colors duration-300 {{ $step >= 3 ? 'text-white' : 'text-zinc-600' }}">پرداخت نهایی</span>
                    </div>
                </div>
            </div>
        @endif

    <!-- ================= SUCCESS STATE ================= -->
        @if (session()->has('success_order'))
            <div class="max-w-lg mx-auto bg-zinc-900/60 backdrop-blur-2xl border border-emerald-500/20 rounded-[2rem] p-8 text-center shadow-[0_20px_60px_-15px_rgba(16,185,129,0.15)] animate-fade-in relative overflow-hidden">

                <!-- Success Glow -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-48 bg-emerald-500/10 blur-[60px] rounded-full pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="w-20 h-20 mx-auto bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 border border-emerald-500/30 text-emerald-400 rounded-3xl flex items-center justify-center mb-6 shadow-[0_0_30px_rgba(16,185,129,0.2)]">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2 tracking-tight">خرید موفق بود</h3>
                    <p class="text-sm text-zinc-400 mb-8 font-medium leading-relaxed">{{ session('success_order') }}</p>

                    <!-- Secure Credential Card -->
                    @if(session()->has('new_account'))
                        <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-6 mb-8 text-right space-y-4 shadow-inner relative overflow-hidden group">
                            <div class="absolute inset-y-0 right-0 w-1 bg-gradient-to-b from-amber-500 to-orange-500"></div>

                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <p class="text-[11px] text-amber-500 font-bold">اطلاعات ورود به حساب (یادداشت کنید)</p>
                            </div>

                            <div class="flex justify-between items-center border-b border-white/[0.05] pb-3">
                                <span class="text-xs font-bold text-zinc-500">نام کاربری (موبایل):</span>
                                <span class="font-mono text-zinc-200 font-bold tracking-wider" dir="ltr">{{ session('username') }}</span>
                            </div>

                            <div class="flex justify-between items-center pt-2" x-data="{ copied: false }">
                                <span class="text-xs font-bold text-zinc-500">رمز عبور ورود:</span>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-emerald-400 text-lg font-black tracking-[0.2em]" dir="ltr" id="new-password">{{ session('password') }}</span>
                                    <!-- Copy Button with Alpine -->
                                    <button @click="navigator.clipboard.writeText('{{ session('password') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="p-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white transition-colors" title="کپی رمز عبور">
                                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <svg x-show="copied" x-cloak class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-black text-sm transition-all shadow-[0_8px_20px_-6px_rgba(16,185,129,0.4)] hover:shadow-[0_12px_25px_-8px_rgba(16,185,129,0.6)] hover:-translate-y-0.5" wire:navigate>
                            <span>ورود به داشبورد و دریافت کانفیگ</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                        <button wire:click="$set('step', 1); session()->forget('success_order');" class="w-full py-4 rounded-xl bg-zinc-900 border border-zinc-800 text-xs font-bold text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors">
                            خرید سرویس جدید
                        </button>
                    </div>
                </div>
            </div>
        @else

        <!-- ================= STEP 1 ================= -->
            @if($step === 1)
                <div class="animate-fade-in">

                    <!-- 4. Segmented Control Filter -->
                    <div class="flex justify-center mb-12">
                        <div class="inline-flex items-center p-1.5 bg-zinc-900/60 backdrop-blur-md border border-white/5 rounded-2xl shadow-inner overflow-x-auto max-w-full">
                            <button wire:click="$set('selectedProtocol', 'all')" class="relative px-5 sm:px-6 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 {{ $selectedProtocol === 'all' ? 'bg-zinc-800/80 text-white shadow-md' : 'text-zinc-500 hover:text-zinc-300' }}">
                                همه سرویس‌ها
                            </button>

                            <button wire:click="$set('selectedProtocol', 'wireguard')" class="relative px-5 sm:px-6 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 flex items-center gap-2 {{ $selectedProtocol === 'wireguard' ? 'bg-orange-500/10 text-orange-400 shadow-md border border-orange-500/20' : 'text-zinc-500 hover:text-zinc-300' }}">
                                <span>وایرگارد</span>
                                <span class="bg-orange-500/20 px-1.5 py-0.5 rounded-md text-[9px] uppercase tracking-wider font-black">WG</span>
                            </button>

                            <button wire:click="$set('selectedProtocol', 'l2tp_openvpn')" class="relative px-5 sm:px-6 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 {{ $selectedProtocol === 'l2tp_openvpn' ? 'bg-blue-500/10 text-blue-400 shadow-md border border-blue-500/20' : 'text-zinc-500 hover:text-zinc-300' }}">
                                L2TP / OpenVPN
                            </button>
                        </div>
                    </div>

                    <!-- 5. Product Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @forelse($plans as $plan)
                        @php
                            $n = strtolower($plan->name);
                            $isWg = str_contains($n, 'wireguard') || str_contains($n, 'وایرگارد') || str_contains($n, 'wg');
                            $isGaming = str_contains($n, 'game') || str_contains($n, 'گیم') || str_contains($n, 'پینگ') || str_contains($n, 'ترید');
                        @endphp

                        <!-- Card Component -->
                            <div wire:key="plan-{{ $plan->id }}" class="group relative bg-zinc-900/40 backdrop-blur-xl border border-white/[0.05] rounded-[2rem] p-7 flex flex-col hover:border-orange-500/40 transition-all duration-500 hover:-translate-y-1 hover:shadow-[0_20px_40px_-15px_rgba(249,115,22,0.15)] overflow-hidden cursor-pointer" wire:click="selectPlan({{ $plan->id }})">

                                <!-- Card Hover Glow -->
                                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/0 to-orange-500/0 group-hover:from-orange-500/5 group-hover:to-transparent transition-all duration-500 pointer-events-none"></div>

                                <!-- 6. Gaming Badge (Visual only based on name) -->
                                @if($isGaming)
                                    <div class="absolute top-0 right-8 bg-gradient-to-b from-orange-500 to-red-500 text-white text-[9px] font-black tracking-widest uppercase px-3 py-1.5 rounded-b-lg shadow-[0_5px_15px_rgba(249,115,22,0.4)] z-10 flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        ویژه گیمینگ
                                    </div>
                            @endif

                            <!-- Header -->
                                <div class="flex justify-between items-start mb-6 relative z-10 pt-2">
                                    <h3 class="text-xl font-black text-white tracking-tight">{{ $plan->name }}</h3>
                                    @if($isWg)
                                        <span class="bg-zinc-800 text-orange-400 border border-orange-500/20 text-[10px] font-black px-2.5 py-1.5 rounded-lg uppercase tracking-wider">WireGuard</span>
                                    @else
                                        <span class="bg-zinc-800 text-blue-400 border border-blue-500/20 text-[10px] font-black px-2.5 py-1.5 rounded-lg uppercase tracking-wider">L2TP / OPVN</span>
                                    @endif
                                </div>

                                <!-- Price -->
                                <div class="mb-8 relative z-10">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-4xl font-black text-white font-mono tracking-tighter">{{ number_format($plan->final_sell_price) }}</span>
                                        <span class="text-xs font-bold text-zinc-500">تومان</span>
                                    </div>
                                </div>

                                <!-- Features Mini Grid -->
                                <div class="grid grid-cols-2 gap-y-5 gap-x-4 mb-8 flex-1 relative z-10">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">حجم ترافیک</span>
                                        <span class="text-sm font-black text-white flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            {{ $plan->group_volume > 0 ? $plan->group_volume . ' گیگابایت' : 'نامحدود ∞' }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">اعتبار زمانی</span>
                                        <span class="text-sm font-black text-white flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $plan->expire_value ?? '30' }} {{ $plan->expire_type === 'days' ? 'روز' : 'ماه' }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col gap-1 col-span-2">
                                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">اتصال همزمان</span>
                                        <span class="text-sm font-black text-white flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            {{ $plan->multi_login ?? 1 }} کاربر
                                        </span>
                                    </div>
                                </div>

                                <!-- 8. CTA Button -->
                                <button class="relative z-10 w-full py-4 rounded-2xl bg-zinc-800 text-white font-bold text-sm group-hover:bg-gradient-to-r group-hover:from-orange-500 group-hover:to-orange-600 group-hover:shadow-[0_8px_20px_-6px_rgba(249,115,22,0.5)] transition-all duration-300">
                                    انتخاب سرویس
                                </button>
                            </div>
                    @empty
                        <!-- 10. Empty State -->
                            <div class="col-span-full py-20 flex flex-col items-center justify-center bg-zinc-900/20 backdrop-blur-sm border border-white/5 rounded-[2rem] text-center">
                                <div class="w-16 h-16 bg-zinc-800/50 rounded-2xl flex items-center justify-center text-zinc-500 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                </div>
                                <h3 class="text-lg font-black text-white mb-2">سرویسی پیدا نشد</h3>
                                <p class="text-sm text-zinc-500 font-medium">در حال حاضر برای این پروتکل، سرویسی برای خرید موجود نیست.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

        <!-- ================= STEP 2 ================= -->
            @if($step === 2)
                <div class="max-w-2xl mx-auto w-full flex flex-col md:flex-row gap-6 animate-fade-in">

                    <!-- 12. Checkout Summary (Left/Top side) -->
                    <div class="w-full md:w-5/12 bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-3xl p-6 h-fit order-2 md:order-1">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/5">
                            <span class="text-sm font-bold text-white">خلاصه سفارش</span>
                            <button wire:click="previousStep" class="text-[10px] font-bold text-orange-500 hover:text-orange-400 bg-orange-500/10 px-2.5 py-1.5 rounded-lg transition-colors">ویرایش</button>
                        </div>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-zinc-500">سرویس:</span>
                                <span class="text-sm font-black text-zinc-200">{{ $selectedPlan->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-zinc-500">حجم:</span>
                                <span class="text-xs font-bold text-zinc-200">{{ $selectedPlan->group_volume > 0 ? $selectedPlan->group_volume . ' GB' : 'نامحدود' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-zinc-500">اعتبار:</span>
                                <span class="text-xs font-bold text-zinc-200">{{ $selectedPlan->expire_value ?? '30' }} {{ $selectedPlan->expire_type === 'days' ? 'روز' : 'ماه' }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-white/5 flex justify-between items-end">
                            <span class="text-xs font-bold text-zinc-500">مبلغ نهایی:</span>
                            <div class="text-left">
                                <span class="text-2xl font-black text-orange-500 font-mono tracking-tighter">{{ number_format($selectedPlanPrice) }}</span>
                                <span class="text-[10px] font-bold text-zinc-500 ml-1">تومان</span>
                            </div>
                        </div>
                    </div>

                    <!-- 11. Customer Details (Right/Bottom side) -->
                    <div class="w-full md:w-7/12 bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-3xl p-6 md:p-8 order-1 md:order-2">
                        <h3 class="text-lg font-black text-white mb-6">مشخصات مشتری</h3>

                    @if(Auth::check())
                        <!-- User Summary Card -->
                            <div class="bg-zinc-950/50 border border-emerald-500/20 rounded-2xl p-5 mb-8">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-emerald-400">شما وارد حساب کاربری هستید</span>
                                </div>
                                <div class="space-y-2 pl-11">
                                    <div class="text-sm font-bold text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs font-bold text-zinc-500 font-mono" dir="ltr">{{ Auth::user()->phone ?? Auth::user()->email ?? Auth::user()->username }}</div>
                                </div>
                            </div>
                    @else
                        <!-- Guest Form -->
                            <div class="space-y-5 mb-8">
                                <div>
                                    <label class="block text-[11px] font-bold text-zinc-400 mb-2 pl-1">نام و نام خانوادگی</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-zinc-500 group-focus-within:text-orange-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <input wire:model="name" type="text" class="block w-full pr-11 pl-4 py-3.5 bg-zinc-950/50 border border-zinc-800 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/50 focus:ring-2 focus:ring-orange-500/20 transition-all text-sm font-medium shadow-inner" placeholder="نام خود را وارد کنید">
                                    </div>
                                    @error('name') <span class="text-red-400 text-[10px] mt-1.5 block font-bold px-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-zinc-400 mb-2 pl-1">شماره موبایل (جهت دریافت مشخصات)</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-zinc-500 group-focus-within:text-orange-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        </div>
                                        <input wire:model="phone" type="text" placeholder="09xxxxxxxxx" class="block w-full pr-11 pl-4 py-3.5 bg-zinc-950/50 border border-zinc-800 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/50 focus:ring-2 focus:ring-orange-500/20 transition-all text-sm font-medium tracking-wider font-mono shadow-inner" dir="ltr">
                                    </div>
                                    @error('phone') <span class="text-red-400 text-[10px] mt-1.5 block font-bold px-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex items-start gap-2 bg-zinc-950/30 p-3 rounded-xl border border-white/[0.02]">
                                    <svg class="w-4 h-4 text-zinc-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-[10px] text-zinc-400 font-medium leading-relaxed">حساب کاربری شما بر اساس شماره موبایل به صورت خودکار پس از تکمیل خرید ایجاد خواهد شد.</p>
                                </div>
                            </div>
                    @endif

                    <!-- Actions -->
                        <div class="flex gap-3 pt-2">
                            <button wire:click="previousStep" class="px-5 py-3.5 rounded-xl bg-zinc-800 text-white font-bold text-sm hover:bg-zinc-700 transition-colors">بازگشت</button>
                            <button wire:click="goToPayment" class="flex-1 py-3.5 rounded-xl bg-white text-zinc-900 font-black text-sm hover:bg-zinc-200 transition-colors shadow-[0_0_15px_rgba(255,255,255,0.1)] hover:shadow-[0_0_25px_rgba(255,255,255,0.2)]">ادامه به پرداخت</button>
                        </div>
                    </div>

                </div>
            @endif

        <!-- ================= STEP 3 ================= -->
            @if($step === 3)
                <div class="max-w-2xl mx-auto w-full flex flex-col md:flex-row gap-6 animate-fade-in">

                    <!-- Checkout Summary (Left/Top side) -->
                    <div class="w-full md:w-5/12 bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-3xl p-6 h-fit order-2 md:order-1">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/5">
                            <span class="text-sm font-bold text-white">پرداخت نهایی</span>
                        </div>

                        <div class="text-center mb-6">
                            <p class="text-xs text-zinc-400 font-bold mb-2">مبلغ قابل پرداخت</p>
                            <div class="flex items-baseline justify-center gap-1.5">
                                <span class="text-4xl text-orange-500 font-black font-mono tracking-tighter">{{ number_format($selectedPlanPrice) }}</span>
                                <span class="text-[11px] text-zinc-500 font-bold">تومان</span>
                            </div>
                        </div>

                        <div class="bg-zinc-950/50 rounded-xl p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-zinc-500">سرویس:</span>
                                <span class="text-[11px] font-bold text-zinc-300">{{ $selectedPlan->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-zinc-500">مشتری:</span>
                                <span class="text-[11px] font-bold text-zinc-300">{{ $name ?? (Auth::check() ? Auth::user()->name : '') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 13. Payment Options (Right/Bottom side) -->
                    <div class="w-full md:w-7/12 bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-3xl p-6 md:p-8 order-1 md:order-2">
                        <h3 class="text-lg font-black text-white mb-6">روش پرداخت</h3>

                    @if(Auth::check())
                        <!-- Payment Method Segmented Control -->
                            <div class="flex p-1 bg-zinc-950/60 rounded-xl border border-white/5 mb-8">
                                <button wire:click="$set('paymentMethod', 'wallet')" class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all duration-300 flex items-center justify-center gap-2 {{ $paymentMethod === 'wallet' ? 'bg-zinc-800 text-white shadow-md border border-white/5' : 'text-zinc-500 hover:text-zinc-300' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    کیف پول
                                </button>
                                <button wire:click="$set('paymentMethod', 'receipt')" class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all duration-300 flex items-center justify-center gap-2 {{ $paymentMethod === 'receipt' ? 'bg-zinc-800 text-white shadow-md border border-white/5' : 'text-zinc-500 hover:text-zinc-300' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    فیش بانکی
                                </button>
                            </div>

                            <!-- 14. Wallet View -->
                            @if($paymentMethod === 'wallet')
                                <div class="bg-zinc-950/80 border border-zinc-800 rounded-2xl p-6 text-center mb-8 relative overflow-hidden">
                                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent opacity-50"></div>
                                    <p class="text-xs text-zinc-400 font-bold mb-3">موجودی کیف پول شما</p>
                                    <div class="flex justify-center items-baseline gap-1.5 mb-2">
                                        <p class="text-2xl text-white font-black font-mono tracking-wider">{{ number_format(Auth::user()->balance) }}</p>
                                        <span class="text-[10px] text-zinc-500 font-bold">تومان</span>
                                    </div>

                                    @if(Auth::user()->balance < $selectedPlanPrice)
                                        <div class="mt-4 bg-rose-500/10 border border-rose-500/20 rounded-xl p-3 flex items-start gap-2 text-right">
                                            <svg class="w-4 h-4 text-rose-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            <p class="text-[10px] text-rose-400 font-bold leading-relaxed">موجودی کیف پول شما برای این خرید کافی نیست. لطفاً از طریق فیش بانکی اقدام کنید.</p>
                                        </div>
                                    @endif
                                    @error('wallet') <p class="text-[10px] text-rose-500 font-bold mt-3">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        @endif

                    <!-- 15. Bank Transfer Card & 16. Upload -->
                        @if($paymentMethod === 'receipt')
                            <div class="bg-zinc-950/50 border border-zinc-800 rounded-2xl p-5 mb-6 relative overflow-hidden" x-data="{ copied: false }">
                                <p class="text-[10px] text-zinc-500 font-bold mb-4 uppercase tracking-wider">اطلاعات واریز به حساب</p>

                                @if($bankDetails)
                                    <div class="flex justify-between items-center bg-zinc-900 rounded-xl p-4 border border-white/5">
                                        <div>
                                            <p class="text-[10px] text-zinc-500 mb-1">شماره کارت</p>
                                            <div class="text-base sm:text-lg font-black text-white font-mono tracking-widest" dir="ltr">{{ $bankDetails->card_number }}</div>
                                            <p class="text-[10px] text-zinc-400 font-bold mt-1.5">{{ $bankDetails->account_name }}</p>
                                        </div>
                                        <button @click="navigator.clipboard.writeText('{{ $bankDetails->card_number }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white transition-colors" title="کپی شماره کارت">
                                            <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            <svg x-show="copied" x-cloak class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="bg-rose-500/10 border border-rose-500/20 rounded-xl p-4 text-center">
                                        <p class="text-xs text-rose-400 font-bold">اطلاعات حساب بانکی ثبت نشده است!</p>
                                    </div>
                                @endif
                            </div>

                            <!-- 16. Upload Dropzone -->
                            <div class="mb-8">
                                <label class="block text-[11px] font-bold text-zinc-400 mb-2 pl-1">آپلود فیش واریزی <span class="text-rose-500">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-zinc-700/50 bg-zinc-950/30 rounded-2xl cursor-pointer hover:bg-zinc-900/50 hover:border-orange-500/50 transition-colors group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 text-zinc-500 group-hover:text-orange-500 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <p class="text-xs font-bold text-zinc-400 group-hover:text-zinc-300">برای آپلود فیش کلیک کنید</p>
                                        <p class="text-[10px] text-zinc-600 mt-1 font-medium">PNG, JPG حداکثر ۲ مگابایت</p>
                                    </div>
                                    <input wire:model="receipt" type="file" accept="image/*" class="hidden">
                                </label>
                                <div wire:loading wire:target="receipt" class="text-[10px] text-amber-500 mt-2 font-bold flex items-center gap-1.5 justify-center">
                                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    در حال آپلود فیش...
                                </div>
                                @if($receipt)
                                    <div class="text-[10px] text-emerald-500 mt-2 font-bold text-center flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        فایل انتخاب شد
                                    </div>
                                @endif
                                @error('receipt') <span class="text-rose-500 text-[10px] mt-1.5 block font-bold text-center">{{ $message }}</span> @enderror
                            </div>
                    @endif

                    <!-- 17. Submit Actions -->
                        <div class="flex gap-3 pt-2">
                            <button wire:click="previousStep" class="px-5 py-3.5 rounded-xl bg-zinc-800 text-white font-bold text-sm hover:bg-zinc-700 transition-colors">بازگشت</button>
                            <button wire:click="submitOrder" class="flex-1 py-3.5 rounded-xl {{ $paymentMethod === 'wallet' ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 shadow-[0_8px_20px_-6px_rgba(16,185,129,0.4)]' : 'bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 shadow-[0_8px_20px_-6px_rgba(249,115,22,0.4)]' }} text-white font-black text-sm transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <svg wire:loading wire:target="submitOrder" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span wire:loading.remove wire:target="submitOrder">
                                    {{ $paymentMethod === 'wallet' ? 'پرداخت و دریافت سرویس' : 'ثبت پرداخت و ارسال فیش' }}
                                </span>
                                <span wire:loading wire:target="submitOrder">در حال پردازش...</span>
                            </button>
                        </div>
                    </div>

                </div>
            @endif

        @endif

    </div>
</div>
