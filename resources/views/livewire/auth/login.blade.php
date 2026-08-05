<div class="min-h-[85vh] w-full flex items-center justify-center p-4 md:p-8 relative overflow-hidden selection:bg-orange-500/30 selection:text-white">

    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-orange-600/10 rounded-full blur-[120px] mix-blend-screen animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-red-600/10 rounded-full blur-[100px] mix-blend-screen animate-pulse" style="animation-duration: 10s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[radial-gradient(ellipse_at_center,rgba(255,255,255,0.02)_0%,transparent_100%)]"></div>
    </div>

    <div class="w-full max-w-5xl bg-zinc-900/40 backdrop-blur-2xl rounded-[2.5rem] border border-white/[0.05] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.7)] overflow-hidden flex flex-col lg:flex-row relative z-10">

        <div class="hidden lg:flex lg:w-5/12 bg-zinc-950/80 p-12 xl:p-14 flex-col justify-between relative overflow-hidden border-l border-white/[0.02]">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 via-transparent to-red-500/5"></div>
            <div class="absolute -right-32 -top-32 w-96 h-96 bg-gradient-to-br from-orange-500/20 to-transparent rounded-full blur-[80px]"></div>

            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500/10 to-red-500/10 border border-orange-500/20 flex items-center justify-center mb-8 shadow-lg shadow-orange-500/5">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 22l10-6 10 6L12 2z"></path>
                    </svg>
                </div>
                <h3 class="text-3xl xl:text-4xl font-black text-white leading-[1.4] mb-5 tracking-tight">
                    استاندارد جدید<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-l from-orange-400 via-orange-500 to-red-500">ارتباطات آزاد</span>
                </h3>
                <p class="text-zinc-400 text-sm leading-relaxed font-medium">
                    با ورود به شبکه اختصاصی ، مرزهای اینترنت را پشت سر بگذارید و اتصالی امن، پایدار و با پینگ طلایی را تجربه کنید.
                </p>
            </div>

            <div class="relative z-10">
                <div class="inline-flex items-center gap-3 px-4 py-2.5 rounded-xl bg-zinc-900/50 border border-white/[0.05] text-xs text-zinc-300 font-bold backdrop-blur-md">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                    </span>
                    سرورهای ابری در حال کار
                </div>
            </div>
        </div>

        <div class="w-full lg:w-7/12 p-8 sm:p-12 xl:p-16 relative flex flex-col justify-center bg-zinc-900/20">

            <div class="mb-10">
                <h2 class="text-3xl font-black text-white mb-3 tracking-tight">
                    @if($step === 'check_identifier') خوش آمدید
                    @elseif($step === 'register') ایجاد حساب کاربری
                    @else بازگشت به پنل
                    @endif
                </h2>
                <p class="text-sm font-medium text-zinc-400">برای ادامه، اطلاعات کاربری خود را تکمیل کنید.</p>
            </div>

            <form wire:submit.prevent="submitForm" method="POST" wire:key="main-auth-form" class="space-y-6">

                @if($step === 'check_identifier')
                    <div class="group">
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none transition-colors group-focus-within:text-orange-500 text-zinc-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input wire:model="identifier" type="text" dir="ltr" class="block w-full pr-14 pl-5 py-4 bg-zinc-900/40 border border-zinc-700/50 rounded-2xl text-white placeholder-zinc-500 focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/80 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 text-base tracking-wider font-bold shadow-inner" placeholder="ایمیل یا شماره موبایل">
                        </div>
                        @error('identifier') <span class="text-red-400 text-xs mt-2 block font-bold px-2 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled" class="w-full py-4 mt-2 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-400 hover:to-red-400 transition-all duration-300 shadow-[0_8px_25px_-8px_rgba(249,115,22,0.6)] hover:shadow-[0_12px_35px_-12px_rgba(249,115,22,0.8)] hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="submitForm">تایید و ادامه</span>
                        <span wire:loading wire:target="submitForm" class="flex items-center gap-2">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            در حال بررسی...
                        </span>
                    </button>
                @endif

                @if($step === 'login_password')
                    <div class="flex items-center justify-between p-4 bg-zinc-900/40 border border-zinc-700/50 rounded-2xl mb-4 backdrop-blur-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400 shadow-inner">
                                @if($loginType === 'email')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                @endif
                            </div>
                            <span class="text-zinc-200 font-bold tracking-wider text-sm" dir="ltr">{{ Str::limit($identifier, 25) }}</span>
                        </div>
                        <button type="button" wire:click.prevent="resetIdentifier" wire:loading.attr="disabled" class="text-xs font-bold text-orange-400 hover:text-white px-3 py-1.5 rounded-lg bg-zinc-800/50 hover:bg-orange-500 transition-colors duration-200 border border-white/5 flex items-center gap-1">
                            <span wire:loading.remove wire:target="resetIdentifier">تغییر</span>
                            <svg wire:loading wire:target="resetIdentifier" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                        </button>
                    </div>

                    <div>
                        <div class="relative">
                            <input wire:model="password" type="password" dir="ltr" class="block w-full px-5 py-4 bg-zinc-900/40 border border-zinc-700/50 rounded-2xl text-white placeholder-zinc-600 focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/80 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 text-center text-2xl tracking-[0.3em] font-black shadow-inner" placeholder="••••••••">
                        </div>
                        @error('password') <span class="text-red-400 text-xs mt-2 block font-bold px-2 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-2.5">
                            <input wire:model="remember" id="remember" type="checkbox" class="w-4 h-4 text-orange-500 focus:ring-orange-500/50 focus:ring-offset-zinc-950 border-zinc-700 rounded bg-zinc-900/50 cursor-pointer transition-colors">
                            <label for="remember" class="block text-sm font-medium text-zinc-400 cursor-pointer hover:text-zinc-300 transition-colors">مرا به خاطر بسپار</label>
                        </div>
                        <a href="#" class="text-sm font-bold text-orange-500 hover:text-orange-400 transition-colors">فراموشی رمز؟</a>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full py-4 mt-2 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-400 hover:to-red-400 transition-all duration-300 shadow-[0_8px_25px_-8px_rgba(249,115,22,0.6)] hover:shadow-[0_12px_35px_-12px_rgba(249,115,22,0.8)] hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="submitForm">ورود امن به پنل</span>
                        <span wire:loading wire:target="submitForm" class="flex items-center gap-2">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            درحال ورود...
                        </span>
                    </button>

                    @if($loginType === 'phone')
                        <div class="text-center pt-4 border-t border-white/[0.05] mt-4">
                            <button type="button" wire:click.prevent="requestLoginOtp" wire:loading.attr="disabled" class="text-sm font-medium text-zinc-400 hover:text-orange-400 transition-colors group flex justify-center items-center w-full gap-2">
                                <span wire:loading.remove wire:target="requestLoginOtp" class="flex justify-center items-center w-full gap-2">
                                    ورود با <span class="font-bold text-orange-500 group-hover:text-orange-400 border-b border-orange-500/30 group-hover:border-orange-400 pb-0.5 transition-colors">کد تایید پیامکی</span>
                                    <svg class="w-4 h-4 text-orange-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                </span>
                                <span wire:loading wire:target="requestLoginOtp" class="flex justify-center items-center w-full gap-2 text-orange-500">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                                    درحال ارسال کد...
                                </span>
                            </button>
                        </div>
                    @endif
                @endif

                @if($step === 'login_otp')
                    <div class="text-center mb-8 p-6 bg-zinc-900/30 rounded-3xl border border-white/5 backdrop-blur-sm relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-b from-orange-500/5 to-transparent"></div>
                        <div class="relative z-10 flex flex-col items-center">
                            <p class="text-sm font-medium text-zinc-300 mb-4">کد ۵ رقمی ارسال شده به <span class="font-black text-white text-base tracking-wider" dir="ltr">{{ $identifier }}</span> را وارد کنید.</p>
                            <button type="button" wire:click.prevent="resetIdentifier" wire:loading.attr="disabled" class="text-xs font-bold text-zinc-400 hover:text-white px-4 py-2 rounded-xl bg-zinc-800/50 hover:bg-orange-500 transition-all border border-zinc-700/50 hover:border-orange-400 flex items-center gap-1.5">
                                <span wire:loading.remove wire:target="resetIdentifier">ویرایش شماره</span>
                                <svg wire:loading wire:target="resetIdentifier" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div dir="ltr" wire:key="otp-login-box" x-data="{
                            otpArray: ['', '', '', '', ''],
                            init() {
                                this.$watch('otpArray', val => { $wire.set('otp', val.join('')); });
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
                        }" class="flex justify-center gap-3 md:gap-4 mb-2">

                            @for($i = 0; $i < 5; $i++)
                                <input type="text" inputmode="numeric" x-ref="box_{{ $i }}" x-model="otpArray[{{ $i }}]" @input="onInput({{ $i }}, $event)" @keydown="onKeydown({{ $i }}, $event)" @paste.prevent="onPaste($event)" class="w-14 h-16 sm:w-16 sm:h-20 text-center text-3xl font-black bg-zinc-900/50 border border-zinc-700/50 rounded-2xl text-white focus:outline-none focus:bg-zinc-900 focus:border-orange-500/80 focus:ring-4 focus:ring-orange-500/20 transition-all duration-200 shadow-inner" placeholder="-">
                            @endfor

                        </div>
                        @error('otp') <span class="text-red-400 text-xs mt-3 block font-bold text-center">{{ $message }}</span> @enderror
                        <span class="text-zinc-500 text-xs text-center block mt-4 font-medium flex items-center justify-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> برای تست از کد 12345 استفاده کنید</span>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full mt-4 py-4 rounded-2xl text-base font-bold text-zinc-900 bg-white hover:bg-zinc-100 transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.15)] hover:shadow-[0_0_30px_rgba(255,255,255,0.25)] hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="submitForm">تایید و ورود</span>
                        <span wire:loading wire:target="submitForm" class="flex items-center gap-2">
                            <svg class="w-5 h-5 animate-spin text-zinc-900" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            درحال تایید...
                        </span>
                    </button>
                @endif

                @if($step === 'register')
                    <div class="flex items-center justify-between p-4 bg-zinc-900/40 border border-zinc-700/50 rounded-2xl mb-4 backdrop-blur-sm text-sm">
                        <span class="text-zinc-400 font-medium">حساب جدید برای: <span class="text-white font-bold tracking-wider ml-1" dir="ltr">{{ $identifier }}</span></span>
                        <button type="button" wire:click.prevent="resetIdentifier" wire:loading.attr="disabled" class="text-xs font-bold text-orange-400 hover:text-white px-3 py-1.5 rounded-lg bg-zinc-800/50 hover:bg-orange-500 transition-colors duration-200 border border-white/5 flex items-center gap-1">
                            <span wire:loading.remove wire:target="resetIdentifier">تغییر</span>
                            <svg wire:loading wire:target="resetIdentifier" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <input wire:model="name" type="text" class="block w-full px-5 py-4 bg-zinc-900/40 border border-zinc-700/50 rounded-2xl text-white placeholder-zinc-500 focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/80 focus:ring-4 focus:ring-orange-500/10 transition-all font-medium shadow-inner" placeholder="نام و نام خانوادگی">
                            @error('name') <span class="text-red-400 text-xs mt-2 block font-bold px-2">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <input wire:model="email" type="email" dir="ltr" class="block w-full px-5 py-4 bg-zinc-900/40 border border-zinc-700/50 rounded-2xl text-white placeholder-zinc-500 focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/80 focus:ring-4 focus:ring-orange-500/10 transition-all font-medium shadow-inner" placeholder="آدرس ایمیل (اختیاری)">
                            @error('email') <span class="text-red-400 text-xs mt-2 block font-bold px-2">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <input wire:model="password" type="password" dir="ltr" class="block w-full px-5 py-4 bg-zinc-900/40 border border-zinc-700/50 rounded-2xl text-white placeholder-zinc-500 focus:outline-none focus:bg-zinc-900/80 focus:border-orange-500/80 focus:ring-4 focus:ring-orange-500/10 transition-all font-medium shadow-inner" placeholder="تعیین رمز عبور (حداقل ۶ کاراکتر)">
                            @error('password') <span class="text-red-400 text-xs mt-2 block font-bold px-2">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/5 mt-6">
                        <label class="block w-full text-center text-sm font-bold text-zinc-300 mb-4">کد تایید پیامکی را وارد کنید</label>

                        <div dir="ltr" wire:key="otp-register-box" x-data="{
                            otpArray: ['', '', '', '', ''],
                            init() {
                                this.$watch('otpArray', val => { $wire.set('otp', val.join('')); });
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
                        }" class="flex justify-center gap-3 mb-2">

                            @for($i = 0; $i < 5; $i++)
                                <input type="text" inputmode="numeric" x-ref="box_{{ $i }}" x-model="otpArray[{{ $i }}]" @input="onInput({{ $i }}, $event)" @keydown="onKeydown({{ $i }}, $event)" @paste.prevent="onPaste($event)" class="w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-black bg-zinc-900/50 border border-zinc-700/50 rounded-2xl text-white focus:outline-none focus:bg-zinc-900 focus:border-emerald-500/80 focus:ring-4 focus:ring-emerald-500/20 transition-all duration-200 shadow-inner" placeholder="-">
                            @endfor

                        </div>
                        @error('otp') <span class="text-red-400 text-xs mt-3 block font-bold text-center">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full py-4 mt-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 transition-all duration-300 shadow-[0_8px_25px_-8px_rgba(16,185,129,0.6)] hover:shadow-[0_12px_35px_-12px_rgba(16,185,129,0.8)] hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="submitForm">تکمیل عضویت و ورود</span>
                        <span wire:loading wire:target="submitForm" class="flex items-center gap-2">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            درحال ثبت‌نام...
                        </span>
                    </button>
                @endif
            </form>

        </div>
    </div>
</div>
