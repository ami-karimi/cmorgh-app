<div>
    <!-- پیام موفقیت یا خطا -->
    @if (session()->has('success'))
        <div class="max-w-xl mx-auto mb-8 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl text-center">
            {{ session('success') }}
        </div>
    @endif

<!-- دکمه‌های سوئیچ (تب‌بندی) -->
    <div class="flex justify-center mb-12">
        <div class="bg-zinc-900/80 p-1.5 rounded-full border border-zinc-800 inline-flex gap-2">
            <button
                wire:click="setTab('volume')"
                class="px-6 py-2.5 rounded-full text-sm font-bold transition-all {{ $activeTab === 'volume' ? 'bg-gradient-to-r from-orange-600 to-red-600 text-white shadow-lg shadow-orange-900/30' : 'text-zinc-400 hover:text-white' }}">
                ⚡ اشتراک‌های حجمی (L2TP / OpenVPN)
            </button>
            <button
                wire:click="setTab('unlimited')"
                class="px-6 py-2.5 rounded-full text-sm font-bold transition-all {{ $activeTab === 'unlimited' ? 'bg-gradient-to-r from-orange-600 to-red-600 text-white shadow-lg shadow-orange-900/30' : 'text-zinc-400 hover:text-white' }}">
                🚀 اشتراک‌های نامحدود (WireGuard)
            </button>
        </div>
    </div>

    <!-- لیست کارت‌های تعرفه -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($packages as $package)
            <div class="bg-zinc-900/70 rounded-3xl p-8 border border-zinc-800/80 hover:border-orange-500/50 transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">

                <div class="absolute top-4 left-4 bg-zinc-800/80 border border-zinc-700 text-zinc-300 text-xs px-3 py-1 rounded-full font-sans">
                    {{ strtoupper($package['protocol'] ?? 'MIX') }}
                </div>

                <div>
                    <h3 class="text-xl font-black text-white mb-2">{{ $package['name'] }}</h3>
                    <p class="text-xs text-zinc-400 mb-6">
                        @if($package['type'] === 'volume')
                            سازگار با کانکشن هوشمند اندروید و تمامی دیوایس‌ها
                        @else
                            نهایت سرعت با پروتکل مدرن وایرگارد
                        @endif
                    </p>

                    <div class="my-6">
                        <span class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-l from-orange-400 to-red-500">{{ number_format($package['price']) }}</span>
                        <span class="text-zinc-500 text-sm">تومان</span>
                    </div>

                    <ul class="space-y-3 text-sm text-zinc-300 mb-8">
                        <li class="flex items-center gap-2">
                            <span class="text-orange-500">✓</span> اعتبار زمانی: {{ $package['duration'] }} روز
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-orange-500">✓</span> ترافیک:
                            <strong class="text-white">{{ !empty($package['traffic_gb']) ? $package['traffic_gb'] . ' گیگابایت' : 'نامحدود بدون قطعی' }}</strong>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-orange-500">✓</span> تحویل آنی و پشتیبانی ۲۴/۷
                        </li>
                    </ul>
                </div>

                <button
                    wire:click="openBuyModal({{ $package['id'] }})"
                    class="w-full py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-zinc-800 hover:bg-gradient-to-r hover:from-orange-600 hover:to-red-600 transition duration-300 border border-zinc-700 hover:border-transparent shadow-lg">
                    انتخاب و خرید این پلن
                </button>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-zinc-900/30 rounded-3xl border border-dashed border-zinc-800">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center text-2xl mb-4 opacity-50">
                    📦
                </div>
                <h3 class="text-lg font-bold text-zinc-300 mb-2">پکیجی یافت نشد</h3>
                <p class="text-sm text-zinc-500">در حال حاضر اشتراکی در این دسته‌بندی برای فروش وجود ندارد.</p>
            </div>
        @endforelse
    </div>

    <!-- مودال ساخت نام کاربری دلخواه (فقط برای L2TP/OpenVPN) -->
    @if($showModal && $selectedPackage)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fadeIn">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl max-w-md w-full p-8 relative shadow-[0_0_50px_rgba(249,115,22,0.15)]">

                <!-- دکمه بستن -->
                <button wire:click="$set('showModal', false)" class="absolute top-6 left-6 text-zinc-500 hover:text-white">✕</button>

                <h3 class="text-xl font-black text-white mb-2">ساخت اکانت اختصاصی</h3>
                <p class="text-xs text-zinc-400 mb-6">شما در حال خرید پلن <strong class="text-orange-400">{{ $selectedPackage->name }}</strong> هستید. نام کاربری و رمز عبور دلخواه خود را بسازید:</p>

                <form wire:submit="confirmAndPay" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-300 mb-1">نام کاربری دلخواه (انگلیسی)</label>
                        <input wire:model="customUsername" type="text" dir="ltr" placeholder="example_user" class="w-full px-4 py-3 bg-zinc-950 border border-zinc-700 rounded-xl text-white text-left focus:ring-2 focus:ring-orange-500 focus:border-transparent transition text-sm font-mono">
                        @error('customUsername') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-300 mb-1">رمز عبور اتصال</label>
                        <input wire:model="customPassword" type="text" dir="ltr" placeholder="••••••••" class="w-full px-4 py-3 bg-zinc-950 border border-zinc-700 rounded-xl text-white text-left focus:ring-2 focus:ring-orange-500 focus:border-transparent transition text-sm font-mono">
                        @error('customPassword') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-zinc-950/60 p-3 rounded-xl border border-zinc-800/80 text-xs text-zinc-400 mt-4">
                        💡 این اطلاعات جهت ورود به کانکشن هوشمند اندروید و کانفیگ‌های L2TP استفاده خواهد شد.
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold rounded-xl text-sm transition shadow-lg">
                            <span wire:loading.remove wire:target="confirmAndPay">تایید و پرداخت آنلاین</span>
                            <span wire:loading wire:target="confirmAndPay">در حال بررسی...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
