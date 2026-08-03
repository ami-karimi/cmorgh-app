<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">مدیریت مالی و کیف پول</h2>
            <p class="text-xs text-zinc-400 mt-1">مدیریت فیش‌های واریزی و حساب زیرنمایندگان</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 px-6 py-4 rounded-2xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] text-zinc-400 font-bold mb-1">موجودی کیف پول شما</p>
                <div class="text-xl font-black text-emerald-400 font-mono">{{ number_format($balance) }} <span class="text-xs text-zinc-500 font-sans">تومان</span></div>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 border-b border-zinc-800/80 pb-3">
        <button wire:click="$set('activeTab', 'my_wallet')" class="px-5 py-2 text-xs font-bold rounded-xl transition-all {{ $activeTab === 'my_wallet' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-zinc-500 hover:text-zinc-300' }}">
            💳 کیف پول من (ارسال فیش به مدیر)
        </button>
        <button wire:click="$set('activeTab', 'sub_agents')" class="px-5 py-2 text-xs font-bold rounded-xl transition-all {{ $activeTab === 'sub_agents' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'text-zinc-500 hover:text-zinc-300' }}">
            👥 امور مالی زیرنمایندگان
        </button>
    </div>

    @if($activeTab === 'my_wallet')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
            <div class="bg-[#09090b] border border-zinc-800/80 rounded-2xl p-5 shadow-sm">
                <h3 class="text-sm font-bold text-white mb-4">ثبت فیش واریزی جدید</h3>

                @if (session()->has('success'))
                    <div class="p-3 mb-4 text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit.prevent="submitMyReceipt" class="space-y-4">
                    <div>
                        <label class="block text-[11px] text-zinc-400 mb-1">مبلغ واریزی (تومان)</label>
                        <input wire:model="myAmount" type="number" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700 text-white text-sm rounded-xl p-3 font-mono focus:ring-1 focus:ring-orange-500">
                        @error('myAmount') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] text-zinc-400 mb-1">تصویر فیش</label>
                        <input wire:model="myReceipt" type="file" accept="image/*" class="w-full bg-zinc-900 border border-zinc-700 text-zinc-400 text-xs rounded-xl p-2.5 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700">
                        @error('myReceipt') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] text-zinc-400 mb-1">توضیحات (اختیاری)</label>
                        <input wire:model="myDescription" type="text" class="w-full bg-zinc-900 border border-zinc-700 text-white text-xs rounded-xl p-3 focus:ring-1 focus:ring-orange-500">
                    </div>
                    <button type="submit" class="w-full py-3 bg-white text-black font-black text-xs rounded-xl hover:bg-zinc-200 transition">
                        آپلود و ثبت فیش
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-[#09090b] border border-zinc-800/80 rounded-2xl p-5 shadow-sm overflow-hidden">
                <h3 class="text-sm font-bold text-white mb-4">تاریخچه تراکنش‌های من</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="text-zinc-500 bg-zinc-900/50">
                        <tr>
                            <th class="p-3 rounded-r-xl">مبلغ (تومان)</th>
                            <th class="p-3">نوع تراکنش</th>
                            <th class="p-3">وضعیت</th>
                            <th class="p-3 rounded-l-xl">تاریخ</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                        @foreach($myTransactions as $tx)
                            @php
                                $isPlus = in_array($tx->type, ['plus', 'plus_amn']);
                                $color = $isPlus ? 'text-emerald-400' : 'text-red-400';
                                $sign = $isPlus ? '+' : '-';
                            @endphp
                            <tr class="hover:bg-zinc-900/30 transition">
                                <td class="p-3 font-mono font-bold {{ $color }}">{{ $sign }}{{ number_format($tx->price) }}</td>
                                <td class="p-3 text-[11px]">{{ $tx->description ?? 'ثبت سیستمی' }}</td>
                                <td class="p-3">
                                    @if($tx->approved == 1)
                                        <span class="px-2 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] rounded-lg">تایید شده</span>
                                    @elseif($tx->approved == 0)
                                        <span class="px-2 py-1 bg-amber-500/10 text-amber-400 text-[10px] rounded-lg">در انتظار مدیر</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-500/10 text-red-400 text-[10px] rounded-lg">رد شده</span>
                                    @endif
                                </td>
                                <td class="p-3 text-[10px] text-zinc-500 font-mono">{{ $tx->shamsi_date }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($activeTab === 'sub_agents')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
            <div class="bg-[#09090b] border border-zinc-800/80 rounded-2xl p-5 shadow-sm">
                <h3 class="text-sm font-bold text-white mb-4">مدیریت موجودی زیرنماینده</h3>

                @if (session()->has('success_sub'))
                    <div class="p-3 mb-4 text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                        {{ session('success_sub') }}
                    </div>
                @endif
                @if (session()->has('error_sub'))
                    <div class="p-3 mb-4 text-[11px] font-bold text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl">
                        {{ session('error_sub') }}
                    </div>
                @endif

                <form wire:submit.prevent="manageSubAgentBalance" class="space-y-4">
                    <div>
                        <label class="block text-[11px] text-zinc-400 mb-1">انتخاب زیرنماینده</label>
                        <select wire:model="subAgentId" class="w-full bg-zinc-900 border border-zinc-700 text-white text-xs rounded-xl p-3 focus:ring-1 focus:ring-purple-500">
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($subAgents as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->username }})</option>
                            @endforeach
                        </select>
                        @error('subAgentId') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] text-zinc-400 mb-1">نوع عملیات</label>
                        <select wire:model="subType" class="w-full bg-zinc-900 border border-zinc-700 text-white text-xs rounded-xl p-3 focus:ring-1 focus:ring-purple-500">
                            <option value="plus">افزایش موجودی (کسر از کیف پول من)</option>
                            <option value="minus">کسر موجودی (بازگشت به کیف پول من)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] text-zinc-400 mb-1">مبلغ (تومان)</label>
                        <input wire:model="subAmount" type="number" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700 text-white text-sm rounded-xl p-3 font-mono focus:ring-1 focus:ring-purple-500">
                        @error('subAmount') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] text-zinc-400 mb-1">توضیحات</label>
                        <input wire:model="subDescription" type="text" placeholder="مثال: تسویه نقدی" class="w-full bg-zinc-900 border border-zinc-700 text-white text-xs rounded-xl p-3 focus:ring-1 focus:ring-purple-500">
                    </div>
                    <button type="submit" class="w-full py-3 bg-purple-500 text-white font-black text-xs rounded-xl hover:bg-purple-600 transition">
                        اعمال تغییرات
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-[#09090b] border border-zinc-800/80 rounded-2xl p-5 shadow-sm overflow-hidden">
                <h3 class="text-sm font-bold text-white mb-4">فیش‌ها و تراکنش‌های زیرنمایندگان</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="text-zinc-500 bg-zinc-900/50">
                        <tr>
                            <th class="p-3 rounded-r-xl">زیرنماینده</th>
                            <th class="p-3">مبلغ (تومان)</th>
                            <th class="p-3">فیش / توضیح</th>
                            <th class="p-3">وضعیت / عملیات</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                        @foreach($subTransactions as $tx)
                            @php
                                $isPlus = in_array($tx->type, ['plus', 'plus_amn']);
                                $color = $isPlus ? 'text-emerald-400' : 'text-red-400';
                                $sign = $isPlus ? '+' : '-';
                                $subUser = $subAgents->firstWhere('id', $tx->for);
                            @endphp
                            <tr class="hover:bg-zinc-900/30 transition">
                                <td class="p-3 text-[11px] font-bold text-white">{{ $subUser->name ?? 'کاربر نامشخص' }}</td>
                                <td class="p-3 font-mono font-bold {{ $color }}">{{ $sign }}{{ number_format($tx->price) }}</td>
                                <td class="p-3 text-[11px]">
                                    {{ $tx->description }}
                                    @if($tx->attachment)
                                        <a href="{{ asset('storage/' . $tx->attachment) }}" target="_blank" class="text-blue-400 underline block mt-1">مشاهده فیش</a>
                                    @endif
                                </td>
                                <td class="p-3">
                                    @if($tx->approved == 1)
                                        <span class="px-2 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] rounded-lg">تایید شده</span>
                                    @elseif($tx->approved == 0)
                                        <div class="flex gap-2">
                                            <button wire:click="toggleSubAgentReceipt({{ $tx->id }}, 1)" class="px-2 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] rounded-md transition">تایید و کسر</button>
                                            <button wire:click="toggleSubAgentReceipt({{ $tx->id }}, 2)" class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-[10px] rounded-md transition">رد</button>
                                        </div>
                                    @else
                                        <span class="px-2 py-1 bg-red-500/10 text-red-400 text-[10px] rounded-lg">رد شده</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
