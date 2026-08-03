<div class="animate-fade-in pb-20 max-w-6xl mx-auto px-4 sm:px-6">

    <div class="text-center pt-12 pb-8">
        @if($storeData['logo'])
            <img src="{{ $storeData['logo'] }}" class="w-16 h-16 mx-auto rounded-2xl object-cover shadow-lg shadow-black/20 mb-6">
        @else
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-orange-500/30 mb-6">
                {{ mb_substr($storeData['brand_name'], 0, 1) }}
            </div>
        @endif
        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">{{ $storeData['brand_name'] }}</h1>
        <p class="text-zinc-400 mt-3 text-sm max-w-md mx-auto">{{ $storeData['description'] }}</p>
    </div>

    @if(!session()->has('success_order'))
        <div class="max-w-2xl mx-auto mb-10" dir="rtl">
            <div class="flex items-center justify-between relative">
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-full h-1 bg-zinc-800 rounded-full z-0"></div>
                <div class="absolute right-0 top-1/2 -translate-y-1/2 h-1 bg-orange-500 rounded-full z-0 transition-all duration-500" style="width: {{ ($step - 1) * 50 }}%"></div>

                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= 1 ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'bg-zinc-800 text-zinc-500' }}">1</div>
                    <span class="text-[10px] font-bold {{ $step >= 1 ? 'text-white' : 'text-zinc-500' }}">انتخاب سرویس</span>
                </div>
                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= 2 ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'bg-zinc-800 text-zinc-500' }}">2</div>
                    <span class="text-[10px] font-bold {{ $step >= 2 ? 'text-white' : 'text-zinc-500' }}">تایید مشخصات</span>
                </div>
                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= 3 ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'bg-zinc-800 text-zinc-500' }}">3</div>
                    <span class="text-[10px] font-bold {{ $step >= 3 ? 'text-white' : 'text-zinc-500' }}">پرداخت</span>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('success_order'))
        <div class="max-w-md mx-auto bg-[#111827] border border-emerald-500/30 rounded-3xl p-8 text-center shadow-2xl shadow-emerald-500/10 animate-fade-in">
            <div class="w-20 h-20 mx-auto bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-black text-white mb-2">سفارش با موفقیت ثبت شد!</h3>
            <p class="text-xs text-zinc-400 mb-6 leading-relaxed">{{ session('success_order') }}</p>

            @if(session()->has('new_account'))
                <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-5 mb-6 text-right space-y-3">
                    <p class="text-[10px] text-amber-400 font-bold text-center mb-2">⚠️ لطفاً اطلاعات زیر را یادداشت کنید</p>
                    <div class="flex justify-between items-center border-b border-zinc-800 pb-2">
                        <span class="text-xs text-zinc-500">شماره موبایل (نام کاربری):</span>
                        <span class="font-mono text-white font-bold">{{ session('username') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-xs text-zinc-500">رمز عبور ورود به پنل:</span>
                        <span class="font-mono text-emerald-400 text-lg font-black tracking-widest">{{ session('password') }}</span>
                    </div>
                </div>
            @endif

            <a href="{{ route('login') }}" class="block w-full py-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-sm transition">
                ورود به داشبورد من
            </a>
            <button wire:click="$set('step', 1); session()->forget('success_order');" class="mt-4 text-xs text-zinc-500 hover:text-white transition">ثبت سفارش جدید</button>
        </div>
    @else

        @if($step === 1)
            <div class="animate-fade-in">
                <div class="flex flex-wrap justify-center gap-2 mb-8">
                    <button wire:click="$set('selectedProtocol', 'all')" class="px-5 py-2.5 rounded-full text-xs font-bold transition {{ $selectedProtocol === 'all' ? 'bg-white text-black shadow-md' : 'bg-zinc-900/50 text-zinc-400 hover:text-white border border-zinc-800' }}">همه سرویس‌ها</button>
                    <button wire:click="$set('selectedProtocol', 'wireguard')" class="px-5 py-2.5 rounded-full text-xs font-bold transition flex items-center gap-2 {{ $selectedProtocol === 'wireguard' ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'bg-zinc-900/50 text-zinc-400 hover:text-white border border-zinc-800' }}">
                        <span>وایرگارد</span> <span class="bg-white/20 px-1.5 py-0.5 rounded text-[8px]">WireGuard</span>
                    </button>
                    <button wire:click="$set('selectedProtocol', 'l2tp_openvpn')" class="px-5 py-2.5 rounded-full text-xs font-bold transition {{ $selectedProtocol === 'l2tp_openvpn' ? 'bg-blue-500 text-white shadow-md shadow-blue-500/20' : 'bg-zinc-900/50 text-zinc-400 hover:text-white border border-zinc-800' }}">L2TP / OpenVPN</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($plans as $plan)
                        <div wire:key="plan-{{ $plan->id }}" class="bg-[#111827] border border-zinc-800 rounded-3xl p-6 flex flex-col hover:border-orange-500/50 transition-all duration-300 shadow-xl cursor-pointer" wire:click="selectPlan({{ $plan->id }})">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-bold text-white">{{ $plan->name }}</h3>
                                @php
                                    $n = strtolower($plan->name);
                                    $isWg = str_contains($n, 'wireguard') || str_contains($n, 'وایرگارد') || str_contains($n, 'wg');
                                @endphp
                                @if($isWg)
                                    <span class="bg-orange-500/10 text-orange-500 text-[10px] font-black px-2 py-1 rounded-md uppercase">WireGuard</span>
                                @endif
                            </div>

                            <div class="mb-6">
                                <span class="text-3xl font-black text-white font-mono">{{ number_format($plan->final_sell_price) }}</span>
                                <span class="text-xs text-zinc-500">تومان</span>
                            </div>

                            <ul class="space-y-3 mb-6 text-sm text-zinc-400 flex-1">
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg> حجم: <strong class="text-white">{{ $plan->group_volume > 0 ? $plan->group_volume . ' گیگابایت' : 'نامحدود' }}</strong></li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg> اعتبار: <strong class="text-white">{{ $plan->expire_value ?? '30' }} {{ $plan->expire_type === 'days' ? 'روز' : 'ماه' }}</strong></li>
                                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg> کاربر همزمان: <strong class="text-white">{{ $plan->multi_login ?? 1 }} کاربر</strong></li>
                            </ul>

                            <button class="w-full py-3 rounded-xl bg-zinc-800 text-white font-bold text-sm hover:bg-orange-500 hover:shadow-lg hover:shadow-orange-500/20 transition-all">انتخاب سرویس</button>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-zinc-500">سرویسی برای این پروتکل یافت نشد.</div>
                    @endforelse
                </div>
            </div>
        @endif

        @if($step === 2)
            <div class="max-w-md mx-auto bg-[#111827] border border-zinc-800 rounded-3xl p-6 shadow-xl animate-fade-in">
                <div class="bg-zinc-900/50 p-4 rounded-2xl mb-6 flex justify-between items-center border border-zinc-800/50">
                    <div>
                        <p class="text-[10px] text-zinc-500 mb-1">سرویس انتخابی</p>
                        <p class="text-sm font-bold text-white">{{ $selectedPlan->name }}</p>
                    </div>
                    <button wire:click="previousStep" class="text-xs text-orange-500 hover:text-orange-400 font-bold">تغییر سرویس</button>
                </div>

                @if(Auth::check())
                    <div class="text-center py-6">
                        <div class="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-500 mx-auto flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-white font-bold">{{ Auth::user()->name }} عزیز</h3>
                        <p class="text-xs text-zinc-400 mt-2">شما وارد سیستم شده‌اید. برای پرداخت به مرحله بعد بروید.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">نام و نام خانوادگی <span class="text-rose-500">*</span></label>
                            <input wire:model="name" type="text" class="w-full bg-[#09090b] border border-zinc-800 text-white rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-500">
                            @error('name') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">شماره موبایل (جهت دریافت مشخصات) <span class="text-rose-500">*</span></label>
                            <input wire:model="phone" type="text" placeholder="09xxxxxxxxx" class="w-full bg-[#09090b] border border-zinc-800 text-white rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-500 font-mono" dir="ltr">
                            @error('phone') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <p class="text-[10px] text-zinc-500 text-center mt-2">حساب کاربری شما بر اساس شماره موبایل به صورت خودکار ساخته خواهد شد.</p>
                    </div>
                @endif

                <div class="flex gap-3 mt-8">
                    <button wire:click="previousStep" class="px-5 py-3 rounded-xl bg-zinc-800 text-white font-bold text-sm hover:bg-zinc-700 transition">بازگشت</button>
                    <button wire:click="goToPayment" class="flex-1 py-3 rounded-xl bg-white text-black font-black text-sm hover:bg-zinc-200 transition">تایید و ادامه</button>
                </div>
            </div>
        @endif

        @if($step === 3)
            <div class="max-w-md mx-auto bg-[#111827] border border-zinc-800 rounded-3xl p-6 shadow-xl animate-fade-in">

                <div class="text-center mb-6">
                    <p class="text-xs text-zinc-400 font-bold mb-1">مبلغ قابل پرداخت</p>
                    <p class="text-3xl text-orange-500 font-black font-mono">{{ number_format($selectedPlanPrice) }} <span class="text-xs text-zinc-500 font-sans">تومان</span></p>
                </div>

                @if(Auth::check())
                    <div class="flex gap-2 mb-6 p-1 bg-zinc-900 rounded-xl">
                        <button wire:click="$set('paymentMethod', 'wallet')" class="flex-1 py-2 rounded-lg text-xs font-bold transition {{ $paymentMethod === 'wallet' ? 'bg-zinc-800 text-white shadow' : 'text-zinc-500' }}">کیف پول</button>
                        <button wire:click="$set('paymentMethod', 'receipt')" class="flex-1 py-2 rounded-lg text-xs font-bold transition {{ $paymentMethod === 'receipt' ? 'bg-zinc-800 text-white shadow' : 'text-zinc-500' }}">فیش بانکی</button>
                    </div>

                    @if($paymentMethod === 'wallet')
                        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5 text-center mb-6">
                            <p class="text-xs text-emerald-500 font-bold mb-2">موجودی کیف پول شما:</p>
                            <p class="text-xl text-white font-black font-mono">{{ number_format(Auth::user()->balance) }} تومان</p>
                            @error('wallet') <p class="text-[10px] text-rose-500 font-bold mt-2 bg-rose-500/10 p-2 rounded-lg">{{ $message }}</p> @enderror
                        </div>
                    @endif
                @endif

                @if($paymentMethod === 'receipt')
                    <div class="bg-blue-500/5 border border-blue-500/20 rounded-2xl p-4 text-center mb-6">
                        <p class="text-[11px] text-blue-400 font-bold mb-3">شماره کارت جهت واریز:</p>
                        @if($bankDetails)
                            <div class="text-xl font-black text-white font-mono tracking-widest mb-1" dir="ltr">{{ $bankDetails->card_number }}</div>
                            <p class="text-[10px] text-zinc-400">به نام: {{ $bankDetails->account_name }}</p>
                        @else
                            <p class="text-xs text-rose-400">اطلاعات حساب ثبت نشده است!</p>
                        @endif
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">آپلود فیش واریزی <span class="text-rose-500">*</span></label>
                        <input wire:model="receipt" type="file" accept="image/*" class="w-full bg-[#09090b] border border-zinc-800 text-zinc-400 rounded-xl px-4 py-2.5 text-xs focus:ring-1 focus:ring-orange-500 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-zinc-800 file:text-white hover:file:bg-zinc-700 cursor-pointer">
                        <div wire:loading wire:target="receipt" class="text-[10px] text-amber-500 mt-2 font-bold animate-pulse">در حال آپلود فیش...</div>
                        @error('receipt') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="flex gap-3">
                    <button wire:click="previousStep" class="px-5 py-3 rounded-xl bg-zinc-800 text-white font-bold text-sm hover:bg-zinc-700 transition">بازگشت</button>
                    <button wire:click="submitOrder" class="flex-1 py-3 rounded-xl {{ $paymentMethod === 'wallet' ? 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20' : 'bg-orange-500 hover:bg-orange-600 shadow-orange-500/20' }} text-white font-black text-sm transition shadow-lg flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="submitOrder" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>{{ $paymentMethod === 'wallet' ? 'پرداخت نهایی و دریافت سرویس' : 'ارسال فیش و ثبت سفارش' }}</span>
                    </button>
                </div>
            </div>
        @endif

    @endif
</div>
