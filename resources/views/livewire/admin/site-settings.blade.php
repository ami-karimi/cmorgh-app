<div class="space-y-6 pb-12 font-sans" wire:key="site-settings-view">

    <div class="relative overflow-hidden bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/60 rounded-[2rem] p-8 shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-zinc-800 to-zinc-900 border border-zinc-700/50 flex items-center justify-center text-orange-400 shadow-inner shadow-white/5">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight">تنظیمات کلان سیستم</h1>
                    <p class="text-xs font-medium text-zinc-400 mt-1">مدیریت برند، ظاهر، دسترسی‌ها و قوانین پلتفرم</p>
                </div>
            </div>

            <button wire:click="save" wire:loading.attr="disabled" class="px-8 py-3.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-[0_10px_20px_-10px_rgba(249,115,22,0.5)] transition-all flex justify-center items-center gap-2 shrink-0">
                <span wire:loading.remove wire:target="save">ذخیره تمام تنظیمات</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                    درحال ذخیره...
                </span>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="px-5 py-4 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl font-bold flex items-center gap-3 shadow-lg">
            <div class="p-1.5 bg-emerald-500/20 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <div class="lg:col-span-1 space-y-2">
            @php
                $tabs = [
                    'general' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'عمومی و برندینگ'],
                    'access'  => ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => 'دسترسی پروتکل‌ها'],
                    'support' => ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'title' => 'پشتیبانی و قوانین'],
                    'radius'  => ['icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01', 'title' => 'مدیریت RADIUS'],
                    'maintenance' => ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'title' => 'سلامت و پاکسازی'],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                <button type="button" wire:click="$set('activeTab', '{{ $key }}')" class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl text-sm font-bold transition-all duration-300 {{ $activeTab === $key ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'bg-zinc-900/40 text-zinc-400 border border-transparent hover:bg-zinc-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"></path></svg>
                    {{ $tab['title'] }}
                </button>
            @endforeach
        </div>

        <div class="lg:col-span-3 bg-zinc-900/40 backdrop-blur-xl border border-zinc-800/60 rounded-[2rem] p-8 shadow-xl relative min-h-[400px]">

            <div wire:loading wire:target="activeTab, $set('activeTab')" class="absolute inset-0 z-50 flex items-center justify-center bg-zinc-950/50 backdrop-blur-sm rounded-[2rem]">
                <svg class="w-8 h-8 animate-spin text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
            </div>

            @if($activeTab === 'radius')
                <livewire:admin.settings.radius-manager />
            @endif

            @if($activeTab === 'maintenance')
                <livewire:admin.settings.system-maintenance />
            @endif


            <form wire:submit.prevent="save">


                {{-- تب عمومی --}}
                @if($activeTab === 'general')
                    <div class="space-y-6 animate-fade-in">
                        <div>
                            <label class="block text-sm font-bold text-zinc-300 mb-2">عنوان سایت (SITE_TITLE)</label>
                            <input wire:model="site_title" type="text" class="w-full bg-zinc-950 border border-zinc-700/50 text-white rounded-xl p-4 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50 transition-all shadow-inner" placeholder="مثلاً: همراه سیمرغ ایران">
                            @error('site_title') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-zinc-300 mb-2">واترمارک کدهای QR (QR_WATRMARK)</label>
                            <input wire:model="qr_watermark" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700/50 text-zinc-300 rounded-xl p-4 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50 transition-all shadow-inner" placeholder="cmorgh VPN">
                        </div>

                        <div class="pt-6 border-t border-zinc-800/80">
                            <label class="block text-sm font-bold text-zinc-300 mb-4">لوگوی سایت (SITE_LOGO)</label>
                            <div class="flex items-center gap-6">
                                <div class="w-24 h-24 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center overflow-hidden shrink-0">
                                    @if ($logo)
                                        <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-cover">
                                    @elseif($current_logo)
                                        <img src="{{ $current_logo }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" wire:model="logo" id="logo_upload" class="hidden" accept="image/*">
                                    <label for="logo_upload" class="inline-block px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-medium text-sm rounded-xl cursor-pointer transition-colors border border-zinc-700">
                                        آپلود لوگوی جدید
                                    </label>
                                    <p class="text-xs text-zinc-500 mt-2">فرمت‌های مجاز: PNG, WEBP (پس‌زمینه ترنسپرنت پیشنهاد می‌شود)</p>
                                    @error('logo') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- تب دسترسی پروتکل‌ها --}}
                @if($activeTab === 'access')
                    <div class="space-y-4 animate-fade-in">

                        <div class="p-5 bg-zinc-950/50 border border-zinc-800/80 rounded-2xl flex items-center justify-between">
                            <div>
                                <h4 class="text-white font-bold text-sm mb-1">ساخت اکانت وایرگارد (WireGuard)</h4>
                                <p class="text-[11px] text-zinc-500">امکان ساخت اکانت وایرگارد برای نمایندگان فعال باشد.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:model="create_wg_account" class="sr-only peer">
                                <div class="w-12 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                        </div>

                        <div class="p-5 bg-zinc-950/50 border border-zinc-800/80 rounded-2xl flex items-center justify-between">
                            <div>
                                <h4 class="text-white font-bold text-sm mb-1">ساخت اکانت OpenVPN / L2TP</h4>
                                <p class="text-[11px] text-zinc-500">امکان ساخت اکانت OpenVPN برای نمایندگان فعال باشد.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:model="create_op_account" class="sr-only peer">
                                <div class="w-12 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                        </div>

                        <div class="p-5 bg-zinc-950/50 border border-zinc-800/80 rounded-2xl flex items-center justify-between">
                            <div>
                                <h4 class="text-white font-bold text-sm mb-1">ساخت اکانت V2Ray</h4>
                                <p class="text-[11px] text-zinc-500">امکان ساخت اکانت V2Ray برای نمایندگان فعال باشد.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:model="create_v2_account" class="sr-only peer">
                                <div class="w-12 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                        </div>

                        <div class="p-5 mt-4 bg-rose-500/5 border border-rose-500/20 rounded-2xl flex items-center justify-between">
                            <div>
                                <h4 class="text-rose-400 font-bold text-sm mb-1">فعال‌سازی حالت تعمیرات</h4>
                                <p class="text-[11px] text-zinc-400">سایت از دسترس خارج شده و فقط ادمین‌ها می‌توانند وارد شوند.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:model="maintenance_mode" class="sr-only peer">
                                <div class="w-12 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600 shadow-inner"></div>
                            </label>
                        </div>

                    </div>
                @endif

                {{-- تب پشتیبانی --}}
                @if($activeTab === 'support')
                    <div class="space-y-6 animate-fade-in">
                        <div>
                            <label class="block text-sm font-bold text-zinc-300 mb-2">لینک پشتیبانی تلگرام</label>
                            <input wire:model="telegram_support" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700/50 text-zinc-300 rounded-xl p-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 transition-all shadow-inner" placeholder="https://t.me/...">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-zinc-300 mb-2">متن قوانین و مقررات</label>
                            <textarea wire:model="rules_text" rows="6" class="w-full bg-zinc-950 border border-zinc-700/50 text-zinc-300 rounded-xl p-4 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50 transition-all shadow-inner leading-relaxed" placeholder="متن توافقنامه کاربری..."></textarea>
                        </div>
                    </div>
                @endif

            </form>
        </div>
    </div>
</div>
