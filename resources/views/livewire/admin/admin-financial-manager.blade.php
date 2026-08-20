<div class="space-y-6 animate-fade-in pb-10">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">مدیریت مالی و تسویه‌حساب‌ها</h1>
            <p class="text-xs text-zinc-500 mt-1 font-medium">بررسی درآمدهای سیستم، ویرایش تراکنش‌ها و بررسی فیش‌های واریزی</p>
        </div>

        <!-- دکمه ایجاد تراکنش جدید -->
        <button wire:click="create" class="px-5 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            ثبت تراکنش دستی جدید
        </button>
    </div>

    <!-- کارت‌های آمار مالی -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-zinc-900/50 border border-zinc-800/80 rounded-2xl p-5 flex items-center justify-between shadow-lg">
            <div>
                <p class="text-xs font-bold text-zinc-500 mb-1">درآمد امروز (تایید شده)</p>
                <div class="flex items-end gap-1.5">
                    <span class="text-2xl font-black text-emerald-400 font-mono-digit">{{ number_format($todayRevenue) }}</span>
                    <span class="text-[10px] text-zinc-500 mb-1">تومان</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-zinc-900/50 border border-zinc-800/80 rounded-2xl p-5 flex items-center justify-between shadow-lg">
            <div>
                <p class="text-xs font-bold text-zinc-500 mb-1">درآمد این ماه (تایید شده)</p>
                <div class="flex items-end gap-1.5">
                    <span class="text-2xl font-black text-blue-400 font-mono-digit">{{ number_format($monthRevenue) }}</span>
                    <span class="text-[10px] text-zinc-500 mb-1">تومان</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
        </div>

        <div class="bg-zinc-900/50 border border-zinc-800/80 rounded-2xl p-5 flex items-center justify-between shadow-lg">
            <div>
                <p class="text-xs font-bold text-zinc-500 mb-1">در انتظار تایید ({{ $pendingCount }} فیش)</p>
                <div class="flex items-end gap-1.5">
                    <span class="text-2xl font-black text-amber-400 font-mono-digit">{{ number_format($pendingAmount) }}</span>
                    <span class="text-[10px] text-zinc-500 mb-1">تومان</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-600 to-red-600 rounded-2xl p-5 flex flex-col justify-center items-center shadow-lg shadow-orange-500/20 relative overflow-hidden">
            <svg class="w-24 h-24 absolute -right-4 -bottom-4 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.97-1.3-3.15-3.61-3.15V7h-1.62v1.51c-1.89.28-3.19 1.4-3.19 3.01 0 1.83 1.54 2.65 3.91 3.24 1.87.47 2.45 1.13 2.45 1.9 0 .9-.85 1.52-2.22 1.52-1.64 0-2.32-.82-2.37-1.84H7.29c.07 2.21 1.56 3.32 3.82 3.32V21h1.62v-1.46c2.08-.26 3.44-1.45 3.44-3.23 0-2.07-1.75-2.73-3.86-3.17z"/></svg>
            <h3 class="text-white font-black text-lg z-10">مدیریت مالی</h3>
            <p class="text-white/80 text-xs mt-1 z-10">ترازنامه شفاف سیستم</p>
        </div>
    </div>

    <!-- کادر جستجو و فیلترها -->
    <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800/60 p-4 rounded-2xl flex flex-col md:flex-row items-center gap-4 shadow-inner">
        <div class="relative w-full md:flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full bg-zinc-950/60 border border-zinc-800 text-zinc-300 text-sm rounded-xl focus:ring-2 focus:ring-orange-500/50 block p-3 transition outline-none" placeholder="جستجو در کد پیگیری، نام یا شماره کاربر...">
        </div>

        <div class="flex flex-wrap md:flex-nowrap items-center gap-2 w-full md:w-auto">
            <select wire:model.live="dateFilter" class="bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs rounded-xl p-3 focus:ring-orange-500 outline-none w-full md:w-auto font-bold">
                <option value="all">📅 تمام تاریخ‌ها</option>
                <option value="today">امروز</option>
                <option value="yesterday">دیروز</option>
                <option value="this_week">این هفته</option>
                <option value="this_month">این ماه</option>
            </select>

            <select wire:model.live="statusFilter" class="bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs rounded-xl p-3 focus:ring-orange-500 outline-none w-full md:w-auto font-bold">
                <option value="">🔘 همه وضعیت‌ها</option>
                <option value="0">⏳ در انتظار تایید</option>
                <option value="1">✅ تایید شده</option>
                <option value="2">❌ رد شده</option>
            </select>

            <select wire:model.live="typeFilter" class="bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs rounded-xl p-3 focus:ring-orange-500 outline-none w-full md:w-auto font-bold">
                <option value="">💸 همه تراکنش‌ها</option>
                <option value="plus">افزایش موجودی (plus)</option>
                <option value="minus">کسر موجودی (minus)</option>
                <option value="plus_amn">شارژ امنیتی (plus_amn)</option>
                <option value="minus_amn">کسر امنیتی (minus_amn)</option>
            </select>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 text-sm text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            {{ session('error') }}
        </div>
    @endif

