<div class="space-y-6 pb-12">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">مدیریت مشتریان</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">لیست مشتریان اختصاصی شما و دسترسی به اکانت‌های متصل</p>
        </div>

        <button wire:click="openModal" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-400 text-white font-bold text-sm shadow-lg shadow-orange-500/20 transition-all hover:-translate-y-0.5">
            + ثبت مشتری جدید
        </button>
    </div>

    @if (session()->has('message'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-bold">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-2xl p-4 shadow-sm flex items-center">
        <div class="relative w-full">
            <svg class="w-4 h-4 absolute right-4 top-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="جستجوی نام، شماره تماس یا ایمیل مشتری..." class="w-full bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 text-sm rounded-xl py-2.5 pr-11 pl-4 focus:outline-none focus:ring-2 focus:ring-orange-500/50 transition-all">
        </div>
    </div>

    <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-400 text-xs font-bold uppercase tracking-wider bg-zinc-50/50 dark:bg-zinc-900/30">
                    <th class="p-5">مشخصات مشتری</th>
                    <th class="p-5">اطلاعات تماس</th>
                    <th class="p-5">اکانت‌های VPN متصل</th>
                    <th class="p-5">وضعیت دسترسی</th>
                    <th class="p-5 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50 text-sm">
                @forelse($customers as $customer)
                    <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/30 transition-colors">
                        <td class="p-5">
                            <div class="font-bold text-zinc-900 dark:text-white text-base">{{ $customer->name }}</div>
                            <span class="text-xs font-mono-digit text-zinc-400 mt-0.5 block">ID: #{{ $customer->id }}</span>
                        </td>

                        <td class="p-5 font-mono-digit text-xs text-zinc-600 dark:text-zinc-300">
                            <div>{{ $customer->phone ?? 'بدون شماره' }}</div>
                            <div class="text-zinc-400 mt-1">{{ $customer->email ?? '-' }}</div>
                        </td>

                        <td class="p-5 relative group">
                            @if($customer->vpnAccounts->count() > 0)
                                <span class="inline-flex items-center px-3 py-1 bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20 text-xs font-bold rounded-lg cursor-pointer">
                                        {{ $customer->vpnAccounts->count() }} اکانت فعال
                                    </span>

                                <div class="absolute right-5 bottom-full mb-2 w-48 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 p-3 space-y-1">
                                    <p class="text-[10px] text-zinc-400 font-bold mb-1">لیست سرویس‌ها:</p>
                                    @foreach($customer->vpnAccounts as $acc)
                                        <div class="flex items-center justify-between text-xs font-mono-digit py-1 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                                            <span class="text-zinc-700 dark:text-zinc-300" dir="ltr">{{ $acc->username }}</span>
                                            <span class="w-2 h-2 rounded-full {{ $acc->is_enabled ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2.5 py-1 rounded-md">بدون اکانت</span>
                            @endif
                        </td>

                        <td class="p-5">
                            <button wire:click="toggleStatus({{ $customer->id }})" class="relative inline-flex items-center cursor-pointer">
                                <div class="w-11 h-6 bg-zinc-200 dark:bg-zinc-800 rounded-full transition-colors {{ $customer->is_active ? 'bg-emerald-500 dark:bg-emerald-500' : '' }}"></div>
                                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $customer->is_active ? 'translate-x-5' : '' }}"></div>
                            </button>
                            <span class="text-xs font-medium ml-2 {{ $customer->is_active ? 'text-emerald-500' : 'text-zinc-400' }}">
                                    {{ $customer->is_active ? 'فعال' : 'مسدود' }}
                                </span>
                        </td>

                        <td class="p-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('reseller.users.show', $customer->id) }}" wire:navigate class="p-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-emerald-500 hover:text-white transition-all shadow-sm" title="مشاهده پرونده و اکانت‌ها">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                <button wire:click="edit({{ $customer->id }})" class="p-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-orange-500 hover:text-white transition-all shadow-sm" title="ویرایش مشتری">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-zinc-400 text-sm">هیچ مشتری‌ای یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-lg bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-6">
                    {{ $customerId ? 'ویرایش مشخصات مشتری' : 'ثبت مشتری جدید' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 mb-1.5">نام و نام خانوادگی <span class="text-rose-500">*</span></label>
                        <input wire:model="name" type="text" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/50">
                        @error('name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-500 mb-1.5">شماره تماس</label>
                        <input wire:model="phone" type="text" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/50 font-mono-digit" dir="ltr">
                        @error('phone') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-500 mb-1.5">ایمیل (اختیاری)</label>
                        <input wire:model="email" type="email" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/50 font-mono-digit" dir="ltr">
                        @error('email') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</i> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-500 mb-1.5">رمز عبور @if(!$customerId) <span class="text-rose-500">*</span> @endif</label>
                        <input wire:model="password" type="text" placeholder="{{ $customerId ? 'در صورت عدم تغییر خالی بگذارید' : '' }}" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/50 font-mono-digit" dir="ltr">
                        @error('password') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input wire:model="is_active" type="checkbox" id="is_active" class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500">
                        <label for="is_active" class="text-sm font-medium text-zinc-700 dark:text-zinc-300 cursor-pointer">حساب کاربری فعال باشد</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-5 py-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-sm font-bold">انصراف</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white text-sm font-bold shadow-lg shadow-orange-500/20">ذخیره اطلاعات</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
