<div class="min-h-[85vh] w-full flex items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-10 right-1/4 w-[500px] h-[500px] bg-orange-600/10 rounded-full blur-[120px] mix-blend-screen"></div>
        <div class="absolute bottom-10 left-1/4 w-[400px] h-[400px] bg-red-600/10 rounded-full blur-[100px] mix-blend-screen"></div>
    </div>

    <div class="w-full max-w-5xl bg-zinc-900/60 backdrop-blur-2xl rounded-[2rem] border border-zinc-800/60 shadow-[0_0_50px_rgba(0,0,0,0.5)] overflow-hidden flex flex-col md:flex-row relative z-10">

        <div class="hidden md:flex md:w-5/12 bg-zinc-950/80 p-12 flex-col justify-between relative overflow-hidden border-l border-zinc-800/50">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 via-transparent to-transparent"></div>
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-orange-500/10 rounded-full blur-[60px]"></div>

            <div class="relative z-10">
                <svg class="w-14 h-14 text-orange-500 mb-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 22l10-6 10 6L12 2z"></path>
                </svg>
                <h3 class="text-3xl font-black text-white leading-[1.4] mb-4">
                    استاندارد جدید<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-l from-orange-400 to-red-500">ارتباطات آزاد</span>
                </h3>
                <p class="text-zinc-400 text-sm leading-relaxed">
                    با ورود به شبکه اختصاصی همراه سیمرغ، مرزهای اینترنت را پشت سر بگذارید و اتصالی امن، پایدار و با پینگ طلایی را تجربه کنید.
                </p>
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 text-xs text-zinc-500 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    سرورهای ابری در حال کار
                </div>
            </div>
        </div>

        <div class="w-full md:w-7/12 p-8 md:p-14 relative flex flex-col justify-center">

            <div class="mb-10">
                <h2 class="text-2xl font-black text-white mb-2">
                    @if($step === 'check_identifier') خوش آمدید
                    @elseif($step === 'register') ایجاد حساب کاربری
                    @else بازگشت به داشبورد
                    @endif
                </h2>
                <p class="text-sm text-zinc-400">برای ادامه، اطلاعات خود را تکمیل کنید.</p>
            </div>

            <form wire:submit.prevent="submitForm" method="POST" wire:key="main-auth-form" class="space-y-6">

                @if($step === 'check_identifier')
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input wire:model="identifier" type="text" dir="ltr" class="block w-full pr-12 pl-4 py-4 bg-zinc-950/50 border border-zinc-700/80 rounded-2xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all text-base tracking-wider font-bold shadow-inner" placeholder="ایمیل یا شماره موبایل">
                        </div>
                        @error('identifier') <span class="text-red-500 text-xs mt-2 block font-medium px-2">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="w-full py-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 transition-all shadow-[0_10px_20px_-10px_rgba(249,115,22,0.5)]">
                        <span wire:loading.remove wire:target="checkIdentifier">تایید و ادامه</span>
                        <span wire:loading wire:target="checkIdentifier" class="animate-pulse">در حال بررسی...</span>
                    </button>
                @endif

                @if($step === 'login_password')
                    <div class="flex items-center justify-between p-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-500/10 flex items-center justify-center text-orange-500">
                                @if($loginType === 'email')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                @endif
                            </div>
                            <span class="text-zinc-200 font-bold tracking-wider" dir="ltr">{{ Str::limit($identifier, 25) }}</span>
                        </div>
                        <button type="button" wire:click.prevent="resetIdentifier" class="text-xs font-bold text-orange-500 hover:text-orange-400 px-3 py-1.5 rounded-lg bg-orange-500/10 hover:bg-orange-500/20 transition">تغییر</button>
                    </div>

                    <div>
                        <div class="relative">
                            <input wire:model="password" type="password" dir="ltr" class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-700/80 rounded-2xl text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all text-center text-xl tracking-widest shadow-inner" placeholder="••••••••">
                        </div>
                        @error('password') <span class="text-red-500 text-xs mt-2 block font-medium px-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center">
                            <input wire:model="remember" id="remember" type="checkbox" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-zinc-700 rounded bg-zinc-950 cursor-pointer">
                            <label for="remember" class="ml-2 block text-sm text-zinc-400 cursor-pointer">مرا به خاطر بسپار</label>
                        </div>
                        <a href="#" class="text-sm font-medium text-orange-500 hover:text-orange-400">فراموشی رمز؟</a>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 transition-all shadow-[0_10px_20px_-10px_rgba(249,115,22,0.5)]">
                        ورود امن به پنل
                    </button>

                    @if($loginType === 'phone')
                        <div class="text-center pt-2">
                            <button type="button" wire:click.prevent="requestLoginOtp" class="text-sm font-medium text-zinc-400 hover:text-orange-400 transition">
                                ورود با <span class="text-orange-500 border-b border-orange-500/30 pb-0.5">کد تایید پیامکی</span>
                            </button>
                        </div>
                    @endif
                @endif

                @if($step === 'login_otp')
                    <div class="text-center mb-8">
                        <p class="text-sm text-zinc-400 mb-3">کد ۵ رقمی به <span class="font-bold text-zinc-100" dir="ltr">{{ $identifier }}</span> ارسال شد.</p>
                        <button type="button" wire:click.prevent="resetIdentifier" class="text-xs font-bold text-orange-500 hover:text-orange-400 px-4 py-1.5 rounded-lg bg-orange-500/10 transition">ویرایش اطلاعات</button>
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
                        }" class="flex justify-center gap-2 sm:gap-3 mb-2">

                            @for($i = 0; $i < 5; $i++)
                                <input type="text" inputmode="numeric" x-ref="box_{{ $i }}" x-model="otpArray[{{ $i }}]" @input="onInput({{ $i }}, $event)" @keydown="onKeydown({{ $i }}, $event)" @paste.prevent="onPaste($event)" class="w-12 h-14 sm:w-16 sm:h-20 text-center text-3xl font-black bg-zinc-950/80 border border-zinc-700/80 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all shadow-inner" placeholder="-">
                            @endfor

                        </div>
                        @error('otp') <span class="text-red-500 text-xs mt-3 block font-medium text-center">{{ $message }}</span> @enderror
                        <span class="text-orange-500/50 text-xs text-center block mt-3 font-medium">برای تست از کد 12345 استفاده کنید</span>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-2xl text-base font-bold text-white bg-zinc-100 hover:bg-white text-zinc-900 transition-all shadow-[0_0_20px_rgba(255,255,255,0.1)]">
                        تایید و ورود
                    </button>
                @endif

                @if($step === 'register')
                    <div class="flex items-center justify-between p-3 bg-zinc-950/50 border border-zinc-800 rounded-xl mb-2 text-sm">
                        <span class="text-zinc-400">حساب جدید برای: <span class="text-white font-bold tracking-wider" dir="ltr">{{ $identifier }}</span></span>
                        <button type="button" wire:click.prevent="resetIdentifier" class="text-xs font-bold text-orange-500 hover:text-orange-400">تغییر</button>
                    </div>

                    <div>
                        <input wire:model="name" type="text" class="block w-full px-4 py-3.5 bg-zinc-950/50 border border-zinc-700/80 rounded-xl text-white placeholder-zinc-500 focus:ring-2 focus:ring-orange-500 transition shadow-inner" placeholder="نام و نام خانوادگی">
                        @error('name') <span class="text-red-500 text-xs mt-1 block px-2">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input wire:model="email" type="email" dir="ltr" class="block w-full px-4 py-3.5 bg-zinc-950/50 border border-zinc-700/80 rounded-xl text-white placeholder-zinc-500 focus:ring-2 focus:ring-orange-500 transition shadow-inner" placeholder="آدرس ایمیل (اختیاری)">
                        @error('email') <span class="text-red-500 text-xs mt-1 block px-2">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input wire:model="password" type="password" dir="ltr" class="block w-full px-4 py-3.5 bg-zinc-950/50 border border-zinc-700/80 rounded-xl text-white placeholder-zinc-500 focus:ring-2 focus:ring-orange-500 transition shadow-inner" placeholder="تعیین رمز عبور دائم (حداقل ۶ کاراکتر)">
                        @error('password') <span class="text-red-500 text-xs mt-1 block px-2">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block w-full text-center text-sm font-medium text-zinc-400 mb-3">کد تایید پیامکی</label>

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
                        }" class="flex justify-center gap-2 sm:gap-3 mb-2">

                            @for($i = 0; $i < 5; $i++)
                                <input type="text" inputmode="numeric" x-ref="box_{{ $i }}" x-model="otpArray[{{ $i }}]" @input="onInput({{ $i }}, $event)" @keydown="onKeydown({{ $i }}, $event)" @paste.prevent="onPaste($event)" class="w-12 h-14 sm:w-16 sm:h-20 text-center text-3xl font-black bg-zinc-950/80 border border-zinc-700/80 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all shadow-inner" placeholder="-">
                            @endfor

                        </div>
                        @error('otp') <span class="text-red-500 text-xs mt-3 block font-medium text-center">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-4 mt-2 rounded-xl text-base font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 transition-all shadow-[0_10px_20px_-10px_rgba(16,185,129,0.5)]">
                        تکمیل عضویت و ورود
                    </button>
                @endif
            </form>

        </div>
    </div>
</div>