<!-- جدول تراکنش‌ها -->
    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden shadow-2xl pb-10">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="bg-zinc-950/80 border-b border-zinc-800/80 text-zinc-400 text-xs font-bold uppercase tracking-wider">
                    <th class="p-4">کد / تاریخ</th>
                    <th class="p-4">کاربر هدف (صاحب کیف پول)</th>
                    <th class="p-4">ایجاد کننده</th>
                    <th class="p-4 text-center">نوع تراکنش</th>
                    <th class="p-4 text-center">مبلغ (تومان)</th>
                    <th class="p-4">توضیحات</th>
                    <th class="p-4 text-center">وضعیت</th>
                    <th class="p-4 text-center">عملیات / فیش</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-sm">
                @forelse($transactions as $trx)
                    @php
                        $forUser = \App\Models\User::find($trx->for);
                        $creatorUser = \App\Models\User::find($trx->creator);
                        $isPlus = in_array($trx->type, ['plus', 'plus_amn']);
                    @endphp
                    <tr class="hover:bg-zinc-800/20 transition-colors">
                        <td class="p-4">
                            <span class="block font-bold text-white font-mono-digit">#TRX-{{ $trx->id }}</span>
                            <span class="text-[10px] text-zinc-500 font-mono-digit">{{ jdate($trx->created_at)->format('Y/m/d H:i') }}</span>
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-zinc-300 text-xs block">{{ $forUser?->name ?? 'کاربر حذف شده' }}</span>
                            <span class="text-[10px] text-zinc-500 font-mono" dir="ltr">{{ $forUser?->phone ?? '' }}</span>
                        </td>
                        <td class="p-4 text-xs font-bold text-zinc-400">
                            {{ $creatorUser?->name ?? 'مدیر کل / سیستم' }}
                        </td>
                        <td class="p-4 text-center">
                            @if($isPlus)
                                <span class="inline-flex px-2 py-1 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 rounded-md text-[10px] font-black">+ {{ $trx->type }}</span>
                            @else
                                <span class="inline-flex px-2 py-1 bg-rose-500/10 text-rose-500 border border-rose-500/20 rounded-md text-[10px] font-black">- {{ $trx->type }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-center font-mono-digit font-black {{ $isPlus ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ number_format($trx->price) }}
                        </td>
                        <td class="p-4 text-[11px] text-zinc-400 max-w-[150px] truncate" title="{{ $trx->description }}">
                            {{ $trx->description ?? '---' }}
                        </td>
                        <td class="p-4 text-center">
                            @if($trx->approved == 1)
                                <span class="text-emerald-500 text-xs font-bold flex items-center justify-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg> تایید شده</span>
                            @elseif($trx->approved == 0)
                                <span class="text-amber-500 text-xs font-bold flex items-center justify-center gap-1 animate-pulse">⏳ در انتظار</span>
                            @else
                                <span class="text-rose-500 text-xs font-bold flex items-center justify-center gap-1">❌ رد شده</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($trx->attachment)
                                    <button wire:click="viewReceipt({{ $trx->id }})" class="p-2 bg-zinc-800 hover:bg-zinc-700 text-orange-400 rounded-lg transition shadow-sm" title="بررسی فیش">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                            @endif

                            <!-- دکمه ویرایش کامل -->
                                <button wire:click="edit({{ $trx->id }})" class="p-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-orange-400 rounded-lg transition shadow-sm" title="ویرایش کامل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <!-- دکمه حذف -->
                                <button wire:click="delete({{ $trx->id }})" wire:confirm="آیا از حذف این تراکنش مطمئن هستید؟" class="p-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-rose-500 rounded-lg transition shadow-sm" title="حذف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-10 text-center text-zinc-500 font-medium">هیچ تراکنشی یافت نشد.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages()) <div class="p-4 border-t border-zinc-800/80">{{ $transactions->links() }}</div> @endif
    </div>

    <!-- 🔍 مودال بررسی سریع فیش واریزی -->
    @if($isReceiptModalOpen && $selectedTrx)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-5 border-b border-zinc-800 flex justify-between items-center bg-zinc-950">
                    <h3 class="text-sm font-black text-white">بررسی فیش واریزی</h3>
                    <button wire:click="$set('isReceiptModalOpen', false)" class="text-zinc-500 hover:text-rose-500 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div class="p-4 bg-[#09090b] flex justify-center">
                    <a href="{{ url('storage/'.$selectedTrx->attachment)  }}" target="_blank" title="مشاهده تصویر اصلی">
                        <img src="{{ url('storage/'.$selectedTrx->attachment)  }}" class="max-h-72 rounded-xl border border-zinc-800 object-contain hover:scale-105 transition duration-300">
                    </a>
                </div>

                <div class="p-6 space-y-4">
                    <div class="bg-zinc-800/40 p-4 rounded-xl border border-zinc-700/50 text-sm space-y-2">
                        <div class="flex justify-between"><span class="text-zinc-400 text-xs">مبلغ:</span><span class="font-black text-orange-400 font-mono-digit">{{ number_format($selectedTrx->price) }} تومان</span></div>
                        <div class="flex justify-between"><span class="text-zinc-400 text-xs">کاربر هدف:</span><span class="font-bold text-white">{{ \App\Models\User::find($selectedTrx->for)?->name ?? 'حذف شده' }}</span></div>
                    </div>

                    @if($selectedTrx->approved == 0)
                        <div class="flex gap-3 pt-2">
                            <button wire:click="rejectTransaction({{ $selectedTrx->id }})" wire:confirm="آیا از رد کردن این فیش مطمئن هستید؟" class="flex-1 py-3 rounded-xl bg-zinc-800 text-rose-500 hover:bg-rose-500 hover:text-white text-sm font-bold transition">رد فیش</button>
                            <button wire:click="approveTransaction({{ $selectedTrx->id }})" wire:confirm="مبلغ فوق به حساب کاربر افزوده خواهد شد. تایید می‌کنید؟" class="flex-1 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-black transition shadow-lg shadow-emerald-500/20">تایید و شارژ حساب</button>
                        </div>
                    @else
                        <div class="text-center p-3 rounded-xl text-xs font-bold {{ $selectedTrx->approved == 1 ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                            این تراکنش قبلاً {{ $selectedTrx->approved == 1 ? 'تایید' : 'رد' }} شده است.
                        </div>
                    @endif

                    <div class="pt-2 text-center">
                        <button wire:click="edit({{ $selectedTrx->id }}); $set('isReceiptModalOpen', false);" class="text-xs text-orange-400 hover:underline">ویرایش جزئیات این تراکنش (مبلغ، کاربر، ایجادکننده)</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

<!-- 📝 مودال ایجاد و ویرایش کامل تراکنش (CRUD) -->
    @if($isFormOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in overflow-y-auto">
            <div class="w-full max-w-lg bg-zinc-900 border border-zinc-800 rounded-3xl shadow-2xl overflow-hidden my-8">
                <div class="p-5 border-b border-zinc-800 flex justify-between items-center bg-zinc-950">
                    <h3 class="text-sm font-black text-white">{{ $trxId ? 'ویرایش کامل تراکنش #' . $trxId : 'ثبت تراکنش دستی جدید' }}</h3>
                    <button wire:click="resetForm" class="text-zinc-500 hover:text-rose-500 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4">

                    <!-- 🔍 کاربر هدف (صاحب کیف پول) با جستجوی زنده -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">کاربر هدف (صاحب کیف پول) <span class="text-rose-500">*</span></label>

                        @if($selectedForUserName)
                            <div class="flex items-center justify-between p-3 bg-zinc-950 border border-orange-500/50 rounded-xl">
                                <span class="text-sm font-bold text-orange-400">{{ $selectedForUserName }}</span>
                                <button type="button" wire:click="$set('for', null); $set('selectedForUserName', '')" class="text-xs text-rose-500 hover:underline">تغییر کاربر</button>
                            </div>
                        @else
                            <input wire:model.live.debounce.200ms="searchForUser" @focus="open = true" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none" placeholder="برای جستجو نام یا شماره موبایل را تایپ کنید...">
                        @endif

                        @if(!empty($searchedForUsers))
                            <div x-show="open" class="absolute z-50 right-0 left-0 mt-1 bg-zinc-900 border border-zinc-700 rounded-xl shadow-2xl max-h-48 overflow-y-auto divide-y divide-zinc-800">
                                @foreach($searchedForUsers as $u)
                                    <button type="button" wire:click="selectForUser({{ $u->id }}, '{{ $u->name }}', '{{ $u->phone }}')" @click="open = false" class="w-full text-right p-3 hover:bg-zinc-800 transition flex justify-between items-center text-xs">
                                        <span class="font-bold text-white">{{ $u->name }}</span>
                                        <span class="text-zinc-500 font-mono" dir="ltr">{{ $u->phone ?? $u->email }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @error('for') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- 🔍 ایجادکننده تراکنش با جستجوی زنده -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">ایجادکننده تراکنش <span class="text-rose-500">*</span></label>

                        @if($selectedCreatorUserName)
                            <div class="flex items-center justify-between p-3 bg-zinc-950 border border-orange-500/50 rounded-xl">
                                <span class="text-sm font-bold text-orange-400">{{ $selectedCreatorUserName }}</span>
                                <button type="button" wire:click="$set('creator', null); $set('selectedCreatorUserName', '')" class="text-xs text-rose-500 hover:underline">تغییر ایجادکننده</button>
                            </div>
                        @else
                            <input wire:model.live.debounce.200ms="searchCreatorUser" @focus="open = true" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none" placeholder="جستجوی نماینده یا مدیر...">
                        @endif

                        @if(!empty($searchedCreatorUsers))
                            <div x-show="open" class="absolute z-50 right-0 left-0 mt-1 bg-zinc-900 border border-zinc-700 rounded-xl shadow-2xl max-h-48 overflow-y-auto divide-y divide-zinc-800">
                                @foreach($searchedCreatorUsers as $u)
                                    <button type="button" wire:click="selectCreatorUser({{ $u->id }}, '{{ $u->name }}', '{{ $u->role }}')" @click="open = false" class="w-full text-right p-3 hover:bg-zinc-800 transition flex justify-between items-center text-xs">
                                        <span class="font-bold text-white">{{ $u->name }}</span>
                                        <span class="text-orange-400 text-[10px]">({{ $u->role }})</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @error('creator') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- مبلغ -->
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">مبلغ (تومان) <span class="text-rose-500">*</span></label>
                            <input wire:model="price" type="number" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none font-mono-digit" placeholder="100000">
                            @error('price') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- نوع تراکنش -->
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">نوع تراکنش <span class="text-rose-500">*</span></label>
                            <select wire:model="type" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none font-bold">
                                <option value="plus">➕ افزایش موجودی (plus)</option>
                                <option value="minus">➖ کسر موجودی (minus)</option>
                                <option value="plus_amn">🛡️ شارژ امنیتی (plus_amn)</option>
                                <option value="minus_amn">🛡️ کسر امنیتی (minus_amn)</option>
                            </select>
                        </div>
                    </div>

                    <!-- وضعیت تایید -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">وضعیت تایید <span class="text-rose-500">*</span></label>
                        <select wire:model="approved" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none font-bold">
                            <option value="1">✅ تایید شده (اعمال در موجودی)</option>
                            <option value="0">⏳ در انتظار بررسی</option>
                            <option value="2">❌ رد شده</option>
                        </select>
                    </div>

                    <!-- توضیحات -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">توضیحات تراکنش</label>
                        <input wire:model="description" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3 text-sm focus:ring-orange-500 outline-none" placeholder="علت واریز یا کسر...">
                    </div>

                    <!-- تصویر فیش / فایل ضمائم -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">تصویر فیش واریزی / ضمیمه</label>
                        <input wire:model="attachment" type="file" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-400 rounded-xl p-2 text-xs focus:ring-orange-500 outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-zinc-800 file:text-white cursor-pointer">
                        @if($existingAttachment)
                            <p class="text-[10px] text-emerald-400 mt-1">یک ضمیمه قبلاً آپلود شده است. (انتخاب فایل جدید آن را جایگزین می‌کند)</p>
                        @endif
                        @error('attachment') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-zinc-800">
                        <button type="button" wire:click="resetForm" class="flex-1 py-3 bg-zinc-800 hover:bg-zinc-700 text-white text-sm font-bold rounded-xl transition">لغو</button>
                        <button type="submit" class="flex-1 py-3 bg-orange-600 hover:bg-orange-500 text-white text-sm font-black rounded-xl transition shadow-lg shadow-orange-500/20">
                            {{ $trxId ? 'ثبت تغییرات تراکنش' : 'ایجاد تراکنش' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
