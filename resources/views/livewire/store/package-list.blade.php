<div class="relative w-full">

    @if (session()->has('success'))
        <div class="max-w-xl mx-auto mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center gap-2 text-emerald-400 font-bold text-sm shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-center gap-3 mb-8" aria-label="انتخاب پروتکل">
        <button type="button" wire:click="setProtocol('wireguard')" wire:loading.attr="disabled" wire:target="setProtocol" aria-pressed="{{ $selectedProtocol === 'wireguard' ? 'true' : 'false' }}" class="ui-button {{ $selectedProtocol === 'wireguard' ? '' : 'ui-button-secondary' }}">وایرگارد (WireGuard)</button>
        <button type="button" wire:click="setProtocol('l2tp_openvpn')" wire:loading.attr="disabled" wire:target="setProtocol" aria-pressed="{{ $selectedProtocol === 'l2tp_openvpn' ? 'true' : 'false' }}" class="ui-button {{ $selectedProtocol === 'l2tp_openvpn' ? '' : 'ui-button-secondary' }}">L2TP / OpenVPN</button>
    </div>
    <p wire:loading wire:target="setProtocol" role="status" class="text-center text-sm text-brand-300 mb-4">در حال دریافت پلن‌ها...</p>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" wire:loading.class="opacity-50" wire:target="setProtocol">
        @forelse($packages as $package)
            <article class="ui-card flex flex-col transition-colors hover:border-brand-500/50" wire:key="package-{{ $selectedProtocol }}-{{ $package->id }}">
                <span class="ui-eyebrow mb-3">{{ $selectedProtocol === 'wireguard' ? 'WireGuard' : 'L2TP / OpenVPN' }}</span>
                <h3 class="text-base font-bold text-zinc-100 leading-8 min-h-16">{{ $package->name }}</h3>
                <div class="flex flex-wrap items-baseline gap-2 mt-5 mb-6 pb-6 border-b border-zinc-800">
                    <span class="text-3xl font-bold text-zinc-50 font-mono-digit">{{ number_format($package->final_sell_price ?? $package->price) }}</span>
                    <span class="text-xs text-zinc-400">تومان</span>
                </div>
                <dl class="space-y-4 text-sm mb-7 flex-1">
                    <div class="flex justify-between gap-3"><dt class="text-zinc-400">ترافیک</dt><dd class="font-bold">{{ $package->group_volume > 0 ? $package->group_volume.' GB' : 'نامحدود' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-zinc-400">کاربر همزمان</dt><dd>{{ $package->multi_login ?? 'آزاد' }} دستگاه</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-zinc-400">پروتکل اتصال</dt><dd dir="ltr">{{ $selectedProtocol === 'wireguard' ? 'WireGuard' : 'L2TP / OpenVPN' }}</dd></div>
                </dl>
                <a href="{{ route('store.index') }}" wire:navigate class="ui-button ui-button-secondary w-full">انتخاب و خرید اشتراک <span aria-hidden="true">←</span></a>
            </article>
        @empty
            <div class="col-span-full ui-card text-center py-12"><h3 class="font-bold text-zinc-300">فعلاً پلنی برای این پروتکل موجود نیست.</h3><p class="ui-eyebrow mt-3">پروتکل دیگری را انتخاب کنید یا با پشتیبانی تماس بگیرید.</p></div>
        @endforelse
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in text-right">
            <div class="relative w-full max-w-md bg-zinc-900/90 border border-zinc-700/60 rounded-[1.25rem] shadow-2xl overflow-hidden backdrop-blur-xl">

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
                            <input wire:model="customUsername" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700/50 text-white rounded-xl p-4 text-sm font-mono focus:ring focus:ring-brand-500/20 focus:border-brand-500/50 outline-none transition-all shadow-inner" placeholder="مثلاً: ali_123">
                            @error('customUsername') <span class="text-red-400 text-[11px] font-bold block mt-1.5">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-2">رمز عبور دلخواه (Password)</label>
                            <input wire:model="customPassword" type="password" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700/50 text-white rounded-xl p-4 text-sm font-mono focus:ring focus:ring-brand-500/20 focus:border-brand-500/50 outline-none transition-all shadow-inner" placeholder="حداقل ۶ کاراکتر">
                            @error('customPassword') <span class="text-red-400 text-[11px] font-bold block mt-1.5">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 mt-4 border-t border-white/5 flex gap-3">
                            <button type="button" wire:click="$set('showModal', false)" class="flex-1 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-sm rounded-xl transition-colors">انصراف</button>
                            <button type="submit" wire:loading.attr="disabled" class="flex-1 py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-brand-500/25 transition-all flex justify-center items-center gap-2">
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
