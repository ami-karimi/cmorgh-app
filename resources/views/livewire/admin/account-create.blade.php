<div class="max-w-4xl mx-auto py-8">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                صدور اکانت جدید (Wizard)
            </h1>
            <p class="text-xs text-zinc-500 mt-1">ساخت اکانت VPN و پروفایل کاربری به صورت همزمان</p>
        </div>
        <a href="{{ route('admin.accounts.list') }}" wire:navigate class="px-5 py-2.5 bg-zinc-900 border border-zinc-800 hover:bg-zinc-800 text-zinc-300 font-bold text-xs rounded-xl transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            بازگشت به لیست
        </a>
    </div>

    @if (session()->has('error'))
        <div class="p-4 mb-6 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl font-bold">{{ session('error') }}</div>
    @endif

    <div class="bg-zinc-950 border border-zinc-800 rounded-3xl shadow-2xl overflow-hidden">

        <div class="px-8 py-8 border-b border-zinc-800/80 bg-zinc-900/30">
            <div class="flex items-center justify-between relative">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-zinc-800 rounded-full z-0"></div>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-emerald-500 rounded-full z-0 transition-all duration-500" style="width: {{ (($currentStep - 1) / 3) * 100 }}%"></div>
                @foreach(['اطلاعات هویتی', 'سرویس و تعرفه', 'پیکربندی سرور', 'تایید و صدور'] as $index => $label)
                    <div class="relative z-10 flex flex-col items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-black transition-all duration-300 shadow-lg {{ $currentStep > ($index + 1) ? 'bg-emerald-500 text-white border-2 border-emerald-400' : ($currentStep == ($index + 1) ? 'bg-zinc-950 text-emerald-400 border-2 border-emerald-500 ring-4 ring-emerald-500/20' : 'bg-zinc-900 text-zinc-600 border-2 border-zinc-800') }}">
                            @if($currentStep > ($index + 1))
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <span class="text-xs font-bold absolute -bottom-7 whitespace-nowrap {{ $currentStep >= ($index + 1) ? 'text-zinc-200' : 'text-zinc-600' }}">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-8 min-h-[400px]">

            @if($currentStep == 1)
                <div class="space-y-6 animate-fade-in max-w-2xl mx-auto">
                    <div class="flex justify-between items-center mb-6 border-b border-zinc-800 pb-4">
                        <h3 class="text-base font-black text-white">۱. هویت کاربر و اطلاعات لاگین</h3>
                        <button type="button" wire:click="autoGenerateUsername" class="text-xs px-4 py-2 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl font-bold transition-all flex items-center gap-1 border border-emerald-500/20">
                            ⚡ تولید یوزر و پسورد
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-2">نام کاربری (الزامی)</label>
                            <div class="relative">
                                <input wire:model.live.debounce.500ms="username" type="text" dir="ltr" class="w-full bg-zinc-900 border {{ $errors->has('username') ? 'border-red-500' : 'border-zinc-800' }} rounded-xl text-white p-4 text-sm font-mono focus:ring-1 focus:ring-emerald-500">
                                <div wire:loading wire:target="username" class="absolute left-4 top-4">
                                    <div class="w-4 h-4 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                                </div>
                            </div>
                            @error('username') <span class="text-red-500 text-[10px] mt-1.5 block font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-2">کلمه عبور (الزامی - حداقل ۶ کاراکتر)</label>
                            <input wire:model.live.debounce.500ms="password" type="text" dir="ltr" class="w-full bg-zinc-900 border {{ $errors->has('password') ? 'border-red-500' : 'border-zinc-800' }} rounded-xl text-white p-4 text-sm font-mono focus:ring-1 focus:ring-emerald-500">
                            @error('password') <span class="text-red-500 text-[10px] mt-1.5 block font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-2">نام کامل مشتری (اختیاری)</label>
                            <input wire:model="name" type="text" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl text-white p-4 text-sm focus:ring-1 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-2">شماره تماس (اختیاری)</label>
                            <input wire:model="phonenumber" type="text" dir="ltr" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl text-white p-4 text-sm font-mono focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep == 2)
                <div class="space-y-8 animate-fade-in max-w-3xl mx-auto">
                    <div>
                        <label class="block text-sm font-black text-white mb-4">۱. انتخاب پروتکل ارتباطی:</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach(['l2tp_cisco' => ['name' => 'L2TP / Cisco', 'icon' => '🔒'], 'wireguard' => ['name' => 'WireGuard', 'icon' => '🟣'], 'v2ray' => ['name' => 'V2Ray / Xray', 'icon' => '⚡']] as $key => $data)
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="service_group" value="{{ $key }}" class="sr-only peer">
                                    <div class="p-6 text-center rounded-2xl border-2 bg-zinc-900/50 text-zinc-400 border-zinc-800 hover:border-zinc-600 peer-checked:border-emerald-500 peer-checked:text-emerald-400 peer-checked:bg-emerald-500/10 transition-all">
                                        <div class="text-3xl mb-2">{{ $data['icon'] }}</div>
                                        <div class="text-xs font-bold">{{ $data['name'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-zinc-900/30 p-6 rounded-2xl border border-zinc-800/50">
                            <label class="block text-xs font-bold text-zinc-400 mb-3">۲. تعیین گروه و تعرفه اکانت:</label>
                            <select wire:model="group_id" class="w-full bg-zinc-950 border border-zinc-700 rounded-xl text-white p-4 text-xs focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                                <option value="">انتخاب گروه کاربری...</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }} ({{ number_format($group->price) }} تومان)</option>
                                @endforeach
                            </select>
                            @error('group_id') <span class="text-red-500 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-zinc-900/30 p-6 rounded-2xl border border-zinc-800/50">
                            <label class="block text-xs font-bold text-zinc-400 mb-3">۳. نماینده مالک حساب (Creator):</label>

                            <select wire:model="creator"  class="w-full bg-zinc-950 border border-zinc-700 rounded-xl text-white p-4 text-xs focus:ring-1 focus:ring-emerald-500 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                @foreach($creators as $crt)
                                    <option value="{{ $crt->id }}">{{ $crt->name }} ({{ $crt->role == 'admin' ? 'مدیر سیستم' : 'نماینده' }})</option>
                                @endforeach
                            </select>
                            @error('creator') <span class="text-red-500 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
                            <span class="text-[9px] text-zinc-500 mt-2 block">حساب ایجادشده تحت امتیاز مالی این نماینده ثبت خواهد شد.</span>
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep == 3)
                <div class="space-y-6 animate-fade-in max-w-2xl mx-auto">
                    <h3 class="text-base font-black text-white border-b border-zinc-800 pb-4 mb-6">۳. پیکربندی سرور ({{ strtoupper(str_replace('_', ' ', $service_group)) }})</h3>

                    @if($service_group === 'wireguard')
                        <div class="bg-purple-500/5 border border-purple-500/20 p-6 rounded-2xl">
                            <label class="block text-xs font-bold text-purple-400 mb-3">انتخاب سرور میکروتیک وایرگارد</label>
                            <select wire:model="wg_server_id" class="w-full bg-zinc-950 border border-purple-500/30 rounded-xl text-white p-4 text-sm focus:ring-1 focus:ring-purple-500 cursor-pointer">
                                <option value="">سرور هدف را مشخص کنید...</option>
                                @foreach($allWgServers as $srv)
                                    <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->ipaddress }})</option>
                                @endforeach
                            </select>
                            @error('wg_server_id') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    @elseif($service_group === 'v2ray')
                        <div class="bg-blue-500/5 border border-blue-500/20 p-6 rounded-2xl">
                            <label class="block text-xs font-bold text-blue-400 mb-3">پروتکل داخلی V2Ray</label>
                            <select wire:model="protocol_v2ray" class="w-full bg-zinc-950 border border-blue-500/30 rounded-xl text-white p-4 text-sm focus:ring-1 focus:ring-blue-500 cursor-pointer">
                                <option value="vmess">VMess</option>
                                <option value="vless">VLess / XTLS</option>
                                <option value="trojan">Trojan</option>
                            </select>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center p-12 bg-zinc-900/30 border border-zinc-800/50 rounded-2xl text-center">
                            <span class="text-5xl mb-4">✅</span>
                            <h4 class="text-base font-bold text-emerald-400 mb-2">پیکربندی هوشمند رادیوس</h4>
                            <p class="text-sm text-zinc-500">اکانت‌های L2TP نیازی به انتخاب سرور در این مرحله ندارند. اطلاعات آماده ثبت است.</p>
                        </div>
                    @endif
                </div>
            @endif

            @if($currentStep == 4)
                <div class="space-y-6 animate-fade-in max-w-2xl mx-auto">
                    <h3 class="text-base font-black text-white border-b border-zinc-800 pb-4 mb-6">۴. بررسی نهایی مشخصات اکانت</h3>

                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl p-6 divide-y divide-zinc-800/80">
                        <div class="py-4 flex justify-between items-center">
                            <span class="text-sm font-bold text-zinc-400">نام کاربری:</span>
                            <span class="text-base font-black text-emerald-400 font-mono tracking-wider" dir="ltr">{{ $username }}</span>
                        </div>
                        <div class="py-4 flex justify-between items-center">
                            <span class="text-sm font-bold text-zinc-400">کلمه عبور:</span>
                            <span class="text-base font-black text-white font-mono tracking-wider" dir="ltr">{{ $password }}</span>
                        </div>
                        <div class="py-4 flex justify-between items-center">
                            <span class="text-sm font-bold text-zinc-400">پروتکل پایه:</span>
                            <span class="text-sm font-black text-white uppercase">{{ str_replace('_', ' ', $service_group) }}</span>
                        </div>
                        <div class="py-4 flex justify-between items-center">
                            <span class="text-sm font-bold text-zinc-400">ایمیل اتوماتیک پنل کاربری:</span>
                            <span class="text-sm font-bold text-blue-400 font-mono" dir="ltr">{{ $username }}@cmorgh.com</span>
                        </div>
                        <div class="py-4 flex justify-between items-center">
                            <span class="text-sm font-bold text-zinc-400">نماینده ثبت‌کننده مالک:</span>
                            <span class="text-sm font-black text-orange-400">
                                @php $crtUser = \App\Models\User::find($creator); @endphp
                                {{ $crtUser->name ?? 'سیستم' }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-orange-500/10 border border-orange-500/20 rounded-2xl p-5 text-sm font-bold text-orange-400 leading-relaxed flex gap-3 items-start">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        با کلیک روی صدور نهایی، کاربر لاگین پنل ساخته شده، اکانت در دیتابیس ثبت و کانفیگ‌های لازم روی سرور اعمال می‌گردد.
                    </div>
                </div>
            @endif
        </div>

        <div class="px-8 py-5 border-t border-zinc-800 bg-zinc-900/50 flex items-center justify-between">
            <div>
                @if($currentStep > 1)
                    <button wire:click="prevStep" class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-sm rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        مرحله قبل
                    </button>
                @endif
            </div>

            <div>
                @if($currentStep < 4)
                    <button wire:click="nextStep" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all flex items-center gap-2">
                        مرحله بعد
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                @else
                    <button wire:click="save" class="px-10 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 text-white font-black text-base rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        صدور نهایی اکانت
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
