<div class="relative w-full">

    @if (session()->has('success'))
        <div class="max-w-xl mx-auto mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center gap-2 text-emerald-400 font-bold text-sm shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-4 mb-12">
        <button wire:click="setProtocol('wireguard')" class="w-full sm:w-auto px-8 py-3.5 rounded-full text-sm font-bold transition-all duration-300 {{ $selectedProtocol === 'wireguard' ? 'bg-orange-600 text-white shadow-[0_0_20px_rgba(234,88,12,0.4)]' : 'bg-zinc-900/50 text-zinc-400 border border-zinc-800 hover:text-white hover:bg-zinc-800' }}">
            سرویس‌های وایرگارد (WireGuard)
        </button>
        <button wire:click="setProtocol('l2tp_openvpn')" class="w-full sm:w-auto px-8 py-3.5 rounded-full text-sm font-bold transition-all duration-300 {{ $selectedProtocol === 'l2tp_openvpn' ? 'bg-red-600 text-white shadow-[0_0_20px_rgba(220,38,38,0.4)]' : 'bg-zinc-900/50 text-zinc-400 border border-zinc-800 hover:text-white hover:bg-zinc-800' }}">
            سرویس‌های L2TP / OpenVPN
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-w-7xl mx-auto relative z-10" wire:loading.class="opacity-50 pointer-events-none" wire:target="setProtocol">
        @forelse($packages as $package)
            <div class="relative bg-zinc-900/60 backdrop-blur-md border border-zinc-800/80 rounded-[2rem] p-6 sm:p-8 flex flex-col transition-all duration-300 hover:border-orange-500/50 hover:shadow-[0_0_30px_rgba(249,115,22,0.15)] hover:-translate-y-2 group">

                <div class="mb-6">
                    <h3 class="text-xl font-black text-white mb-2">{{ $package->name }}</h3>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-l from-orange-400 to-red-500 font-mono">
                            {{ number_format($package->final_sell_price ?? $package->price) }}
                        </span>
                        <span class="text-sm text-zinc-500 font-medium">تومان</span>
                    </div>
                </div>

                <ul class="space-y-4 mb-8 flex-1">
                    <li class="flex items-center gap-3 text-sm text-zinc-300">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>ترافیک: <strong class="text-white">{{ $package->group_volume > 0 ? $package->group_volume." GB" : 'نامحدود' }}</strong></span>
                    </li>
                    <li class="flex items-center gap-3 text-sm text-zinc-300">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>کاربر همزمان: <strong class="text-white font-mono">{{ $package->multi_login ?? 'آزاد' }}</strong> دستگاه</span>
                    </li>
                    <li class="flex items-center gap-3 text-sm text-zinc-300">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>پروتکل: <strong class="text-white uppercase" dir="ltr">{{ $selectedProtocol === 'wireguard' ? 'WireGuard' : 'L2TP / Open' }}</strong></span>
                    </li>
                </ul>

                <a href="{{route('store.index')}}" wire:navigate class="w-full block text-center py-3.5 rounded-xl font-bold text-sm bg-zinc-950 border border-zinc-800 text-zinc-300 group-hover:bg-gradient-to-r group-hover:from-orange-600 group-hover:to-red-600 group-hover:text-white group-hover:border-transparent transition-all duration-300 relative overflow-hidden">
                    <span wire:loading.remove wire:target="openBuyModal({{ $package->id }})">خرید و تحویل آنی</span>
                    <span wire:loading wire:target="openBuyModal({{ $package->id }})" class="animate-pulse">درحال پردازش...</span>
                </a>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-zinc-900/30 rounded-3xl border border-zinc-800 border-dashed">
                <svg class="w-12 h-12 text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <h3 class="text-lg font-bold text-zinc-400">هیچ تعرفه‌ای برای این پروتکل ثبت نشده است.</h3>
            </div>
        @endforelse
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in text-right">
            <div class="relative w-full max-w-md bg-zinc-900/90 border border-zinc-700/60 rounded-[2rem] shadow-2xl overflow-hidden backdrop-blur-xl">

                <div class="flex items-center justify-between px-6 py-5 border-b border-white/5">
                    <h2 class="text-base font-black text-white">انتخاب شناسه کاربری</h2>
                    <button wire:click="$set('showModal', false)" class="text-zinc-500 hover:text-white bg-zinc-800/50 hover:bg-zinc-700 p-2 rounded-full transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-xs font-medium text-zinc-400 mb-6 leading-relaxed">
                        برای اتصال به پروتکل‌های <strong class="text-white">Cisco / L2TP</strong> باید یک نام کاربری و رمز عبور دلخواه (به زبان انگلیسی) برای خود تعیین کنید.
                    </p>

                    <form wire:submit.prevent="confirmAndPay" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-2">نام کاربری دلخواه (Username)</label>
                            <input wire:model="customUsername" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700/50 text-white rounded-xl p-4 text-sm font-mono focus:ring focus:ring-orange-500/20 focus:border-orange-500/50 outline-none transition-all shadow-inner" placeholder="مثلاً: ali_123">
                            @error('customUsername') <span class="text-red-400 text-[11px] font-bold block mt-1.5">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-2">رمز عبور دلخواه (Password)</label>
                            <input wire:model="customPassword" type="password" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700/50 text-white rounded-xl p-4 text-sm font-mono focus:ring focus:ring-orange-500/20 focus:border-orange-500/50 outline-none transition-all shadow-inner" placeholder="حداقل ۶ کاراکتر">
                            @error('customPassword') <span class="text-red-400 text-[11px] font-bold block mt-1.5">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 mt-4 border-t border-white/5 flex gap-3">
                            <button type="button" wire:click="$set('showModal', false)" class="flex-1 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-sm rounded-xl transition-colors">انصراف</button>
                            <button type="submit" wire:loading.attr="disabled" class="flex-1 py-3 bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/25 transition-all flex justify-center items-center gap-2">
                                <span wire:loading.remove wire:target="confirmAndPay">ثبت و پرداخت</span>
                                <svg wire:loading wire:target="confirmAndPay" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
