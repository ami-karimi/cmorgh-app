<div class="space-y-6 pb-12 animate-fade-in font-sans">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">مدیریت زیر‌نمایندگان</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">ساخت و مدیریت نمایندگان زیرمجموعه، شارژ کیف پول و بررسی آمار فروش</p>
        </div>

        <button wire:click="openCreateModal" class="px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-2xl font-black text-sm transition shadow-lg shadow-orange-500/20 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>ایجاد زیر‌نماینده جدید</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-orange-500/10 text-orange-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <span class="text-zinc-500 dark:text-zinc-400 text-xs font-bold block mb-0.5">کل زیر‌نمایندگان</span>
                <h3 class="text-2xl font-black text-zinc-900 dark:text-white font-mono-digit">{{ $stats['totalCount'] }} <span class="text-xs text-zinc-400 font-normal">نفر</span></h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-zinc-500 dark:text-zinc-400 text-xs font-bold block mb-0.5">نمایندگان فعال</span>
                <h3 class="text-2xl font-black text-emerald-500 font-mono-digit">{{ $stats['activeCount'] }} <span class="text-xs text-zinc-400 font-normal">نفر</span></h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <div>
                <span class="text-zinc-500 dark:text-zinc-400 text-xs font-bold block mb-0.5">مجموع موجودی زیر‌نمایندگان</span>
                <h3 class="text-2xl font-black text-zinc-900 dark:text-white font-mono-digit">{{ number_format($stats['totalBalance']) }} <span class="text-xs text-zinc-400 font-normal">تومان</span></h3>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between bg-zinc-50 dark:bg-zinc-900/50 p-3 rounded-2xl border border-zinc-200 dark:border-zinc-800">
        <div class="relative w-full md:w-80">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="جستجوی نام، یوزرنیم یا شماره..." class="w-full bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-700 text-sm text-zinc-900 dark:text-white rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-orange-500/50 outline-none transition">
        </div>
    </div>

    <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 text-[10px] font-black uppercase tracking-wider">
                    <th class="p-4">مشخصات نماینده</th>
                    <th class="p-4">تلفن / ایمیل</th>
                    <th class="p-4">موجود کیف پول</th>
                    <th class="p-4 text-center">تعداد اکانت‌ها</th>
                    <th class="p-4">وضعیت</th>
                    <th class="p-4 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-sm">
                @forelse($subAgents as $agent)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-2xl bg-orange-500/10 text-orange-500 font-black flex items-center justify-center text-sm">
                                    {{ mb_substr($agent->name, 0, 1) }}
                                </div>
                                <div>
                                    <strong class="block text-zinc-900 dark:text-white font-bold">{{ $agent->name }}</strong>
                                    <span class="text-xs text-zinc-400 font-mono" dir="ltr">@ {{ $agent->username }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="p-4">
                            <span class="block text-xs font-mono text-zinc-700 dark:text-zinc-300" dir="ltr">{{ $agent->phone ?? '-' }}</span>
                            <span class="block text-[11px] text-zinc-400 truncate max-w-[150px]">{{ $agent->email }}</span>
                        </td>

                        <td class="p-4">
                            <span class="font-black text-emerald-600 dark:text-emerald-400 font-mono-digit text-base">{{ number_format($agent->balance) }}</span>
                            <span class="text-[10px] text-zinc-400">تومان</span>
                        </td>

                        <td class="p-4 text-center">
                            <span class="inline-block px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-xs font-bold text-zinc-700 dark:text-zinc-300 font-mono">
                                {{ $agent->accounts_count }} اکانت
                            </span>
                        </td>

                        <td class="p-4">
                            <button wire:click="toggleStatus({{ $agent->id }})" class="cursor-pointer">
                                @if($agent->is_active)
                                    <span class="inline-flex px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-500 text-[10px] font-bold border border-emerald-500/20">
                                        فعال
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-500 text-[10px] font-bold border border-rose-500/20">
                                        غیرفعال
                                    </span>
                                @endif
                            </button>
                        </td>

                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="openWalletModal({{ $agent->id }})" class="p-2 rounded-xl bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white transition shadow-sm" title="شارژ / کسر موجودی">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                </button>

                                <button wire:click="openEditModal({{ $agent->id }})" class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:text-orange-500 transition shadow-sm" title="ویرایش اطلاعات">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-zinc-500 text-xs">هیچ زیر‌نماینده‌ای یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($subAgents->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30">
                {{ $subAgents->links() }}
            </div>
        @endif
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-md bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden">

                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-900/50">
                    <h3 class="text-base font-black text-zinc-900 dark:text-white">
                        {{ $editingAgentId ? 'ویرایش زیر‌نماینده' : 'ایجاد زیر‌نماینده جدید' }}
                    </h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-zinc-500 hover:text-rose-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">

                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">نام و نام خانوادگی <span class="text-rose-500">*</span></label>
                        <input wire:model="name" type="text" placeholder="مثلاً: علی محمدی" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-orange-500 text-zinc-900 dark:text-white">
                        @error('name') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">شماره تماس <span class="text-rose-500">*</span></label>
                        <input wire:model="phone" type="text" dir="ltr" placeholder="09123456789" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-orange-500 text-zinc-900 dark:text-white">
                        @error('phone') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">آدرس ایمیل (اختیاری)</label>
                        <input wire:model="email" type="email" dir="ltr" placeholder="example@mail.com" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-orange-500 text-zinc-900 dark:text-white">
                        @error('email') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">کلمه عبور {{ $editingAgentId ? '(در صورت عدم تغییر خالی بگذارید)' : '*' }}</label>
                        <input wire:model="password" type="password" dir="ltr" placeholder="******" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-orange-500 text-zinc-900 dark:text-white">
                        @error('password') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-2">وضعیت حساب کاربری</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" wire:click="$set('is_active', 1)" class="py-2.5 rounded-xl font-bold text-xs border transition flex items-center justify-center gap-1.5 {{ $is_active == 1 ? 'bg-emerald-500 text-white border-emerald-500 shadow-lg shadow-emerald-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-transparent' }}">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                <span>فعال</span>
                            </button>
                            <button type="button" wire:click="$set('is_active', 0)" class="py-2.5 rounded-xl font-bold text-xs border transition flex items-center justify-center gap-1.5 {{ $is_active == 0 ? 'bg-rose-500 text-white border-rose-500 shadow-lg shadow-rose-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-transparent' }}">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                <span>غیرفعال</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button wire:click="saveAgent" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-black text-sm rounded-xl transition shadow-lg shadow-orange-500/20">
                            {{ $editingAgentId ? 'ثبت تغییرات' : 'ذخیره و ایجاد زیر‌نماینده' }}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    @if($isWalletModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-sm bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-900/50">
                    <h3 class="text-base font-black text-zinc-900 dark:text-white">انتقال / کسر موجودی کیف پول</h3>
                    <button wire:click="$set('isWalletModalOpen', false)" class="text-zinc-500 hover:text-rose-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-2">نوع تراکنش</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button wire:click="$set('walletType', 'plus')" class="py-2.5 rounded-xl font-bold text-xs border transition {{ $walletType === 'plus' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-transparent' }}">
                                ➕ افزایش شارژ
                            </button>
                            <button wire:click="$set('walletType', 'minus')" class="py-2.5 rounded-xl font-bold text-xs border transition {{ $walletType === 'minus' ? 'bg-rose-500 text-white border-rose-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-transparent' }}">
                                ➖ کسر شارژ
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">مبلغ (تومان)</label>
                        <input wire:model="walletAmount" type="number" dir="ltr" placeholder="مثلا 500000" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-orange-500">
                        @error('walletAmount') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">توضیحات (اختیاری)</label>
                        <input wire:model="walletDescription" type="text" placeholder="علت انتقال یا کسر..." class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div class="pt-2">
                        <button wire:click="processWalletTransaction" class="w-full py-3 {{ $walletType === 'plus' ? 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20' : 'bg-rose-500 hover:bg-rose-600 shadow-rose-500/20' }} text-white font-black text-sm rounded-xl transition shadow-lg">
                            تایید و انجام تراکنش
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
