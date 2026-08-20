<div class="space-y-8 pb-12">

    @if($announcements->count() > 0)
        <div class="space-y-3 mb-6">
            @foreach($announcements as $ann)
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-2xl p-4 flex items-start gap-4 relative overflow-hidden shadow-sm">
                    <div class="absolute top-0 right-0 w-1 h-full bg-blue-500"></div>
                    <div class="w-10 h-10 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-blue-400">{{ $ann->title }}</h4>
                        <p class="text-xs text-blue-300 mt-1 leading-relaxed">{{ $ann->content }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black tracking-tight">خوش آمدید، {{ auth()->user()->name ?? 'همکار گرامی' }} 👋</h2>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">گزارش لحظه‌ای وضعیت فروش و سرویس‌های فعال شما</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{route('reseller.accounts.create')}}"  wire:navigate  class="px-5 py-3 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm shadow-lg shadow-orange-500/25 transition-all hover:-translate-y-0.5">
                + صدور اکانت جدید
            </a>
        </div>
    </div>

    @if($this->balance < 50000)
        <div class="p-5 rounded-2xl bg-gradient-to-r from-rose-500/10 to-orange-500/10 border border-rose-500/20 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-500/20 text-rose-500 flex items-center justify-center font-bold">!</div>
                <div>
                    <h4 class="font-bold text-rose-500 text-base">موجودی کیف پول رو به اتمام است</h4>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">موجودی فعلی شما کمتر از حد مجاز است. لطفاً حساب خود را شارژ کنید.</p>
                </div>
            </div>
            <button class="px-4 py-2.5 rounded-xl bg-rose-500 text-white font-bold text-xs shadow-md shadow-rose-500/20">
                شارژ کیف پول
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="p-6 rounded-3xl bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">موجودی کیف پول</span>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-black font-mono-digit {{ $this->balance >= 0 ? 'text-zinc-900 dark:text-white' : 'text-rose-500' }}">
                    {{ number_format($this->balance) }}
                </span>
                <span class="text-xs font-medium text-zinc-400">تومان</span>
            </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 shadow-sm flex flex-col justify-between relative overflow-hidden">
            <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">میزان بدهی به سیستم</span>

            <div class="mt-4 flex items-baseline gap-1.5 relative z-10">
        <span class="text-3xl font-black font-mono-digit {{ auth()->user()->debt_balance > 0 ? 'text-rose-500' : 'text-zinc-900 dark:text-white' }}">
            {{ number_format(auth()->user()->debt_balance) }}
        </span>
                <span class="text-xs font-medium text-zinc-400">تومان</span>
            </div>

            @if(auth()->user()->debt_balance > 0)
                <div class="absolute -left-10 -bottom-10 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>
            @endif
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">تعداد مشتریان اختصاصی</span>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-black font-mono-digit text-zinc-900 dark:text-white">{{ $totalCustomers }}</span>
                <span class="text-xs font-medium text-zinc-400">نفر</span>
            </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">اکانت‌های فعال</span>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-black font-mono-digit text-orange-500">{{ $activeAccounts }}</span>
                <span class="text-xs font-medium text-zinc-400">از {{ $totalAccounts }} کل</span>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 shadow-sm">
            <h3 class="text-base font-bold text-zinc-900 dark:text-white mb-4">اکانت‌های رو به انقضا (۷ روز آینده)</h3>

            <div class="space-y-3">
                @forelse($expiringAccounts as $acc)
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800/50">
                        <div>
                            <span class="font-mono-digit font-bold text-sm text-zinc-800 dark:text-zinc-200" dir="ltr">{{ $acc->username }}</span>
                            <span class="block text-xs text-rose-500 mt-1 font-medium">
                                انقضا: {{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($acc->expire_date))->format('%Y/%m/%d') }}
                            </span>
                        </div>
                        <a href="{{route('reseller.accounts.show',['id' => $acc->id ])}}" wire:navigate class="px-4 py-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-bold hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-all">
                            تمدید سرویس
                        </a>
                    </div>
                @empty
                    <div class="py-12 text-center text-zinc-400 text-sm">هیچ اکانتی در آستانه انقضا نیست.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 shadow-sm">
            <h3 class="text-base font-bold text-zinc-900 dark:text-white mb-4">آخرین تراکنش‌های مالی</h3>

            <div class="space-y-3">
                @forelse($recentTransactions as $tx)
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800/50">
                        <div>
                            <span class="text-sm font-bold text-zinc-800 dark:text-zinc-200">
                                {{ in_array($tx->type, ['plus', 'plus_amn']) ? 'شارژ حساب (واریز)' : 'خرید یا تمدید سرویس' }}
                            </span>
                            <span class="block text-xs text-zinc-400 mt-0.5 font-mono-digit">
                                {{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($tx->created_at))->format('%Y/%m/%d - H:i') }}
                            </span>
                        </div>
                        <span class="font-mono-digit font-black text-sm {{ in_array($tx->type, ['plus', 'plus_amn']) ? 'text-emerald-500' : 'text-zinc-700 dark:text-zinc-300' }}" dir="ltr">
                            {{ in_array($tx->type, ['plus', 'plus_amn']) ? '+' : '-' }}{{ number_format($tx->price) }} T
                        </span>
                    </div>
                @empty
                    <div class="py-12 text-center text-zinc-400 text-sm">تراکنشی ثبت نشده است.</div>
                @endforelse
            </div>
        </div>


        <div class="lg:col-span-2 space-y-8">
        <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 shadow-sm ">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        تعرفه سرویس‌های قابل فروش
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        @if($discountPercent > 0)
                            قیمت‌های زیر با احتساب <span class="text-orange-500 font-bold font-mono-digit">{{ $discountPercent }}%</span> تخفیف اختصاصی شما محاسبه شده‌اند.
                        @else
                            لیست سرویس‌های فعال و مجاز برای خرید و صدور اکانت.
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($availableGroups as $group)
                    @php
                        $basePrice = $group->price_reseler ?? 0;

                        $finalPrice = $basePrice;
                        if ($discountPercent > 0) {
                            $finalPrice = $basePrice - ($basePrice * $discountPercent / 100);
                        }
                    @endphp
                    <div class="p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800/50 hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all flex flex-col justify-between h-full">

                        <div class="flex items-start justify-between mb-4">
                            <span class="font-bold text-sm text-zinc-800 dark:text-zinc-200 leading-tight">{{ $group->name }}</span>
                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold rounded-lg whitespace-nowrap">مجاز</span>
                        </div>

                        <div class="mt-auto pt-4 border-t border-zinc-200 dark:border-zinc-800/80">
                            @if($discountPercent > 0)
                                <div class="text-[11px] text-zinc-400 dark:text-zinc-500 line-through font-mono-digit mb-0.5">
                                    {{ number_format($basePrice) }} تومان
                                </div>
                            @else
                                <div class="text-[10px] text-zinc-400 mb-0.5">قیمت نهایی:</div>
                            @endif

                            <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono-digit">
                                {{ number_format(round($group->getFinalPriceFor(auth()->user()))) }} <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">تومان</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 flex flex-col items-center justify-center bg-zinc-50 dark:bg-zinc-900/30 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800">
                        <svg class="w-10 h-10 text-zinc-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm text-zinc-500 font-medium">هیچ سرویس فعالی برای شما تعریف نشده است.</span>
                    </div>
                @endforelse
            </div>
        </div>
        </div>

    </div>

</div>
