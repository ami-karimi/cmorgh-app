<div class="min-h-screen w-full flex items-center justify-center p-4 md:p-6 lg:p-8 bg-zinc-950 text-zinc-100 selection:bg-orange-500/30 font-sans relative overflow-hidden" dir="rtl">

    <!-- Ambient Premium Background -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <!-- Subtle Grid -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:32px_32px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
        <!-- Glows -->
        <div class="absolute top-[-15%] right-[-10%] w-[500px] h-[500px] bg-orange-600/10 rounded-full blur-[120px] mix-blend-screen animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-[-15%] left-[-10%] w-[400px] h-[400px] bg-red-600/5 rounded-full blur-[100px] mix-blend-screen animate-pulse" style="animation-duration: 12s;"></div>
    </div>

    <!-- Main Authentication Card -->
    <div class="w-full max-w-5xl bg-zinc-900/60 backdrop-blur-2xl rounded-[2rem] border border-white/5 shadow-[0_30px_80px_-20px_rgba(0,0,0,0.8)] overflow-hidden flex flex-col lg:flex-row relative z-10">

        <!-- Left Side: Premium Branding & Hero (Desktop Only) -->
        <div class="hidden lg:flex lg:w-5/12 bg-zinc-950/40 p-12 flex-col justify-between relative overflow-hidden border-l border-white/5">
            <!-- Glass Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 via-transparent to-transparent"></div>

            <div class="relative z-10">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-[10px] font-bold uppercase tracking-widest w-max mb-6 shadow-[0_0_15px_rgba(249,115,22,0.1)]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Premium Gaming Network
                </div>

                <h2 class="text-3xl xl:text-4xl font-black text-white leading-[1.4] mb-4 tracking-tight">
                    اتصال سریع‌تر،<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-l from-orange-400 to-red-500">تجربه‌ای بدون مرز</span>
                </h2>
                <p class="text-zinc-400 text-sm leading-relaxed font-medium">
                    به شبکه اختصاصی ما متصل شوید و تجربه‌ای پایدار، سریع و بهینه برای بازی و اینترنت را احساس کنید.
                </p>
            </div>

            <!-- Visual Concept: Network Connect -->
            <div class="relative w-full h-48 flex items-center justify-center my-8 z-10">
                <svg class="absolute inset-0 w-full h-full opacity-80" viewBox="0 0 300 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <style>
                        @keyframes dash { to { stroke-dashoffset: -20; } }
                        @keyframes dash-rev { to { stroke-dashoffset: 20; } }
                    </style>
                    <!-- Connecting Lines -->
                    <path d="M40 75 L120 35 L220 60 L260 75" stroke="#3f3f46" stroke-width="1.5" stroke-dasharray="4 4" class="animate-[dash_3s_linear_infinite]" />
                    <path d="M40 75 L120 115 L220 90 L260 75" stroke="#3f3f46" stroke-width="1.5" stroke-dasharray="4 4" class="animate-[dash-rev_3s_linear_infinite]" />

                    <!-- Nodes -->
                    <!-- User -->
                    <circle cx="40" cy="75" r="5" fill="#f97316" class="animate-pulse" />
                    <circle cx="40" cy="75" r="12" fill="#f97316" fill-opacity="0.2" class="animate-ping" style="animation-duration: 3s;" />
                    <!-- Cloud Intermediaries -->
                    <circle cx="120" cy="35" r="4" fill="#52525b" />
                    <circle cx="120" cy="115" r="4" fill="#52525b" />
                    <circle cx="220" cy="60" r="4" fill="#52525b" />
                    <circle cx="220" cy="90" r="4" fill="#52525b" />
                    <!-- Global Network -->
                    <circle cx="260" cy="75" r="7" fill="#10b981" />
                    <circle cx="260" cy="75" r="16" fill="#10b981" fill-opacity="0.15" class="animate-pulse" style="animation-duration: 2s;" />
                </svg>
            </div>

            <!-- Status Indicator -->
            <div class="relative z-10 flex items-center gap-3">
                <div class="px-4 py-2.5 rounded-xl bg-zinc-900/60 border border-white/5 text-xs text-zinc-300 font-bold backdrop-blur-md flex items-center gap-3 w-max">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" style="animation-duration: 2s;"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.8)]"></span>
                    </span>
                    شبکه در وضعیت پایدار
                </div>
            </div>
        </div>

        <!-- Right Side: Authentication Form -->
        <div class="w-full lg:w-7/12 p-6 sm:p-10 lg:p-14 xl:p-16 relative flex flex-col justify-center">

            <!-- Mobile Brand Header -->
            <div class="lg:hidden flex flex-col items-center mb-10 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500/10 to-red-500/10 border border-orange-500/20 flex items-center justify-center mb-4 shadow-[0_0_20px_rgba(249,115,22,0.15)]">
                    <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h1 class="text-xl font-black text-white tracking-tight">Premium Network</h1>
            </div>

            <!-- Form Header -->
            <div class="mb-8">
                <h3 class="text-2xl font-black text-white mb-2 tracking-tight">
                    @if($step === 'check_identifier') خوش آمدید
                    @elseif($step === 'login_password') خوش برگشتید
                    @elseif($step === 'login_otp') تأیید ورود
                    @elseif($step === 'register') حساب خود را بسازید
                    @else بازگشت به پنل
                    @endif
                </h3>
                <p class="text-sm font-medium text-zinc-400">
                    @if($step === 'check_identifier') برای اتصال، اطلاعات کاربری خود را وارد کنید.
                    @elseif($step === 'register') لطفاً اطلاعات زیر را برای تکمیل ثبت‌نام وارد کنید.
                    @else برای ورود به حساب کاربری، فرم زیر را تکمیل کنید.
                    @endif
                </p>
            </div>

            <!-- Livewire Form -->
            <form wire:submit.prevent="submitForm" method="POST" wire:key="main-auth-form" class="space-y-5">

                <!-- STEP 1: Check Identifier -->
                @if($step === 'check_identifier')
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-2 pl-1">ایمیل یا شماره موبایل</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-zinc-500 group-focus-within:text-orange-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input wire:model="identifier" type="text" dir="ltr" class="block w-full pr-12 pl-4 py-3.5 bg-zinc-950/50 border border-zinc-800 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/50 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 text-sm font-medium shadow-inner" placeholder="name@example.com">
                        </div>
                        @error('identifier')
                        <div class="flex items-center gap-1.5 mt-2 text-red-400 text-[11px] font-bold px-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 mt-2 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 transition-all duration-300 shadow-[0_8px_20px_-6px_rgba(249,115,22,0.4)] hover:shadow-[0_12px_25px_-8px_rgba(249,115,22,0.6)] hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="submitForm">ادامه</span>
                        <span wire:loading wire:target="submitForm" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            در حال بررسی...
                        </span>
                    </button>
                @endif

            <!-- STEP 2: Password Login -->
                @if($step === 'login_password')
                <!-- User Identifier Card -->
                    <div class="flex items-center justify-between p-3.5 bg-zinc-950/40 border border-zinc-800/80 rounded-xl mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-zinc-800/80 flex items-center justify-center text-zinc-400 border border-white/5">
                                @if($loginType === 'email')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                @endif
                            </div>
                            <span class="text-zinc-200 font-bold tracking-wide text-sm" dir="ltr">{{ Str::limit($identifier, 25) }}</span>
                        </div>
                        <button type="button" wire:click.prevent="resetIdentifier" wire:loading.attr="disabled" class="text-[11px] font-bold text-zinc-400 hover:text-white px-3 py-1.5 rounded-lg bg-zinc-800/40 hover:bg-zinc-700 transition-colors duration-200 border border-white/5">
                            <span wire:loading.remove wire:target="resetIdentifier">تغییر</span>
                            <svg wire:loading wire:target="resetIdentifier" class="w-3 h-3 animate-spin mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                        </button>
                    </div>

                    <!-- Password Field with Alpine Toggle -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-2 pl-1">رمز عبور</label>
                        <div class="relative group" x-data="{ show: false }">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-zinc-500 group-focus-within:text-orange-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input wire:model="password" :type="show ? 'text' : 'password'" dir="ltr" class="block w-full pr-12 pl-12 py-3.5 bg-zinc-950/50 border border-zinc-800 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/50 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 text-sm font-medium tracking-widest shadow-inner" placeholder="••••••••">

                            <!-- Toggle Button -->
                            <button type="button" @click="show = !show" class="absolute inset-y-0 left-0 flex items-center pl-4 text-zinc-500 hover:text-zinc-300 focus:outline-none transition-colors">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        @error('password')
                        <div class="flex items-center gap-1.5 mt-2 text-red-400 text-[11px] font-bold px-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center gap-2">
                            <div class="relative flex items-center">
                                <input wire:model="remember" id="remember" type="checkbox" class="peer appearance-none w-4 h-4 border border-zinc-700 rounded bg-zinc-950/50 checked:bg-orange-500 checked:border-orange-500 cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:ring-offset-1 focus:ring-offset-zinc-900">
                                <svg class="absolute inset-0 w-4 h-4 text-white pointer-events-none opacity-0 peer-checked:opacity-100 p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <label for="remember" class="text-[13px] font-medium text-zinc-400 cursor-pointer hover:text-zinc-300 transition-colors">مرا به خاطر بسپار</label>
                        </div>
                        <a href="#" class="text-[13px] font-bold text-zinc-400 hover:text-orange-400 transition-colors">فراموشی رمز؟</a>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 mt-2 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 transition-all duration-300 shadow-[0_8px_20px_-6px_rgba(249,115,22,0.4)] hover:shadow-[0_12px_25px_-8px_rgba(249,115,22,0.6)] hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="submitForm">ورود به پنل</span>
                        <span wire:loading wire:target="submitForm" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            درحال ورود...
                        </span>
                    </button>

                    @if($loginType === 'phone')
                        <div class="text-center pt-5 border-t border-white/5 mt-5">
                            <button type="button" wire:click.prevent="requestLoginOtp" wire:loading.attr="disabled" class="text-[13px] font-medium text-zinc-400 hover:text-white transition-colors group flex justify-center items-center w-full gap-2">
                                <span wire:loading.remove wire:target="requestLoginOtp" class="flex justify-center items-center w-full gap-2">
                                    ورود سریع با <span class="font-bold text-orange-500 group-hover:text-orange-400 transition-colors">کد یکبار مصرف (OTP)</span>
                                    <svg class="w-4 h-4 text-orange-500 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                </span>
                                <span wire:loading wire:target="requestLoginOtp" class="flex justify-center items-center w-full gap-2 text-orange-500">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                    درحال ارسال پیامک...
                                </span>
                            </button>
                        </div>
                    @endif
                @endif

            <!-- STEP 3: OTP Login -->
                @if($step === 'login_otp')
                    <div class="flex items-center justify-between p-3.5 bg-zinc-950/40 border border-zinc-800/80 rounded-xl mb-6 text-sm">
                        <span class="text-zinc-400 font-medium text-[13px]">کد پیامک شده به: <span class="text-white font-bold tracking-widest ml-1" dir="ltr">{{ $identifier }}</span></span>
                        <button type="button" wire:click.prevent="resetIdentifier" wire:loading.attr="disabled" class="text-[11px] font-bold text-zinc-400 hover:text-white px-3 py-1.5 rounded-lg bg-zinc-800/40 hover:bg-zinc-700 transition-colors duration-200 border border-white/5">
                            <span wire:loading.remove wire:target="resetIdentifier">ویرایش</span>
                            <svg wire:loading wire:target="resetIdentifier" class="w-3 h-3 animate-spin mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                        </button>
                    </div>

                    <div x-data="{
                            otpArray: ['', '', '', '', ''],
                            timer: 45,
                            interval: null,
                            init() {
                                this.$watch('otpArray', val => { $wire.set('otp', val.join('')); });
                                this.startTimer();
                            },
                            startTimer() {
                                this.timer = 45;
                                clearInterval(this.interval);
                                this.interval = setInterval(() => {
                                    if(this.timer > 0) this.timer--;
                                    else clearInterval(this.interval);
                                }, 1000);
                            },
                            resend() {
                                if(this.timer === 0) {
                                    $wire.requestLoginOtp();
                                    this.startTimer();
                                }
                            },
                            onInput(index, e) {
                                let val = e.target.value.replace(/[^0-9]/g, '');
                                this.otpArray[index] = val.slice(-1);
                                if (val && index < 4) { this.$refs['box_' + (index + 1)].focus(); }
                            },
                            onKeydown(index, e) {
                                if (e.key === 'Backspace' && !this.otpArray[index] && index > 0) {
                                    this.$refs['box_' + (index - 1)].focus();
                                }
                            },
                            onPaste(e) {
                                let data = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 5);
                                for(let i=0; i<data.length; i++) { this.otpArray[i] = data[i]; }
                                let nextFocus = Math.min(data.length, 4);
                                if (this.$refs['box_' + nextFocus]) { this.$refs['box_' + nextFocus].focus(); }
                            }
                        }">

                        <!-- OTP Inputs -->
                        <div dir="ltr" class="flex justify-center gap-3 sm:gap-4 mb-2">
                            @for($i = 0; $i < 5; $i++)
                                <input type="text" inputmode="numeric" x-ref="box_{{ $i }}" x-model="otpArray[{{ $i }}]" @input="onInput({{ $i }}, $event)" @keydown="onKeydown({{ $i }}, $event)" @paste.prevent="onPaste($event)" class="w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-black bg-zinc-950/50 border border-zinc-800 rounded-xl text-white focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/50 focus:ring-4 focus:ring-orange-500/10 focus:scale-[1.02] transition-all duration-200 shadow-inner" placeholder="-">
                            @endfor
                        </div>

                        @error('otp')
                        <div class="flex items-center justify-center gap-1.5 mt-3 text-red-400 text-[11px] font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <!-- Resend Timer -->
                        <div class="mt-5 text-center h-5 flex items-center justify-center">
                            <template x-if="timer > 0">
                                <span class="text-xs font-medium text-zinc-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    ارسال مجدد کد تا <span class="text-orange-400 font-bold ml-1" x-text="timer < 10 ? '00:0' + timer : '00:' + timer"></span>
                                </span>
                            </template>
                            <template x-if="timer === 0">
                                <button type="button" @click="resend()" class="text-xs font-bold text-orange-500 hover:text-orange-400 transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    ارسال مجدد کد
                                </button>
                            </template>
                        </div>

                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full mt-4 py-3.5 rounded-xl text-sm font-bold text-zinc-900 bg-white hover:bg-zinc-200 transition-all duration-300 shadow-[0_0_15px_rgba(255,255,255,0.1)] hover:shadow-[0_0_25px_rgba(255,255,255,0.2)] hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="submitForm">تایید و ورود</span>
                        <span wire:loading wire:target="submitForm" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-zinc-900" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            درحال تایید...
                        </span>
                    </button>
                @endif

            <!-- STEP 4: Register -->
                @if($step === 'register')
                    <div class="flex items-center justify-between p-3.5 bg-zinc-950/40 border border-zinc-800/80 rounded-xl mb-4">
                        <div class="flex flex-col">
                            <span class="text-[11px] text-zinc-500 font-medium mb-0.5">حساب جدید برای:</span>
                            <span class="text-white font-bold tracking-widest text-sm" dir="ltr">{{ $identifier }}</span>
                        </div>
                        <button type="button" wire:click.prevent="resetIdentifier" wire:loading.attr="disabled" class="text-[11px] font-bold text-zinc-400 hover:text-white px-3 py-1.5 rounded-lg bg-zinc-800/40 hover:bg-zinc-700 transition-colors duration-200 border border-white/5">
                            <span wire:loading.remove wire:target="resetIdentifier">تغییر</span>
                            <svg wire:loading wire:target="resetIdentifier" class="w-3 h-3 animate-spin mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-3.5">
                        <!-- Name -->
                        <div>
                            <div class="relative group">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-zinc-500 group-focus-within:text-emerald-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <input wire:model="name" type="text" class="block w-full pr-11 pl-4 py-3 bg-zinc-950/50 border border-zinc-800 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:bg-zinc-900/80 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-300 text-sm font-medium shadow-inner" placeholder="نام و نام خانوادگی">
                            </div>
                            @error('name') <span class="text-red-400 text-[11px] mt-1.5 block font-bold px-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <div class="relative group">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-zinc-500 group-focus-within:text-emerald-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <input wire:model="email" type="email" dir="ltr" class="block w-full pr-11 pl-4 py-3 bg-zinc-950/50 border border-zinc-800 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:bg-zinc-900/80 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-300 text-sm font-medium shadow-inner" placeholder="ایمیل (اختیاری)">
                            </div>
                            @error('email') <span class="text-red-400 text-[11px] mt-1.5 block font-bold px-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <div class="relative group" x-data="{ show: false }">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-zinc-500 group-focus-within:text-emerald-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <input wire:model="password" :type="show ? 'text' : 'password'" dir="ltr" class="block w-full pr-11 pl-11 py-3 bg-zinc-950/50 border border-zinc-800 rounded-xl text-white placeholder-zinc-600 focus:outline-none focus:bg-zinc-900/80 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-300 text-sm font-medium tracking-wider shadow-inner" placeholder="تعیین رمز عبور">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-500 hover:text-zinc-300 focus:outline-none transition-colors">
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                </button>
                            </div>
                            @error('password') <span class="text-red-400 text-[11px] mt-1.5 block font-bold px-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- OTP Block -->
                    <div class="pt-5 border-t border-white/5 mt-5">
                        <label class="block w-full text-center text-xs font-bold text-zinc-400 mb-3">کد تایید پیامکی را وارد کنید</label>

                        <div dir="ltr" x-data="{
                            otpArray: ['', '', '', '', ''],
                            timer: 45,
                            interval: null,
                            init() {
                                this.$watch('otpArray', val => { $wire.set('otp', val.join('')); });
                                this.startTimer();
                            },
                            startTimer() {
                                this.timer = 45;
                                clearInterval(this.interval);
                                this.interval = setInterval(() => {
                                    if(this.timer > 0) this.timer--;
                                    else clearInterval(this.interval);
                                }, 1000);
                            },
                            resend() {
                                // اگر در بک‌اند متد ارسال مجدد OTP دارید، آن را اینجا صدا بزنید
                                if(this.timer === 0) {
                                    this.startTimer();
                                }
                            },
                            onInput(index, e) {
                                let val = e.target.value.replace(/[^0-9]/g, '');
                                this.otpArray[index] = val.slice(-1);
                                if (val && index < 4) { this.$refs['box_' + (index + 1)].focus(); }
                            },
                            onKeydown(index, e) {
                                if (e.key === 'Backspace' && !this.otpArray[index] && index > 0) {
                                    this.$refs['box_' + (index - 1)].focus();
                                }
                            },
                            onPaste(e) {
                                let data = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 5);
                                for(let i=0; i<data.length; i++) { this.otpArray[i] = data[i]; }
                                let nextFocus = Math.min(data.length, 4);
                                if (this.$refs['box_' + nextFocus]) { this.$refs['box_' + nextFocus].focus(); }
                            }
                        }">
                            <div class="flex justify-center gap-2 sm:gap-3 mb-2">
                                @for($i = 0; $i < 5; $i++)
                                    <input type="text" inputmode="numeric" x-ref="box_{{ $i }}" x-model="otpArray[{{ $i }}]" @input="onInput({{ $i }}, $event)" @keydown="onKeydown({{ $i }}, $event)" @paste.prevent="onPaste($event)" class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl font-black bg-zinc-950/50 border border-zinc-800 rounded-xl text-white focus:outline-none focus:bg-zinc-900/80 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 focus:scale-[1.03] transition-all duration-200 shadow-inner" placeholder="-">
                                @endfor
                            </div>
                            @error('otp')
                            <div class="flex items-center justify-center gap-1.5 mt-2 text-red-400 text-[11px] font-bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <!-- Simple visual timer for Register OTP -->
                            <div class="mt-3 text-center h-4 flex items-center justify-center">
                                <template x-if="timer > 0">
                                    <span class="text-[11px] font-medium text-zinc-500">
                                        ارسال مجدد تا <span class="text-emerald-500 font-bold ml-0.5" x-text="timer < 10 ? '00:0' + timer : '00:' + timer"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 mt-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 transition-all duration-300 shadow-[0_8px_20px_-6px_rgba(16,185,129,0.3)] hover:shadow-[0_12px_25px_-8px_rgba(16,185,129,0.5)] hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="submitForm">ایجاد حساب و اتصال</span>
                        <span wire:loading wire:target="submitForm" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            در حال ایجاد...
                        </span>
                    </button>
                @endif
            </form>

            <!-- Trust Indicators -->
            <div class="mt-8 pt-5 border-t border-white/5 flex items-center justify-center gap-5 sm:gap-8 text-zinc-500 text-[11px] font-bold">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    اتصال امن
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    شبکه پایدار
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    سرورهای ابری
                </div>
            </div>

        </div>
    </div>
</div>
