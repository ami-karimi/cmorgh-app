<div class="space-y-6 pb-12">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">پرونده جامع مشتری: {{ $customer->name }}</h1>
            <p class="text-xs text-zinc-500 mt-1 font-medium">شناسه سیستم: #{{ $customer->id }} | تاریخ عضویت: {{ \Morilog\Jalali\Jalalian::fromCarbon($customer->created_at)->format('Y/m/d') }}</p>
        </div>

        <a href="{{ route('reseller.customers') }}" wire:navigate class="px-5 py-2.5 bg-zinc-900 border border-zinc-800 hover:bg-zinc-800 text-zinc-300 text-xs font-bold rounded-xl transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            بازگشت به لیست
        </a>
    </div>

    @if (session()->has('profile_msg'))
        <div class="p-3 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">{{ session('profile_msg') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 flex items-center justify-center text-white font-black text-xl shadow-lg">
                        {{ mb_substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ $customer->name }}</h2>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-zinc-800 text-zinc-400 mt-1 inline-block">
                            نقش: {{ $customer->role === 'customer' ? 'مشتری عادی' : 'زیرنماینده' }}
                        </span>
                    </div>
                </div>
                <button wire:click="openEditModal" class="p-2 bg-zinc-800 hover:bg-orange-500 hover:text-white text-zinc-400 rounded-lg transition" title="ویرایش مشخصات">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
            </div>

            <div class="space-y-3 flex-1 text-sm">
                <div class="flex justify-between items-center py-2.5 border-b border-zinc-800/50">
                    <span class="text-xs text-zinc-500">ایجاد کننده</span>
                    <span class="text-xs font-bold text-orange-400">{{ $creatorName }}</span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-zinc-800/50">
                    <span class="text-xs text-zinc-500">شماره تماس</span>
                    <span class="text-xs font-bold text-white font-mono" dir="ltr">{{ $customer->phone ?? 'ثبت نشده' }}</span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-zinc-800/50">
                    <span class="text-xs text-zinc-500">آدرس ایمیل</span>
                    <span class="text-xs font-bold text-white font-mono" dir="ltr">{{ $customer->email ?? 'ثبت نشده' }}</span>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-zinc-800/50">
                    <span class="text-xs text-zinc-500">تعداد اکانت‌ها</span>
                    <span class="text-xs font-bold text-emerald-400">{{ $customer->vpnAccounts->count() }} سرویس</span>
                </div>
                <div class="flex justify-between items-center pt-2.5">
                    <span class="text-xs text-zinc-500">دسترسی ورود به پنل</span>
                    <button wire:click="toggleUserStatus" class="relative inline-flex items-center cursor-pointer">
                        <div class="w-10 h-5 rounded-full transition-colors {{ $customer->is_active ? 'bg-emerald-500' : 'bg-zinc-700' }}"></div>
                        <div class="absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-transform {{ $customer->is_active ? 'translate-x-5' : '' }}"></div>
                    </button>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        سرویس‌های VPN مشتری
                    </h2>
                </div>
                <a href="{{ route('reseller.accounts.create', ['customer_id' => $customer->id]) }}" wire:navigate  class="px-4 py-2 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded-xl transition shadow-lg whitespace-nowrap">+ صدور اکانت جدید</a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-right text-xs">
                    <thead class="bg-zinc-950/80 text-zinc-400 font-bold border-b border-zinc-800">
                    <tr>
                        <th class="p-4 rounded-tr-lg">شناسه کاربری (Username)</th>
                        <th class="p-4">وضعیت</th>
                        <th class="p-4">انقضا</th>
                        <th class="p-4 rounded-tl-lg text-center">مدیریت</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                    @forelse($customer->vpnAccounts as $account)
                        <tr class="hover:bg-zinc-800/30 transition-colors">
                            <td class="p-4 font-mono font-bold text-white" dir="ltr">{{ $account->username }}</td>
                            <td class="p-4">
                                @if($account->is_enabled)
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20 text-[10px]">فعال</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 font-bold border border-red-500/20 text-[10px]">مسدود</span>
                                @endif
                            </td>
                            <td class="p-4 text-zinc-400 font-mono">
                                {{ $account->expire_date ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($account->expire_date))->format('Y/m/d') : 'نامحدود' }}
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('reseller.accounts.show', $account->id) }}" wire:navigate class="inline-block p-2 bg-zinc-800 hover:bg-emerald-500 hover:text-white text-zinc-400 rounded-lg transition text-[10px] font-bold">مشاهده سرویس</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-zinc-500 font-medium">هیچ اکانت VPN برای این کاربر ثبت نشده است.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-xl">💳</div>
                <div>
                    <h2 class="text-base font-bold text-white">کیف پول و تاریخچه تراکنش‌ها</h2>
                    <p class="text-xs text-zinc-500 mt-1">موجودی فعلی کاربر: <strong class="text-emerald-400 font-mono text-sm">{{ number_format($balance) }}</strong> تومان</p>
                </div>
            </div>
            <button wire:click="openTrxModal" class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl border border-zinc-700 transition flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                ثبت تراکنش دستی
            </button>
        </div>

        @if (session()->has('trx_msg'))
            <div class="p-3 mb-4 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">{{ session('trx_msg') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-zinc-950/80 text-zinc-400 font-bold border-b border-zinc-800">
                <tr>
                    <th class="p-4 rounded-tr-lg">شرح تراکنش</th>
                    <th class="p-4">نوع</th>
                    <th class="p-4 text-left">مبلغ (تومان)</th>
                    <th class="p-4 rounded-tl-lg">تاریخ ثبت</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                @forelse($transactions as $trx)
                    <tr class="hover:bg-zinc-800/30 transition-colors">
                        <td class="p-4 font-medium">{{ $trx->description }}</td>
                        <td class="p-4">
                            @if(in_array($trx->type, ['plus', 'plus_amn']))
                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20 text-[10px]">واریز/شارژ</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 font-bold border border-red-500/20 text-[10px]">برداشت/خرید</span>
                            @endif
                        </td>
                        <td class="p-4 text-left font-bold font-mono text-sm {{ in_array($trx->type, ['plus', 'plus_amn']) ? 'text-emerald-400' : 'text-red-400' }}" dir="ltr">
                            {{ in_array($trx->type, ['plus', 'plus_amn']) ? '+' : '-' }}{{ number_format($trx->price) }}
                        </td>
                        <td class="p-4 text-zinc-500 font-mono">{{ \Morilog\Jalali\Jalalian::fromCarbon($trx->created_at)->format('Y/m/d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-zinc-500 font-medium">هیچ تراکنشی ثبت نشده است.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pt-4">{{ $transactions->links() }}</div>
    </div>

    @if($isEditModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="w-full max-w-lg bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-white mb-6">ویرایش مشخصات: {{ $customer->name }}</h3>

                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">نام و نام خانوادگی</label>
                            <input wire:model="editName" type="text" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 focus:ring-orange-500">
                            @error('editName') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">نقش کاربر</label>
                            <select wire:model="editRole" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 focus:ring-orange-500">
                                <option value="customer">مشتری عادی</option>
                                <option value="sub_agent">زیرنماینده</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">شماره تماس</label>
                            <input wire:model="editPhone" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 font-mono focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">آدرس ایمیل</label>
                            <input wire:model="editEmail" type="email" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 font-mono focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">تغییر رمز عبور <span class="text-zinc-600 font-normal">(در صورت عدم تغییر خالی بگذارید)</span></label>
                        <input wire:model="editPassword" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 font-mono focus:ring-orange-500">
                        @error('editPassword') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 mt-6 border-t border-zinc-800">
                        <button type="button" wire:click="$set('isEditModalOpen', false)" class="px-5 py-2.5 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-800 rounded-xl transition">لغو</button>
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-orange-600 hover:bg-orange-500 rounded-xl shadow-lg transition">ذخیره تغییرات</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($isTrxModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-white mb-6">ثبت تراکنش برای: {{ $customer->name }}</h3>
                <form wire:submit.prevent="addTransaction" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">نوع تراکنش</label>
                        <select wire:model="newType" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 focus:ring-orange-500">
                            <option value="plus">افزایش موجودی کاربر (+)</option>
                            <option value="minus">کسر موجودی کاربر (-)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">مبلغ تراکنش (تومان)</label>
                        <input wire:model="newPrice" type="number" dir="ltr" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 font-mono focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">شرح تراکنش</label>
                        <input wire:model="newDescription" type="text" placeholder="بابت..." class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-xl text-sm p-3 focus:ring-orange-500">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 mt-6 border-t border-zinc-800">
                        <button type="button" wire:click="$set('isTrxModalOpen', false)" class="px-5 py-2.5 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-800 rounded-xl transition">لغو</button>
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-orange-600 hover:bg-orange-500 rounded-xl shadow-lg transition">ثبت نهایی</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
