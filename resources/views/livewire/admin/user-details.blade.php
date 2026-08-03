<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">پروفایل مشتری: {{ $user->name }}</h1>
            <p class="text-xs text-zinc-500 mt-1 font-medium">مشاهده اطلاعات هویتی و لیست سرویس‌های متصل به کاربر</p>
        </div>

        <a href="{{ route('admin.users.index') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-sm rounded-xl border border-zinc-700 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            بازگشت به لیست
        </a>
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-orange-600 to-red-600"></div>

                <div class="flex flex-col items-center text-center mt-2">
                    <img class="w-24 h-24 rounded-full object-cover border-4 border-zinc-800 shadow-lg mb-4" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=27272a&color=f97316&size=128" alt="Avatar">
                    <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                    <span class="text-sm text-zinc-400 mt-1">مشتری عادی (شناسه: {{ $user->id }})</span>
                </div>

                <hr class="border-zinc-800 my-6">

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-zinc-500">شماره تماس:</span>
                        <span class="text-sm text-zinc-300 font-mono" dir="ltr">{{ $user->phone ?? 'ثبت نشده' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-zinc-500">ایمیل (ورود):</span>
                        <span class="text-sm text-zinc-300 font-mono" dir="ltr">{{ $user->email ?? 'ثبت نشده' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-zinc-500">تاریخ ثبت‌نام:</span>
                        <span class="text-sm text-zinc-300">{{ $user->created_at ? \Morilog\Jalali\Jalalian::fromCarbon($user->created_at)->format('%d %B %Y') : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-zinc-500">وضعیت ورود به پنل:</span>
                        <label class="relative inline-flex items-center cursor-pointer" wire:click.prevent="toggleUserStatus">
                            <input type="checkbox" class="sr-only peer" {{ $user->is_active ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 border border-zinc-700"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-2xl">
                <h3 class="text-sm font-bold text-white mb-4">خلاصه سرویس‌ها</h3>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="bg-zinc-950/50 border border-zinc-800 rounded-xl p-3">
                        <span class="block text-2xl font-black text-orange-500 mb-1">{{ $user->vpnAccounts->count() }}</span>
                        <span class="text-xs text-zinc-500">کل اکانت‌ها</span>
                    </div>
                    <div class="bg-zinc-950/50 border border-zinc-800 rounded-xl p-3">
                        <span class="block text-2xl font-black text-emerald-500 mb-1">{{ $user->vpnAccounts->where('is_enabled', 1)->count() }}</span>
                        <span class="text-xs text-zinc-500">اکانت‌های فعال</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden shadow-2xl h-full">
                <div class="p-5 border-b border-zinc-800/80 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white">اکانت‌های VPN متصل به این کاربر</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                        <tr class="bg-zinc-950/80 border-b border-zinc-800/80 text-zinc-400 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4">نام کاربری (اکانت)</th>
                            <th class="p-4">گروه / سرویس</th>
                            <th class="p-4">وضعیت</th>
                            <th class="p-4 text-center">عملیات</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50 text-sm">
                        @forelse($user->vpnAccounts as $account)
                            <tr class="hover:bg-zinc-800/20 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-orange-400 font-mono text-base" dir="ltr">{{ $account->username }}</div>
                                    <div class="text-[11px] text-zinc-500 mt-1">تعداد کاربر مجاز: {{ $account->multi_login }}</div>
                                </td>
                                <td class="p-4">
                                        <span class="px-2 py-1 bg-zinc-800 rounded text-zinc-300 text-xs border border-zinc-700 block w-fit mb-1">
                                            {{ $account->group->name ?? 'بدون گروه' }}
                                        </span>
                                    <span class="text-[10px] text-zinc-500 uppercase">{{ str_replace('_', ' ', $account->service_group) }}</span>
                                </td>
                                <td class="p-4">
                                    @if($account->is_enabled)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> فعال
                                            </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> غیرفعال
                                            </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('admin.accounts.show', $account->id) }}" wire:navigate class="inline-flex items-center justify-center p-2 bg-zinc-800/60 hover:bg-zinc-700 text-zinc-400 hover:text-white rounded-lg border border-zinc-700/50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-10 text-center text-zinc-500 font-medium">
                                    <svg class="w-10 h-10 mx-auto text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4M12 20V4"></path></svg>
                                    هیچ اکانت VPN برای این مشتری ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <div class="mt-8">
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-5 border-b border-zinc-800/80 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-bold text-white">کیف پول و سوابق مالی</h3>
                    <div class="bg-zinc-950 px-4 py-1.5 rounded-lg border border-zinc-800">
                        <span class="text-xs text-zinc-500">موجودی فعلی:</span>
                        <span class="font-black text-sm {{ $this->balance >= 0 ? 'text-emerald-400' : 'text-red-400' }} ml-1">
                            {{ number_format($this->balance) }} تومان
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button wire:click="openTxModal('plus')" class="px-4 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 text-xs font-bold rounded-lg border border-emerald-500/30 transition">
                        + افزایش موجودی (شارژ)
                    </button>
                    <button wire:click="openTxModal('minus')" class="px-4 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 text-xs font-bold rounded-lg border border-red-500/30 transition">
                        - کسر از حساب
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                    <tr class="bg-zinc-950/80 border-b border-zinc-800/80 text-zinc-400 text-xs font-bold uppercase tracking-wider">
                        <th class="p-4">کد / تاریخ</th>
                        <th class="p-4">نوع تراکنش</th>
                        <th class="p-4">مبلغ (تومان)</th>
                        <th class="p-4">توضیحات</th>
                        <th class="p-4">پیوست / فیش</th>
                        <th class="p-4">وضعیت (تایید)</th>
                        <th class="p-4 text-center">عملیات</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50 text-sm">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-zinc-800/20 transition-colors">
                            <td class="p-4">
                                <span class="text-xs font-mono text-zinc-500 block">#{{ $tx->id }}</span>
                                <span class="text-[11px] text-zinc-400">{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($tx->created_at))->format('%Y/%m/%d H:i') }}</span>
                            </td>

                            <td class="p-4">
                                @if(in_array($tx->type, ['plus', 'plus_amn']))
                                    <span class="px-2 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded border border-emerald-500/20">افزایش موجودی</span>
                                @else
                                    <span class="px-2 py-1 bg-red-500/10 text-red-400 text-[10px] font-bold rounded border border-red-500/20">کسر از حساب</span>
                                @endif
                            </td>

                            <td class="p-4 font-mono font-bold {{ in_array($tx->type, ['plus', 'plus_amn']) ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ in_array($tx->type, ['plus', 'plus_amn']) ? '+' : '-' }} {{ number_format($tx->price) }}
                            </td>

                            <td class="p-4 text-xs text-zinc-400 max-w-[200px] truncate" title="{{ $tx->description }}">
                                {{ $tx->description ?? '-' }}
                            </td>

                            <td class="p-4 text-center">
                                @if($tx->attachment)
                                    <a href="{{ asset($tx->attachment) }}" target="_blank" class="text-blue-400 hover:text-blue-300 text-xs flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        مشاهده فیش
                                    </a>
                                @else
                                    <span class="text-xs text-zinc-600">-</span>
                                @endif
                            </td>

                            <td class="p-4">
                                <label class="relative inline-flex items-center cursor-pointer" wire:click.prevent="toggleTxApproval({{ $tx->id }})">
                                    <input type="checkbox" class="sr-only peer" {{ $tx->approved ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 border border-zinc-700"></div>
                                    <span class="ms-2 text-[10px] {{ $tx->approved ? 'text-emerald-400' : 'text-zinc-500' }}">{{ $tx->approved ? 'تایید شده' : 'رد/معلق' }}</span>
                                </label>
                            </td>

                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openTxModal('edit', {{ $tx->id }})" title="ویرایش تراکنش" class="p-1.5 bg-zinc-800/60 hover:bg-zinc-700 text-zinc-400 hover:text-blue-400 rounded-lg border border-zinc-700/50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="deleteTransaction({{ $tx->id }})" onclick="confirm('آیا از حذف این تراکنش مطمئن هستید؟ موجودی تغییر خواهد کرد.') || event.stopImmediatePropagation()" title="حذف" class="p-1.5 bg-zinc-800/60 hover:bg-zinc-700 text-zinc-400 hover:text-red-400 rounded-lg border border-zinc-700/50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-zinc-500 font-medium">هیچ تراکنشی برای این کاربر ثبت نشده است.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($isTxModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/70 backdrop-blur-sm p-4">
            <div class="relative w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        @if($txId) ویرایش تراکنش @else ثبت تراکنش جدید @endif
                        <span class="text-xs px-2 py-1 rounded {{ $txType == 'plus' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $txType == 'plus' ? 'افزایش موجودی' : 'کسر موجودی' }}
                        </span>
                    </h3>

                    <form wire:submit.prevent="saveTransaction" class="space-y-4">
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">نوع تراکنش <span class="text-red-500">*</span></label>
                            <select wire:model="txType" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-sm rounded-xl p-2.5 focus:ring-orange-500">
                                <option value="plus">افزایش موجودی (شارژ حساب)</option>
                                <option value="plus_amn">شارژ امن</option>
                                <option value="minus">کسر از موجودی</option>
                            </select>
                            @error('txType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">مبلغ (تومان) <span class="text-red-500">*</span></label>
                            <input wire:model="txPrice" type="number" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-sm rounded-xl p-2.5 focus:ring-orange-500" dir="ltr">
                            @error('txPrice') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">توضیحات (اختیاری)</label>
                            <textarea wire:model="txDesc" rows="2" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-sm rounded-xl p-2.5 focus:ring-orange-500" placeholder="بابت..."></textarea>
                        </div>

                        <div class="flex items-center mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="txApproved" class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ms-3 text-sm font-medium text-zinc-300">تایید نهایی تراکنش (اعمال در کیف پول)</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-800 mt-6">
                            <button type="button" wire:click="$set('isTxModalOpen', false)" class="px-5 py-2.5 text-sm font-medium text-zinc-400 hover:text-white bg-zinc-800 hover:bg-zinc-700 rounded-xl transition">لغو</button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-orange-600 hover:bg-orange-500 rounded-xl shadow-lg transition">ذخیره تراکنش</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
