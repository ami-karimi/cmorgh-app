<div class="max-w-6xl mx-auto py-8 px-4 md:px-0">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                صدور اکانت جدید سرویس
            </h1>
            <p class="text-xs text-zinc-500 mt-1">ایجاد اکانت تکی یا گروهی با قابلیت تخصیص به نمایندگان و مشتریان</p>
        </div>
        <a href="{{ route('admin.accounts.list') }}" wire:navigate class="px-5 py-2.5 bg-zinc-900 border border-zinc-800 hover:bg-zinc-800 text-zinc-300 font-bold text-xs rounded-xl transition-all flex items-center gap-2 shadow-sm w-fit">
            بازگشت به لیست
        </a>
    </div>

    <div class="flex items-center gap-2 mb-6 bg-zinc-900/50 p-1.5 rounded-2xl border border-zinc-800 w-fit">
        <button wire:click="$set('creationType', 'single')" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all {{ $creationType === 'single' ? 'bg-zinc-800 text-white shadow-md' : 'text-zinc-500 hover:text-zinc-300' }}">
            👤 صدور تکی (Single)
        </button>
        <button wire:click="$set('creationType', 'bulk')" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all {{ $creationType === 'bulk' ? 'bg-zinc-800 text-white shadow-md' : 'text-zinc-500 hover:text-zinc-300' }}">
            👥 صدور گروهی (Bulk)
        </button>
    </div>

    @if (session()->has('error'))
        <div class="p-4 mb-6 text-sm text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-2xl font-bold flex items-center justify-between animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    @if (session()->has('success_msg') || session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl font-bold flex items-center justify-between animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('success_msg') ?? session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    @if ($errors->has('balance') || $errors->has('error'))
        <div class="p-4 mb-6 text-sm text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-2xl font-bold flex items-center gap-2 animate-fade-in">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ $errors->first('balance') ?? $errors->first('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 space-y-6">

            <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-6 shadow-xl space-y-5">
                <div class="border-b border-zinc-800/80 pb-3 flex items-center gap-2">
                    <h2 class="text-sm font-black text-white">مالکیت و کسر هزینه</h2>
                </div>

                <div x-data="{
        open: false, search: '',
        options: @js($creators->map(fn($c) => ['id' => $c->id, 'name' => $c->name . ' (' . ($c->role=='manager' || $c->role=='admin'?'مدیر':'نماینده') . ')'])),
        get filteredOptions() { return this.search === '' ? this.options : this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase())); },
        get selectedName() { let sel = this.options.find(i => i.id == $wire.creator); return sel ? sel.name : 'انتخاب نماینده...'; }
    }"
                     @click.outside="open = false"
                     class="relative">

                    <label class="block text-xs font-bold text-zinc-400 mb-2">نماینده مالک اکانت (Creator)</label>
                    <button @click="open = !open" type="button" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl text-white p-3 text-sm flex items-center justify-between outline-none focus:ring-1 focus:ring-emerald-500">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" x-cloak @click.stop class="absolute z-50 w-full mt-1 bg-zinc-800 border border-zinc-700 rounded-xl shadow-2xl overflow-hidden max-h-60 flex flex-col">
                        <div class="p-2 border-b border-zinc-700">
                            <input x-model="search" type="text" placeholder="جستجوی نماینده..." class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-lg px-3 py-2 text-xs outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <ul class="overflow-y-auto p-1 custom-scrollbar">
                            <template x-for="option in filteredOptions" :key="option.id">
                                <li @click="$wire.set('creator', option.id); open = false" class="px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-700 hover:text-white rounded-lg cursor-pointer transition" x-text="option.name"></li>
                            </template>
                        </ul>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" wire:model="payFromAgentWallet" class="peer sr-only">
                            <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                        </div>
                        <span class="text-xs font-bold text-zinc-300 group-hover:text-white transition">کسر هزینه از نماینده فوق</span>
                    </label>

                    @if($creationType === 'single')
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" wire:model="payFromUserWallet" class="peer sr-only">
                                <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                            </div>
                            <span class="text-xs font-bold text-zinc-300 group-hover:text-white transition">کسر هزینه از کیف پول مشتری</span>
                        </label>
                    @endif
                </div>
            </div>

            @if($creationType === 'single')
                <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-6 shadow-xl space-y-5 animate-fade-in">
                    <div class="border-b border-zinc-800/80 pb-3 flex items-center gap-2">
                        <h2 class="text-sm font-black text-white">کاربر نهایی (مشتری)</h2>
                    </div>

                    <div x-data="{
        open: false, search: '',
        staticOptions: [
            {id: 'new', name: '➕ ثبت مشتری جدید (همینجا)', agent: ''},
            {id: 'me', name: '🗂️ ذخیره در آرشیو شخصی خودم', agent: ''}
        ],
        dbOptions: @js($customers->map(fn($c) => [
            'id' => $c->id,
            'name' => ($c->name ?? 'بدون نام') . ' (' . ($c->phone ?? $c->email ?? 'بدون تماس') . ')',
            'agent' => $c->parentAgent->name ?? 'بدون نماینده'
        ])),
        get filteredDbOptions() {
            if (this.search === '') return this.dbOptions;
            let s = this.search.toLowerCase();
            return this.dbOptions.filter(i => i.name.toLowerCase().includes(s) || i.agent.toLowerCase().includes(s));
        },
        get selectedName() {
            let selStatic = this.staticOptions.find(i => i.id == $wire.customer_id);
            if(selStatic) return selStatic.name;
            let selDb = this.dbOptions.find(i => i.id == $wire.customer_id);
            return selDb ? selDb.name + (selDb.agent ? ' — نماینده: ' + selDb.agent : '') : 'انتخاب مشتری...';
        }
    }"
                         @click.outside="open = false"
                         class="relative">

                        <label class="block text-xs font-bold text-zinc-400 mb-2">جستجو یا انتخاب مشتری</label>
                        <button @click="open = !open" type="button" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl text-white p-3 text-sm flex items-center justify-between outline-none focus:ring-1 focus:ring-emerald-500">
                            <span x-text="selectedName" class="truncate text-xs"></span>
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" x-cloak @click.stop class="absolute z-50 w-full mt-1 bg-zinc-800 border border-zinc-700 rounded-xl shadow-2xl overflow-hidden max-h-60 flex flex-col">
                            <div class="p-2 border-b border-zinc-700">
                                <input x-model="search" type="text" placeholder="جستجوی مشتری یا نماینده..." class="w-full bg-zinc-900 border border-zinc-700 text-white rounded-lg px-3 py-2 text-xs outline-none focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <ul class="overflow-y-auto p-1 custom-scrollbar">
                                <template x-for="opt in staticOptions" :key="opt.id">
                                    <li @click="$wire.set('customer_id', opt.id); open = false" class="px-3 py-2 text-xs font-bold text-orange-400 bg-orange-500/5 hover:bg-orange-500/10 mb-1 rounded-lg cursor-pointer transition" x-text="opt.name"></li>
                                </template>
                                <div class="px-3 py-1 text-[10px] text-zinc-500 border-t border-zinc-700 mt-1">مشتریان ثبت شده:</div>
                                <template x-for="option in filteredDbOptions" :key="option.id">
                                    <li @click="$wire.set('customer_id', option.id); open = false" class="px-3 py-2 hover:bg-zinc-700 rounded-lg cursor-pointer transition flex items-center justify-between gap-2">
                                        <span class="text-[11px] font-mono text-zinc-200 truncate" x-text="option.name"></span>
                                        <span class="text-[9px] px-2 py-0.5 rounded bg-orange-500/10 text-orange-400 font-bold shrink-0" x-text="'نماینده: ' + option.agent"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    @if($customer_id === 'new')
                        <div class="space-y-4 pt-2 border-t border-zinc-800/50 animate-fade-in">
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">نام و نام خانوادگی مشتری <span class="text-rose-500">*</span></label>
                                <input wire:model="newCustomerName" type="text" placeholder="مثلاً: علی محمدی" class="w-full bg-zinc-900 border border-zinc-800 rounded-lg text-white p-2.5 text-xs focus:ring-1 focus:ring-emerald-500 outline-none">
                                @error('newCustomerName') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">شماره موبایل (جهت یوزرنیم پنلش)</label>
                                <input wire:model="newCustomerPhone" type="text" dir="ltr" placeholder="0912..." class="w-full bg-zinc-900 border border-zinc-800 rounded-lg text-white p-2.5 text-xs font-mono focus:ring-1 focus:ring-emerald-500 outline-none">
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-6 md:p-8 shadow-xl space-y-6">
                <div>
                    <label class="block text-xs font-bold text-zinc-400 mb-3">پروتکل ارتباطی:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach(['l2tp_cisco' => ['name' => 'L2TP / Cisco', 'icon' => '🔒'], 'wireguard' => ['name' => 'WireGuard', 'icon' => '🟣'], 'v2ray' => ['name' => 'V2Ray / Xray', 'icon' => '⚡']] as $key => $data)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="service_group" value="{{ $key }}" class="sr-only peer">
                                <div class="p-4 text-center rounded-xl border-2 bg-zinc-900/50 text-zinc-500 border-zinc-800 hover:border-zinc-700 peer-checked:border-emerald-500 peer-checked:text-emerald-400 peer-checked:bg-emerald-500/10 transition-all flex flex-col items-center gap-2">
                                    <span class="text-2xl">{{ $data['icon'] }}</span>
                                    <span class="text-[11px] font-bold">{{ $data['name'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-2">گروه / تعرفه اکانت</label>
                        <select wire:model.live="group_id" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl text-white p-3.5 text-sm focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">انتخاب گروه کاربری...</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }} ({{ number_format($group->price_reseler) }} تومان)</option>
                            @endforeach
                        </select>
                        @error('group_id') <span class="text-rose-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    @if($service_group === 'wireguard')
                        <div>
                            <label class="block text-xs font-bold text-purple-400 mb-2">سرور وایرگارد</label>
                            <select wire:model="wg_server_id" class="w-full bg-zinc-900 border border-purple-500/30 rounded-xl text-white p-3.5 text-sm focus:ring-1 focus:ring-purple-500 outline-none">
                                <option value="auto">تخصیص خودکار (خلوت‌ترین)</option>
                                @foreach($allWgServers as $srv)
                                    <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="bg-zinc-900/40 border border-emerald-500/20 rounded-2xl p-5 shadow-inner mt-4 animate-fade-in relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>

                    <h3 class="text-xs font-bold text-emerald-400 mb-4 border-b border-emerald-500/20 pb-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                        خلاصه مالی فاکتور
                    </h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-[10px] text-zinc-500 font-bold mb-1">قیمت پایه فروش مشتری:</p>
                            <p class="text-sm font-mono text-zinc-300">{{ number_format($basePrice) }} <span class="text-[9px] font-sans">تومان</span></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-zinc-500 font-bold mb-1">تخفیف نماینده انتخابی:</p>
                            <p class="text-sm font-mono text-orange-400">%{{ $agentDiscount }}</p>
                        </div>
                        <div class="bg-zinc-950/50 p-2 rounded-xl border border-zinc-800">
                            <p class="text-[10px] text-zinc-400 font-bold mb-1">قیمت فروش به مشتری:</p>
                            <p class="text-sm font-black font-mono text-white">{{ number_format($totalUserPrice) }} <span class="text-[9px] font-sans">تومان</span></p>
                        </div>
                        <div class="bg-emerald-500/10 p-2 rounded-xl border border-emerald-500/30">
                            <p class="text-[10px] text-emerald-500 font-bold mb-1">قیمت برای نماینده</p>
                            <p class="text-sm font-black font-mono text-emerald-400">{{ number_format($totalAgentPrice) }} <span class="text-[9px] font-sans">تومان</span></p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-zinc-800/80 pt-6">
                    @if($creationType === 'single')
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold text-zinc-300">اطلاعات اتصال (VPN)</h3>
                            <button type="button" wire:click="autoGenerateUsername" class="text-[10px] px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded-lg font-bold transition flex items-center gap-1 border border-zinc-700">
                                ⚡ تولید تصادفی
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-500 mb-1.5">نام کاربری (Username)</label>
                                <input wire:model.live.debounce.500ms="username" type="text" dir="ltr" class="w-full bg-zinc-900 border {{ $errors->has('username') ? 'border-rose-500' : 'border-zinc-800' }} rounded-xl text-white p-3 text-sm font-mono focus:ring-1 focus:ring-emerald-500 outline-none">
                                @error('username') <span class="text-rose-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-500 mb-1.5">کلمه عبور (Password)</label>
                                <input wire:model="password" type="text" dir="ltr" class="w-full bg-zinc-900 border {{ $errors->has('password') ? 'border-rose-500' : 'border-zinc-800' }} rounded-xl text-white p-3 text-sm font-mono focus:ring-1 focus:ring-emerald-500 outline-none">
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-500 mb-1.5">تعداد اکانت جهت صدور</label>
                                <input wire:model.live.debounce.500ms="bulkCount" type="number" min="2" max="100" dir="ltr" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl text-white p-3 text-sm font-mono focus:ring-1 focus:ring-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-500 mb-1.5">پیشوند نام‌کاربری (اختیاری)</label>
                                <input wire:model="prefix" type="text" dir="ltr" placeholder="مثلاً: vip_" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl text-white p-3 text-sm font-mono outline-none">
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <button wire:click="save" class="w-full py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-base rounded-2xl shadow-[0_10px_20px_-10px_rgba(16,185,129,0.5)] transition flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    صدور و ثبت در دیتابیس
                </span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    در حال پردازش سرور...
                </span>
            </button>
        </div>
    </div>
</div>
