<div class="space-y-6 animate-fade-in pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">مدیریت شیوه‌های پرداخت</h1>
            <p class="text-xs text-zinc-500 mt-1 font-medium">تعریف و مدیریت روش‌های پرداخت تومانی و ارزی (کریپتو)</p>
        </div>
        <button wire:click="openModal" class="px-5 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg> ثبت روش پرداخت جدید
        </button>
    </div>

    @if (session()->has('success'))
        <div class="p-4 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        @forelse($methods as $method)
            @php
                $isShow = isset($method->is_show) ? (bool)$method->is_show : true;
                $isCrypto = ($method->type ?? '') === 'crypto' || str_contains(strtolower($method->bank_name ?? ''), 'crypto') || str_contains(strtolower($method->account_name ?? ''), 'usdt');
            @endphp
            <div class="bg-zinc-900 border {{ $isShow ? ($isCrypto ? 'border-emerald-500/30' : 'border-orange-500/30') : 'border-zinc-800 opacity-60' }} rounded-3xl p-6 relative overflow-hidden shadow-xl transition-all">

                @if($isShow)
                    <div class="absolute top-0 right-0 w-1.5 h-full {{ $isCrypto ? 'bg-emerald-500' : 'bg-orange-500' }}"></div>
                @endif

                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $isCrypto ? 'bg-emerald-500/10 text-emerald-400' : 'bg-orange-500/10 text-orange-400' }} flex items-center justify-center font-bold text-lg">
                            {{ $isCrypto ? '🪙' : '💳' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-white text-sm">{{ $method->bank_name ?? ($isCrypto ? 'ارزی / کریپتو' : 'کارت بانکی') }}</h3>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full {{ $isCrypto ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-orange-500/10 text-orange-400 border border-orange-500/20' }}">
                                    {{ $isCrypto ? 'ارزی' : 'تومانی' }}
                                </span>
                            </div>

                            <!-- 👤 نمایش ایجاد کننده -->
                            <span class="text-[10px] text-zinc-500 block mt-1">
                                ایجادکننده: <strong class="text-zinc-400">{{ $method->creator_name }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- دکمه تغییر وضعیت نمایش -->
                        <button wire:click="toggleShow({{ $method->id }})" class="p-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-400 transition" title="{{ $isShow ? 'مخفی کردن' : 'نمایش دادن' }}">
                            @if($isShow)
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 01-6 0z"></path><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            @else
                                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.863c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"></path></svg>
                            @endif
                        </button>

                        <button wire:click="openModal({{ $method->id }})" class="text-zinc-500 hover:text-orange-400 transition" title="ویرایش"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                        <button wire:click="delete({{ $method->id }})" wire:confirm="آیا از حذف این حساب مطمئن هستید؟" class="text-zinc-500 hover:text-rose-500 transition" title="حذف"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                </div>

                <!-- مقدار حساب (شماره کارت یا آدرس ولت) -->
                <div class="bg-zinc-950 p-4 rounded-xl border border-zinc-800 text-center mb-3 relative group">
                    <p class="text-sm font-black text-white font-mono tracking-wider break-all select-all" dir="ltr">{{ $method->card_number }}</p>
                    <p class="text-xs text-zinc-400 mt-1.5 font-bold">
                        {{ $isCrypto ? 'شبکه / ارز:' : 'به نام:' }} {{ $method->account_name }}
                    </p>
                </div>

                @if($method->sheba_number && !$isCrypto)
                    <div class="text-[11px] text-zinc-500 font-mono text-center" dir="ltr">IR{{ $method->sheba_number }}</div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-zinc-500 bg-zinc-900/40 rounded-3xl border border-zinc-800/80">هیچ روش پرداختی ثبت نشده است!</div>
        @endforelse
    </div>

    <!-- مودال ثبت / ویرایش حساب -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-5 border-b border-zinc-800 flex justify-between items-center bg-zinc-950">
                    <h3 class="text-sm font-black text-white">{{ $methodId ? 'ویرایش روش پرداخت' : 'ثبت روش پرداخت جدید' }}</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-zinc-500 hover:text-rose-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4">

                    <!-- انتخاب نوع روش پرداخت -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">نوع پرداخت <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-zinc-950 rounded-xl border border-zinc-800">
                            <button type="button" wire:click="$set('type', 'card')" class="py-2.5 rounded-lg text-xs font-bold transition {{ $type === 'card' ? 'bg-orange-500 text-white shadow' : 'text-zinc-400 hover:text-white' }}">
                                💳 کارت بانکی (تومانی)
                            </button>
                            <button type="button" wire:click="$set('type', 'crypto')" class="py-2.5 rounded-lg text-xs font-bold transition {{ $type === 'crypto' ? 'bg-emerald-500 text-white shadow' : 'text-zinc-400 hover:text-white' }}">
                                🪙 پرداخت ارزی / کریپتو
                            </button>
                        </div>
                    </div>

                @if($type === 'card')
                    <!-- کارت بانکی تومانی -->
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">شماره کارت (۱۶ رقم) <span class="text-rose-500">*</span></label>
                            <input wire:model="card_number" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none font-mono text-center" dir="ltr" placeholder="6037990000000000">
                            @error('card_number') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-1.5">نام صاحب حساب <span class="text-rose-500">*</span></label>
                                <input wire:model="account_name" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none">
                                @error('account_name') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-1.5">نام بانک</label>
                                <input wire:model="bank_name" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none" placeholder="مثلاً: ملی">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">شماره شبا (بدون IR)</label>
                            <input wire:model="sheba_number" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none font-mono" dir="ltr" placeholder="012345678901234567890123">
                        </div>
                @else
                    <!-- پرداخت ارزی / کریپتو -->
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">عنوان ارز / شبکه <span class="text-rose-500">*</span></label>
                            <input wire:model="account_name" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-emerald-500 outline-none" placeholder="مثال: Tether (TRC20) یا TON">
                            @error('account_name') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">آدرس کیف‌پول (Wallet Address) <span class="text-rose-500">*</span></label>
                            <input wire:model="card_number" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-emerald-500 outline-none font-mono text-xs" dir="ltr" placeholder="TXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                            @error('card_number') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">توضیحات یا نام صرافی / ولت (اختیاری)</label>
                            <input wire:model="bank_name" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-emerald-500 outline-none" placeholder="مثلاً: ولت اختصاصی تتر">
                        </div>
                @endif

                <!-- توگل فعال/مخفی بودن -->
                    <div class="pt-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_show" class="sr-only peer">
                            <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 border border-zinc-700"></div>
                            <span class="ms-3 text-xs font-bold text-zinc-300">نمایش به مشتریان و نمایندگان</span>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-zinc-800">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="flex-1 py-3 bg-zinc-800 hover:bg-zinc-700 text-white text-sm font-bold rounded-xl transition">لغو</button>
                        <button type="submit" class="flex-1 py-3 {{ $type === 'crypto' ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-orange-600 hover:bg-orange-500' }} text-white text-sm font-black rounded-xl transition shadow-lg">ذخیره روش پرداخت</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
