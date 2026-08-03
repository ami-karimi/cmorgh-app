<div class="space-y-6 pb-12">
    <div>
        <h1 class="text-2xl font-black text-white tracking-wide">پیکربندی و تنظیمات سیستم</h1>
        <p class="text-xs text-zinc-500 mt-1 font-medium">مدیریت برندینگ، حساب‌های مالی، فروشگاه و تعرفه‌های اختصاصی</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 items-start mt-8">

        <div class="w-full lg:w-72 bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-3 shadow-lg shrink-0 sticky top-24">
            <nav class="space-y-1.5">
                <button wire:click="switchTab('branding')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-sm {{ $activeTab === 'branding' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    برندینگ و دامنه
                </button>

                <button wire:click="switchTab('financial')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-sm {{ $activeTab === 'financial' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    حساب‌های بانکی
                </button>

                <button wire:click="switchTab('store')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-sm {{ $activeTab === 'store' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    فروشگاه اختصاصی
                </button>

                <button wire:click="switchTab('pricing')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-sm {{ $activeTab === 'pricing' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    تعرفه و قیمت‌گذاری
                </button>
            </nav>
        </div>

        <div class="flex-1 w-full space-y-6">

            @if($activeTab === 'branding')
                <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl animate-fade-in">
                    <h2 class="text-lg font-bold text-white mb-2">شخصی‌سازی دامنه و ظاهر</h2>
                    <p class="text-xs text-zinc-500 mb-6">با ثبت دامنه اختصاصی، مشتریان متوجه نمی‌شوند که از سیستم واسط استفاده می‌کنید.</p>

                    @if(session('branding_msg'))
                        <div class="p-3 mb-6 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">{{ session('branding_msg') }}</div>
                    @endif

                    @if($domain_status === 'pending')
                        <div class="p-4 mb-6 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-xl font-bold text-xs flex items-center gap-2">
                            درخواست دامنه شما ({{ $custom_domain }}) در حال بررسی توسط مدیریت است.
                        </div>
                    @elseif($domain_status === 'approved')
                        <div class="p-4 mb-6 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-xl font-bold text-xs flex items-center gap-2">
                            دامنه شما تایید شده است ({{ $custom_domain }}).
                        </div>
                    @endif

                    <form wire:submit.prevent="saveBranding" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-1.5">نام برند شما</label>
                                <input wire:model="brand_name" type="text" placeholder="مثال: نت‌اسپید" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 focus:ring-1 focus:ring-orange-500">
                                @error('brand_name') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-1.5">دامنه اختصاصی</label>
                                <input wire:model="custom_domain" type="text" dir="ltr" placeholder="panel.yourdomain.com" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 focus:ring-1 focus:ring-orange-500 font-mono">
                                @error('custom_domain') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">لوگوی اختصاصی</label>
                            <div class="flex items-center gap-4">
                                @if($current_logo)
                                    <img src="{{ asset('storage/'.$current_logo) }}" class="w-14 h-14 rounded-xl object-cover border border-zinc-700 bg-zinc-900">
                                @endif
                                <input wire:model="logo" type="file" class="text-xs text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-zinc-800 file:text-white hover:file:bg-zinc-700 transition">
                            </div>
                        </div>

                        <div class="pt-4 text-left border-t border-zinc-800/80">
                            <button type="submit" class="px-6 py-3 bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm rounded-xl transition">ثبت و ذخیره تغییرات</button>
                        </div>
                    </form>
                </div>
            @endif

            @if($activeTab === 'financial')
                <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl animate-fade-in">
                    <h2 class="text-lg font-bold text-white mb-2">مدیریت شماره حساب‌های شما</h2>
                    <p class="text-xs text-zinc-500 mb-6">با ثبت کارت، مشتریان در فروشگاه شما می‌توانند مبلغ سرویس را مستقیماً به حساب شما واریز کنند.</p>

                    @if(session('bank_msg'))
                        <div class="p-3 mb-6 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">{{ session('bank_msg') }}</div>
                    @endif

                    <form wire:submit.prevent="saveBankAccount" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 bg-zinc-950/50 p-5 rounded-2xl border border-zinc-800/50">
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-1">نام بانک</label>
                            <input wire:model="bank_name" type="text" placeholder="مثال: بانک ملت" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-lg text-xs p-2.5">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-1">نام صاحب حساب</label>
                            <input wire:model="account_name" type="text" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-lg text-xs p-2.5">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-1">شماره 16 رقمی کارت</label>
                            <input wire:model="card_number" type="text" dir="ltr" placeholder="1234-5678-9012-3456" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-lg text-xs p-2.5 font-mono">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-1">شماره شبا (بدون IR)</label>
                            <input wire:model="sheba_number" type="text" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-lg text-xs p-2.5 font-mono">
                        </div>
                        <div class="md:col-span-2 text-left pt-2">
                            <button type="submit" class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-xs rounded-xl border border-zinc-700 transition">افزودن حساب جدید</button>
                        </div>
                    </form>

                    <div class="space-y-3">
                        @forelse($bankAccounts as $bank)
                            <div class="flex items-center justify-between p-4 bg-zinc-950 border border-zinc-800 rounded-xl">
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $bank->bank_name }} <span class="text-[10px] text-zinc-500">({{ $bank->account_name }})</span></p>
                                    <p class="text-xs text-zinc-400 font-mono mt-1">{{ $bank->card_number }}</p>
                                </div>
                                <button wire:click="deleteBankAccount({{ $bank->id }})" class="p-2 text-zinc-500 hover:text-red-400 rounded-lg text-xs">حذف</button>
                            </div>
                        @empty
                            <p class="text-xs text-zinc-500 text-center py-6">هیچ حساب بانکی ثبت نشده است.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            @if($activeTab === 'store')
                <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl animate-fade-in">
                    <h2 class="text-lg font-bold text-white mb-2">مدیریت فروشگاه آنلاین</h2>
                    <p class="text-xs text-zinc-500 mb-6">با فعال‌سازی فروشگاه، مشتریان با ورود به دامنه شما مستقیماً فرم خرید را می‌بینند.</p>

                    @if(session('store_msg'))
                        <div class="p-3 mb-6 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">{{ session('store_msg') }}</div>
                    @endif

                    <form wire:submit.prevent="saveStoreSettings" class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-zinc-950/50 border border-zinc-800 rounded-xl">
                            <div>
                                <span class="block text-sm font-bold text-white">وضعیت فروشگاه</span>
                                <span class="text-[10px] text-zinc-500">امکان خرید مستقیم روی دامنه شما فعال شود.</span>
                            </div>
                            <button wire:click="$toggle('is_store_active')" type="button" class="relative inline-flex items-center cursor-pointer">
                                <div class="w-11 h-6 rounded-full transition-colors {{ $is_store_active ? 'bg-emerald-500' : 'bg-zinc-700' }}"></div>
                                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $is_store_active ? 'translate-x-5' : '' }}"></div>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-1.5">عنوان فروشگاه (سئو)</label>
                                <input wire:model="store_title" type="text" placeholder="خرید آنلاین فیلترشکن" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-1.5">آیدی پشتیبانی تلگرام</label>
                                <input wire:model="support_id" type="text" dir="ltr" placeholder="SupportID" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 font-mono">
                            </div>
                        </div>

                        <div class="pt-4 text-left border-t border-zinc-800">
                            <button type="submit" class="px-6 py-3 bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm rounded-xl transition">ذخیره تنظیمات فروشگاه</button>
                        </div>
                    </form>
                </div>
            @endif

            @if($activeTab === 'pricing')
                <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl animate-fade-in space-y-8">
                    <div>
                        <h2 class="text-lg font-bold text-white mb-2">مدیریت تعرفه و قیمت‌گذاری</h2>
                        <p class="text-xs text-zinc-500">تعیین درصد سود برای زیرنمایندگان و قیمت فروش سرویس‌ها به مشتریان نهایی.</p>
                    </div>

                    <div class="bg-zinc-950/50 p-5 rounded-2xl border border-zinc-800/50 space-y-4">
                        <h3 class="text-sm font-bold text-white">درصد سود از شبکه‌ی زیرنمایندگان</h3>
                        @if(session('markup_msg'))
                            <div class="p-2.5 text-xs text-emerald-400 bg-emerald-500/10 rounded-lg">{{ session('markup_msg') }}</div>
                        @endif
                        <form wire:submit.prevent="saveMarkup" class="flex items-end gap-3">
                            <div class="flex-1">
                                <label class="block text-[11px] font-bold text-zinc-400 mb-1">درصد افزایش قیمت (Markup %)</label>
                                <input wire:model="sub_agent_markup" type="number" step="0.1" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-xl text-sm p-2.5 font-mono">
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-xs rounded-xl border border-zinc-700 transition">ذخیره سود</button>
                        </form>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-white">قیمت‌گذاری سرویس‌ها برای فروشگاه (مشتریان نهایی)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($availableGroups as $group)
                                @php
                                    $basePrice =  $group->price ?? 0;
                                    $agentCost = $group->getFinalPriceFor(auth()->user());
                                    $selling = $sellingPrices[$group->id] ?? $agentCost;
                                    $profit = max(0, $selling - $agentCost);
                                @endphp

                                <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-4 flex flex-col justify-between">
                                    <div class="mb-3">
                                        <span class="block text-sm font-bold text-white">{{ $group->name }}</span>
                                        <span class="text-[11px] text-zinc-400 font-mono mt-1 block">بهای تمام شده برای شما: <span class="text-emerald-400">{{ number_format(round($agentCost)) }} ت</span></span>
                                    </div>

                                    <form wire:submit.prevent="saveSellingPrice({{ $group->id }})" class="space-y-2 border-t border-zinc-900 pt-3">
                                        <div class="flex items-center gap-2">
                                            <input wire:model="sellingPrices.{{ $group->id }}" type="number" placeholder="قیمت فروش" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-lg text-xs p-2 font-mono">
                                            <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs rounded-lg transition whitespace-nowrap">ثبت</button>
                                        </div>
                                        <div class="flex justify-between items-center text-[10px] text-zinc-500">
                                            <span>سود هر فروش: <strong class="{{ $profit > 0 ? 'text-emerald-400' : 'text-zinc-400' }}">{{ number_format(round($profit)) }} تومان</strong></span>
                                            @if(session("price_msg_{$group->id}"))
                                                <span class="text-emerald-400 font-bold">{{ session("price_msg_{$group->id}") }}</span>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
