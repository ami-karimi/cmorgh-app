<div class="space-y-6 pb-12">
    <div>
        <h1 class="text-2xl font-black text-white tracking-wide">صدور سرویس جدید</h1>
        <p class="text-xs text-zinc-500 mt-1 font-medium">ایجاد اکانت تکی برای مشتریان یا ساخت گروهی (Bulk) برای فروش</p>
    </div>

    @if (session()->has('success_msg'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl font-bold text-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success_msg') }}
        </div>
    @endif

    @error('balance')
    <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl font-bold text-sm flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        {{ $message }}
    </div>
    @enderror

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-zinc-900/40 p-2 rounded-2xl border border-zinc-800/80 flex gap-2">
                <button wire:click="setCreationType('single')" class="flex-1 py-3 text-sm font-bold rounded-xl transition-all flex justify-center items-center gap-2 {{ $creationType === 'single' ? 'bg-orange-500 text-white shadow-lg' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    اکانت تکی (سفارشی)
                </button>
                <button wire:click="setCreationType('bulk')" class="flex-1 py-3 text-sm font-bold rounded-xl transition-all flex justify-center items-center gap-2 {{ $creationType === 'bulk' ? 'bg-orange-500 text-white shadow-lg' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    صدور گروهی (Bulk)
                </button>
            </div>

            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-6 shadow-xl relative overflow-hidden">
                <form wire:submit.prevent="createAccounts" class="space-y-8 relative z-10">

                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-zinc-400">انتخاب پروتکل اتصال <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <button type="button" wire:click="setServiceGroup('wireguard')" class="flex flex-col items-center justify-center p-4 rounded-2xl border transition-all duration-200 {{ $service_group === 'wireguard' ? 'bg-purple-500/10 border-purple-500 text-purple-400 shadow-[0_0_15px_rgba(168,85,247,0.15)]' : 'bg-zinc-950 border-zinc-800 text-zinc-500 hover:border-zinc-700 hover:text-zinc-300' }}">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <span class="text-xs font-bold uppercase tracking-wider">WireGuard</span>
                            </button>
                            <button type="button" wire:click="setServiceGroup('l2tp_cisco')" class="flex flex-col items-center justify-center p-4 rounded-2xl border transition-all duration-200 {{ $service_group === 'l2tp_cisco' ? 'bg-blue-500/10 border-blue-500 text-blue-400 shadow-[0_0_15px_rgba(59,130,246,0.15)]' : 'bg-zinc-950 border-zinc-800 text-zinc-500 hover:border-zinc-700 hover:text-zinc-300' }}">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                <span class="text-xs font-bold uppercase tracking-wider">L2TP / Open</span>
                            </button>
                        </div>
                    </div>

                    @if($service_group === 'wireguard')
                        <div class="p-4 mt-4 bg-purple-500/5 border border-purple-500/20 rounded-2xl animate-fade-in space-y-3">
                            <div class="flex items-center justify-between text-purple-400 mb-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                                    <span class="text-xs font-bold">انتخاب سرور مقصد (Node Selection)</span>
                                </div>
                                <span class="text-[10px] bg-purple-500/10 px-2 py-0.5 rounded-full text-purple-300 font-mono">WireGuard Nodes</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                <div wire:click="$set('selectedWgServer', 'auto')" class="cursor-pointer p-3.5 rounded-xl border transition-all flex flex-col justify-between {{ $selectedWgServer === 'auto' ? 'bg-purple-500/20 border-purple-500 text-white shadow-sm' : 'bg-zinc-950 border-zinc-800 text-zinc-400 hover:border-zinc-700' }}">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-purple-500/20 flex items-center justify-center text-purple-400 text-xs font-bold">🤖</div>
                                            <div>
                                                <span class="block text-xs font-bold">انتخاب هوشمند (Auto)</span>
                                                <span class="text-[9px] text-zinc-500">توزیع بار روی خلوت‌ترین سرور</span>
                                            </div>
                                        </div>
                                        @if($selectedWgServer === 'auto')
                                            <div class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></div>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-purple-300/80 bg-purple-500/10 px-2 py-1 rounded-lg text-center font-medium">سیستم به صورت خودکار بهترین سرور را انتخاب می‌کند</span>
                                </div>

                                @foreach($wgServers as $server)
                                    @php
                                        // رنگ‌بندی پروگرس‌بار بر اساس میزان پر بودن سرور
                                        $barColor = $server->usage_percent > 85 ? 'bg-red-500' : ($server->usage_percent > 60 ? 'bg-amber-500' : 'bg-emerald-500');
                                    @endphp
                                    <div wire:click="$set('selectedWgServer', {{ $server->id }})" class="cursor-pointer p-3.5 rounded-xl border transition-all flex flex-col justify-between {{ $selectedWgServer == $server->id ? 'bg-purple-500/20 border-purple-500 text-white shadow-sm' : 'bg-zinc-950 border-zinc-800 text-zinc-400 hover:border-zinc-700' }}">

                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2.5 truncate">
                                                <div class="w-7 h-7 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-300 text-xs shrink-0 font-mono">🌍</div>
                                                <div class="truncate">
                                                    <span class="block text-xs font-bold truncate text-zinc-200">{{ $server->name }}</span>
                                                    <span class="text-[9px] text-zinc-500 font-mono" dir="ltr">{{ $server->ipaddress }}</span>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-zinc-900 text-zinc-300 font-mono">
                                                ظرفیت: {{ $server->max_capacity }}
                                            </span>
                                        </div>

                                        <div class="space-y-1.5 my-1">
                                            <div class="flex justify-between items-center text-[10px]">
                                                <span class="text-zinc-400">فعال: <strong class="text-white font-mono">{{ $server->active_users_count }}</strong></span>
                                                <span class="text-zinc-400">باقیمانده: <strong class="text-emerald-400 font-mono">{{ $server->remaining_capacity }}</strong></span>
                                            </div>
                                            <div class="w-full bg-zinc-900 rounded-full h-1.5 overflow-hidden border border-zinc-800">
                                                <div class="{{ $barColor }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $server->usage_percent }}%"></div>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <label class="text-xs font-bold text-zinc-400">گروه‌ها و تعرفه‌های مجاز <span class="text-red-500">*</span></label>
                            <div class="relative w-full sm:w-64">
                                <svg class="w-4 h-4 absolute right-3 top-2.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input wire:model.live.debounce.300ms="searchPlan" type="text" placeholder="جستجوی نام تعرفه..." class="w-full bg-zinc-950 border border-zinc-800 text-white text-xs rounded-xl py-2.5 pr-9 pl-3 focus:ring-1 focus:ring-orange-500 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-64 overflow-y-auto pr-1 pb-1" style="scrollbar-width: thin; scrollbar-color: #3f3f46 transparent;">
                            @forelse($availableGroups as $group)
                                @php
                                    $basePrice = $group->price_reseler ?? $group->price ?? 0;
                                @endphp
                                <div wire:click="$set('group_id', {{ $group->id }})" class="cursor-pointer flex flex-col p-4 rounded-2xl border transition-all duration-200 {{ $group_id == $group->id ? 'bg-orange-500/10 border-orange-500 ring-1 ring-orange-500' : 'bg-zinc-950 border-zinc-800 hover:border-zinc-700' }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-sm font-bold {{ $group_id == $group->id ? 'text-orange-400' : 'text-zinc-200' }}">{{ $group->name }}</span>
                                        @if($group_id == $group->id)
                                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    </div>
                                    <div class="mt-auto pt-2 flex justify-between items-end">
                                        <div>
                                            <span class="block text-[10px] text-zinc-500">قیمت برای شما:</span>
                                            <span class="text-base font-black text-emerald-400 font-mono">{{ number_format($group->getFinalPriceFor(auth()->user())) }} <span class="text-[9px] font-sans font-normal text-zinc-500">تومان</span></span>
                                        </div>
                                        @if($discountPercent > 0)
                                            <span class="text-[10px] text-zinc-500 line-through font-mono">{{ number_format($basePrice) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-8 text-center bg-zinc-950 rounded-2xl border border-zinc-800/50">
                                    <p class="text-xs text-zinc-500 font-bold">برای این پروتکل گروهی یافت نشد.</p>
                                </div>
                            @endforelse
                        </div>
                        @error('group_id') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if($creationType === 'single')
                        <div class="p-5 bg-zinc-950/50 rounded-2xl border border-zinc-800 space-y-4 animate-fade-in" wire:key="single-form">

                            <div>
                                <label class="block text-[11px] font-bold text-zinc-500 mb-1.5 flex justify-between">
                                    <span>تخصیص اکانت به مشتری</span>
                                </label>
                                <div class="relative"
                                     x-data="{
                 open: false,
                 search: '',
                 selectedName: '➕ ثبت مشتری جدید (پیش‌فرض)',
                 selectedVal: 'new'
             }"
                                     @click.away="open = false">


                                    <div @click="open = !open"
                                         class="w-full bg-zinc-900 border border-zinc-700 text-white text-xs rounded-xl p-3 cursor-pointer flex items-center justify-between focus:ring-1 focus:ring-orange-500 transition">
                                        <span x-text="selectedName"></span>
                                        <svg class="w-4 h-4 text-zinc-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>

                                    <div x-show="open"
                                         x-transition.origin.top.duration.200ms
                                         class="absolute z-50 w-full mt-2 bg-zinc-900 border border-zinc-700 rounded-xl shadow-2xl overflow-hidden p-2 space-y-2"
                                         style="display: none;">

                                        <div class="relative">
                                            <input type="text"
                                                   x-model="search"
                                                   placeholder="جستجوی نام یا شماره تماس..."
                                                   class="w-full bg-zinc-950 border border-zinc-800 text-white text-xs rounded-lg p-2.5 pr-8 focus:outline-none focus:border-orange-500 font-sans"
                                                   @click.stop>
                                            <svg class="w-4 h-4 text-zinc-500 absolute right-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>

                                        <div class="max-h-48 overflow-y-auto space-y-1 custom-scrollbar">

                                            <div @click="selectedName = '➕ ثبت مشتری جدید (پیش‌فرض)'; selectedVal = 'new'; $wire.set('customer_id', 'new'); open = false; search = ''"
                                                 x-show="search === '' || 'ثبت مشتری جدید'.includes(search.toLowerCase())"
                                                 class="p-2 rounded-lg text-xs cursor-pointer hover:bg-orange-500/10 hover:text-orange-400 text-zinc-200 transition flex items-center justify-between">
                                                <span>➕ ثبت مشتری جدید (پیش‌فرض)</span>
                                            </div>

                                            <div @click="selectedName = 'ثبت به نام خودم (آرشیو اکانت‌های بدون مشتری)'; selectedVal = 'me'; $wire.set('customer_id', 'me'); open = false; search = ''"
                                                 x-show="search === '' || 'ثبت به نام خودم آرشیو اکانت‌های بدون مشتری'.toLowerCase().includes(search.toLowerCase())"
                                                 class="p-2 rounded-lg text-xs cursor-pointer hover:bg-orange-500/10 hover:text-orange-400 text-zinc-200 transition flex items-center justify-between">
                                                <span>🗂️ ثبت به نام خودم (آرشیو اکانت‌های بدون مشتری)</span>
                                            </div>

                                            <div class="border-t border-zinc-800 my-1 pt-1 px-1 text-[10px] text-zinc-500 font-bold">مشتریان قبلی شما</div>

                                            @foreach($customers as $customer)
                                                @php
                                                    $label = $customer->name . ' (' . ($customer->phone ?? 'بدون شماره') . ')';
                                                @endphp
                                                <div @click="selectedName = '{{ $label }}'; selectedVal = '{{ $customer->id }}'; $wire.set('customer_id', '{{ $customer->id }}'); open = false; search = ''"
                                                     x-show="search === '' || '{{ strtolower($label) }}'.includes(search.toLowerCase())"
                                                     class="p-2 rounded-lg text-xs cursor-pointer hover:bg-zinc-800 text-zinc-300 transition flex items-center justify-between">
                                                    <span>{{ $label }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <input type="hidden" wire:model="customer_id" x-model="selectedVal">
                                </div>
                            </div>

                            @if($customer_id === 'new')
                                <div class="p-4 bg-orange-500/5 border border-orange-500/20 rounded-xl space-y-4 mb-4">
                                    <h4 class="text-[11px] font-black text-orange-400 mb-2">مشخصات مشتری جدید</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-bold text-zinc-400 mb-1">نام و نام خانوادگی <span class="text-red-500">*</span></label>
                                            <input wire:model="newCustomerName" type="text" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-orange-500">
                                            @error('newCustomerName') <span class="text-red-500 text-[9px] mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-zinc-400 mb-1">شماره تماس (اختیاری)</label>
                                            <input wire:model="newCustomerPhone" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-lg text-xs p-2.5 font-mono focus:ring-1 focus:ring-orange-500">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[10px] font-bold text-zinc-400 mb-1">آدرس ایمیل (اختیاری)</label>
                                            <div class="flex gap-2">
                                                <input wire:model="newCustomerEmail" type="email" dir="ltr" class="flex-1 bg-zinc-950 border border-zinc-700 text-white rounded-lg text-xs p-2.5 font-mono focus:ring-1 focus:ring-orange-500">
                                                <button type="button" wire:click="generateRandomEmail" class="px-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded-lg text-[10px] font-bold transition whitespace-nowrap">تولید ایمیل رندوم 🎲</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($service_group !== 'wireguard')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-zinc-800/80">
                                    <div>
                                        <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">نام کاربری اکانت (Username)</label>
                                        <input wire:model="username" type="text" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-xl text-sm p-3 font-mono focus:ring-1 focus:ring-orange-500">
                                        @error('username') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">کلمه عبور (Password)</label>
                                        <input wire:model="password" type="text" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-xl text-sm p-3 font-mono focus:ring-1 focus:ring-orange-500">
                                        @error('password') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <button type="button" wire:click="generateRandomCredentials" class="text-[10px] text-zinc-400 hover:text-white bg-zinc-800 hover:bg-zinc-700 px-3 py-1.5 rounded-lg transition mt-3">تولید یوزر و پسورد تصادفی 🎲</button>
                            @else
                                <div class="p-4 bg-purple-500/10 border border-purple-500/20 rounded-xl mt-4">
                                    <p class="text-[11px] text-purple-400 font-bold flex items-center gap-2">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        در پروتکل WireGuard، اتصال از طریق کلیدها (Keys) برقرار می‌شود. نام کاربری و مشخصات سرور به صورت خودکار توسط سیستم در پس‌زمینه ایجاد خواهد شد.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($creationType === 'bulk')
                        <div class="p-5 bg-zinc-950/50 rounded-2xl border border-zinc-800 space-y-4 animate-fade-in" wire:key="bulk-form">
                            <div class="bg-orange-500/10 border border-orange-500/20 p-4 rounded-xl mb-4">
                                <p class="text-[11px] text-orange-400 font-bold leading-relaxed text-justify">
                                    در ساخت گروهی، سیستم به صورت خودکار به تعداد مشخص‌شده، نام کاربری و کلمه عبور تصادفی تولید می‌کند. این اکانت‌ها مستقیماً در لیست اکانت‌های شخصی شما قرار می‌گیرند.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">تعداد اکانت جهت صدور</label>
                                    <input wire:model.live="bulkCount" type="number" min="2" max="100" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-xl text-lg p-3 font-mono focus:ring-1 focus:ring-orange-500">
                                    @error('bulkCount') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">پیشوند نام کاربری (اختیاری)</label>
                                    <input wire:model="prefix" type="text" placeholder="مثال: vip_" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-xl text-sm p-3 font-mono focus:ring-1 focus:ring-orange-500">
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="pt-2 text-left">
                        <button type="submit" class="px-8 py-3 w-full md:w-auto bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 text-white font-bold text-sm rounded-xl shadow-[0_5px_15px_-5px_rgba(249,115,22,0.4)] transition-all">
                            تایید، صدور و کسر هزینه
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-6 shadow-xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-emerald-400">💳</div>
                    <span class="text-sm font-bold text-zinc-300">موجودی کیف پول شما</span>
                </div>
                <div class="text-3xl font-black text-white font-mono" dir="ltr">
                    {{ number_format($balance) }} <span class="text-xs text-zinc-500 font-sans">تومان</span>
                </div>
            </div>

            <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
                <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    فاکتور نهایی صدور
                </h3>

                @php
                    $group = collect($availableGroups)->firstWhere('id', $group_id);
                    $basePrice = $group ? ($group->price_reseler ?? $group->price ?? 0) : 0;
                    $agentCost = $discountPercent > 0 ? $basePrice - ($basePrice * $discountPercent / 100) : $basePrice;

                    $totalAccountsToCreate = $creationType === 'bulk' ? (int) $bulkCount : 1;
                    $totalCost = $agentCost * $totalAccountsToCreate;
                    $isSufficient = $balance >= $totalCost;
                @endphp

                <div class="space-y-3 text-xs mb-6 border-b border-zinc-800/80 pb-6">
                    <div class="flex justify-between text-zinc-400">
                        <span>نوع پروتکل:</span>
                        <span class="font-bold text-white uppercase">{{ $service_group }}</span>
                    </div>
                    <div class="flex justify-between text-zinc-400">
                        <span>هزینه پایه اکانت:</span>
                        <span class="font-mono">{{ number_format($basePrice) }} ت</span>
                    </div>
                    @if($discountPercent > 0 && $group)
                        <div class="flex justify-between text-emerald-400 font-bold">
                            <span>سود تخفیف نماینده ({{ $discountPercent }}%):</span>
                            <span class="font-mono">- {{ number_format($basePrice - $agentCost) }} ت</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-zinc-300 font-bold border-t border-zinc-800 pt-3">
                        <span>هزینه نهایی هر اکانت:</span>
                        <span class="font-mono">{{ number_format($agentCost) }} ت</span>
                    </div>
                    <div class="flex justify-between text-zinc-400">
                        <span>تعداد درخواستی:</span>
                        <span class="font-mono font-bold text-white">x {{ $totalAccountsToCreate }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-end mb-4">
                    <span class="text-sm font-bold text-white">جمع کل پرداخت:</span>
                    <span class="text-2xl font-black {{ $isSufficient ? 'text-orange-400' : 'text-red-500' }} font-mono">{{ number_format($totalCost) }}</span>
                </div>

                @if(!$group)
                    <div class="p-3 bg-zinc-900 text-zinc-500 border border-zinc-800 text-[10px] font-bold rounded-xl text-center">
                        لطفاً یک تعرفه انتخاب کنید.
                    </div>
                @elseif(!$isSufficient)
                    <div class="p-3 bg-red-500/10 text-red-400 border border-red-500/20 text-[10px] font-bold rounded-xl text-center">
                        موجودی ناکافی است. ابتدا حساب را شارژ کنید.
                    </div>
                @else
                    <div class="p-3 bg-emerald-500/5 text-emerald-500 border border-emerald-500/10 text-[10px] font-bold rounded-xl text-center flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        موجودی کافی است. قابل صدور.
                    </div>
                @endif
            </div>

        </div>
    </div>


    @if($isSuccessModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in" wire:key="success-creation-modal">
            <div class="relative w-full max-w-lg bg-zinc-900 border border-zinc-700/60 rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                <div class="flex items-center justify-between px-7 py-5 border-b border-zinc-800/80 bg-zinc-900/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white">صدور سرویس با موفقیت انجام شد</h2>
                            <p class="text-[10px] text-zinc-400 mt-0.5">مشخصات سرویس(های) صادر شده به شرح زیر است</p>
                        </div>
                    </div>
                    <button wire:click="resetFormAndCloseModal" class="p-2 text-zinc-400 hover:text-white bg-zinc-800/60 hover:bg-zinc-700 rounded-full transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-7 overflow-y-auto space-y-4 max-h-[60vh]">
                    @foreach($createdAccountsList as $acc)
                        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4 space-y-3 relative overflow-hidden">
                            <div class="flex justify-between items-center border-b border-zinc-800/80 pb-2">
                                <span class="text-xs font-bold text-orange-400 font-mono">{{ $acc['group_name'] }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-zinc-900 text-zinc-400 font-mono uppercase border border-zinc-800">{{ $acc['service_group'] }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs font-mono" dir="ltr">
                                <div class="bg-zinc-900/80 p-2.5 rounded-xl border border-zinc-800">
                                    <span class="text-[9px] text-zinc-500 font-sans block mb-0.5 text-right">نام کاربری:</span>
                                    <span class="text-white font-bold select-all">{{ $acc['username'] }}</span>
                                </div>
                                <div class="bg-zinc-900/80 p-2.5 rounded-xl border border-zinc-800">
                                    <span class="text-[9px] text-zinc-500 font-sans block mb-0.5 text-right">کلمه عبور:</span>
                                    <span class="text-emerald-400 font-bold select-all">{{ $acc['password'] }}</span>
                                </div>
                            </div>

                            @if($acc['id'])
                                <div class="pt-1 text-left">
                                    <a href="{{ route('reseller.accounts.show', $acc['id']) }}" wire:navigate class="inline-flex items-center gap-1.5 text-[11px] font-bold text-orange-400 hover:text-orange-300 transition-colors">
                                        مشاهده و مدیریت کامل این اکانت
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="px-7 py-5 border-t border-zinc-800/80 bg-zinc-900/80 space-y-3">
                    <p class="text-[11px] text-zinc-400 font-bold text-center">اکنون تمایل دارید چه عملیاتی انجام دهید؟</p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button wire:click="resetFormAndCloseModal" class="flex-1 py-3 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 text-white font-bold text-xs rounded-xl shadow-lg transition-all text-center">
                            ➕ ساخت اکانت جدید
                        </button>
                        <a href="{{ route('reseller.accounts.index') }}" wire:navigate class="flex-1 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-bold text-xs rounded-xl border border-zinc-700/50 transition-all text-center">
                            📋 مشاهده لیست اکانت‌ها
                        </a>
                    </div>
                </div>

            </div>
        </div>
    @endif

    @if($isErrorModalOpen)
        <div class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in" wire:key="error-modal">
            <div class="relative w-full max-w-md bg-zinc-900 border border-rose-500/50 rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col transform transition-all">

                <div class="flex items-center justify-between px-7 py-5 border-b border-zinc-800/80 bg-zinc-900/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white">خطا در انجام عملیات</h2>
                            <p class="text-[10px] text-zinc-400 mt-0.5">عملیات متوقف شد</p>
                        </div>
                    </div>
                    <button wire:click="$set('isErrorModalOpen', false)" class="p-2 text-zinc-400 hover:text-white bg-zinc-800/60 hover:bg-zinc-700 rounded-full transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-7 text-center space-y-4">
                    <p class="text-sm font-bold text-zinc-300 leading-loose">
                        {{ $errorMessage }}
                    </p>
                </div>

                <div class="px-7 py-5 border-t border-zinc-800/80 bg-zinc-900/80 flex justify-center">
                    <button wire:click="$set('isErrorModalOpen', false)" class="px-8 py-2.5 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white font-bold text-xs rounded-xl transition-all shadow-lg">
                        متوجه شدم
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
