<div class="space-y-6 md:space-y-8 font-sans pb-12 animate-fade-in">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">


        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 shadow-lg relative overflow-hidden flex items-center justify-between">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-zinc-400 mb-1 uppercase tracking-wider">کاربران آنلاین (اکنون)</p>
                <p class="text-3xl font-black text-white font-mono">{{ number_format($onlineUsersCount) }} <span class="text-xs font-medium text-zinc-500">نفر</span></p>
            </div>
            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center relative z-10">
                <span class="absolute top-2 right-2 w-2 h-2 bg-emerald-400 rounded-full animate-ping"></span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 shadow-lg flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-zinc-400 mb-1 uppercase tracking-wider">درآمد امروز</p>
                <p class="text-2xl font-black text-emerald-400 font-mono">+{{ number_format($todayRevenue) }} <span class="text-[10px] font-sans text-zinc-500">تومان</span></p>
            </div>
            <div class="w-12 h-12 bg-zinc-800 text-zinc-400 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 shadow-lg flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-zinc-400 mb-1 uppercase tracking-wider">درآمد این ماه</p>
                <p class="text-2xl font-black text-white font-mono">{{ number_format($thisMonthRevenue) }} <span class="text-[10px] font-sans text-zinc-500">تومان</span></p>
            </div>
            <div class="w-12 h-12 bg-orange-500/10 text-orange-500 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
        </div>

        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 shadow-lg flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-zinc-400 mb-1 uppercase tracking-wider">درآمد ماه قبل</p>
                <p class="text-2xl font-black text-zinc-300 font-mono">{{ number_format($lastMonthRevenue) }} <span class="text-[10px] font-sans text-zinc-500">تومان</span></p>

                @php
                    $growth = $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 100;
                @endphp
                <p class="text-[10px] font-bold mt-1 flex items-center gap-1 {{ $growth >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $growth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}"></path>
                    </svg>
                    {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}% نسبت به ماه قبل
                </p>
            </div>
        </div>

    </div>

    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 shadow-xl">
        <div class="flex items-center justify-between mb-4 border-b border-zinc-800/80 pb-3">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                دسترسی و میانبرهای سریع
            </h3>
            <span class="text-[10px] text-zinc-500 font-mono">Quick Navigation</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

            <a href="{{ route('admin.accounts.create') ?? '#' }}" wire:navigate
               class="p-3.5 bg-zinc-950/60 hover:bg-orange-500/10 border border-zinc-800/80 hover:border-orange-500/30 rounded-2xl transition-all group flex flex-col items-center text-center gap-2.5">
                <div class="w-10 h-10 bg-orange-500/10 group-hover:bg-orange-500 text-orange-500 group-hover:text-white rounded-xl flex items-center justify-center transition-colors shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-xs font-bold text-zinc-300 group-hover:text-orange-400 transition-colors">ایجاد اکانت جدید</span>
            </a>

            <a href="{{ route('admin.accounts.list') ?? '#' }}" wire:navigate
               class="p-3.5 bg-zinc-950/60 hover:bg-blue-500/10 border border-zinc-800/80 hover:border-blue-500/30 rounded-2xl transition-all group flex flex-col items-center text-center gap-2.5">
                <div class="w-10 h-10 bg-blue-500/10 group-hover:bg-blue-500 text-blue-500 group-hover:text-white rounded-xl flex items-center justify-center transition-colors shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <span class="text-xs font-bold text-zinc-300 group-hover:text-blue-400 transition-colors">لیست اکانت ها</span>
            </a>

            <a href="{{ route('admin.managers.list') ?? '#' }}" wire:navigate
               class="p-3.5 bg-zinc-950/60 hover:bg-purple-500/10 border border-zinc-800/80 hover:border-purple-500/30 rounded-2xl transition-all group flex flex-col items-center text-center gap-2.5">
                <div class="w-10 h-10 bg-purple-500/10 group-hover:bg-purple-500 text-purple-500 group-hover:text-white rounded-xl flex items-center justify-center transition-colors shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-zinc-300 group-hover:text-purple-400 transition-colors">مدیریت نمایندگان</span>
            </a>

            <a href="{{ route('admin.announcements') ?? '#' }}" wire:navigate
               class="p-3.5 bg-zinc-950/60 hover:bg-amber-500/10 border border-zinc-800/80 hover:border-amber-500/30 rounded-2xl transition-all group flex flex-col items-center text-center gap-2.5">
                <div class="w-10 h-10 bg-amber-500/10 group-hover:bg-amber-500 text-amber-500 group-hover:text-white rounded-xl flex items-center justify-center transition-colors shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                </div>
                <span class="text-xs font-bold text-zinc-300 group-hover:text-amber-400 transition-colors">ثبت اطلاعیه جدید</span>
            </a>

            <a href="{{ route('admin.financial') ?? '#' }}" wire:navigate
               class="p-3.5 bg-zinc-950/60 hover:bg-emerald-500/10 border border-zinc-800/80 hover:border-emerald-500/30 rounded-2xl transition-all group flex flex-col items-center text-center gap-2.5">
                <div class="w-10 h-10 bg-emerald-500/10 group-hover:bg-emerald-500 text-emerald-500 group-hover:text-white rounded-xl flex items-center justify-center transition-colors shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <span class="text-xs font-bold text-zinc-300 group-hover:text-emerald-400 transition-colors">لیست تراکنش‌ها</span>
            </a>

            <a href="{{ route('admin.accounts.logs') ?? '#' }}" wire:navigate
               class="p-3.5 bg-zinc-950/60 hover:bg-rose-500/10 border border-zinc-800/80 hover:border-rose-500/30 rounded-2xl transition-all group flex flex-col items-center text-center gap-2.5">
                <div class="w-10 h-10 bg-rose-500/10 group-hover:bg-rose-500 text-rose-500 group-hover:text-white rounded-xl flex items-center justify-center transition-colors shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-zinc-300 group-hover:text-rose-400 transition-colors">لیست رخدادها</span>
            </a>

        </div>
    </div>


    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 md:p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-sm font-bold text-white">نمودار ساخت اکانت جدید (۶ ماه گذشته)</h3>
                <p class="text-xs text-zinc-500 mt-1">تعداد اکانت‌های صادر شده توسط سیستم و نمایندگان به تفکیک ماه</p>
            </div>
            <div class="hidden md:block">
                <span class="px-3 py-1 bg-zinc-800 text-zinc-300 text-xs font-bold rounded-lg border border-zinc-700">کل اکانت‌های فعال: <span class="text-orange-500">{{ number_format($totalActiveAccounts) }}</span></span>
            </div>
        </div>

        <div class="w-full h-72 relative"
             x-data="{
                initChart() {
                    const ctx = this.$refs.canvas.getContext('2d');

                    // ساخت گرادینت زیر نمودار
                    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(249, 115, 22, 0.3)'); // orange-500
                    gradient.addColorStop(1, 'rgba(249, 115, 22, 0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @js($chartLabels),
                            datasets: [{
                                label: 'اکانت‌های ساخته شده',
                                data: @js($chartData),
                                borderColor: '#f97316',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                pointBackgroundColor: '#f97316',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                fill: true,
                                tension: 0.4 // انحنای خط
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                                    ticks: { color: '#71717a', font: { family: 'IRANSansX' } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#a1a1aa', font: { family: 'IRANSansX', size: 11 } }
                                }
                            }
                        }
                    });
                }
             }" x-init="initChart()">
            <canvas x-ref="canvas"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">

        <div class="lg:col-span-1 space-y-6 md:space-y-8">

            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4 border-b border-zinc-800/80 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        جدیدترین اعضای سیستم
                    </h3>
                </div>
                <div class="space-y-4">
                    @forelse($latestUsers as $user)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name ?? 'U') }}&background=27272a&color=f97316" class="w-10 h-10 rounded-full border border-zinc-700">
                                <div>
                                    <p class="text-xs font-bold text-white">{{ $user->name ?: $user->username }}</p>
                                    <p class="text-[10px] text-zinc-500">{{ $user->role === 'agent' ? 'نماینده فروش' : ($user->role === 'sub_agent' ? 'زیرنماینده' : 'مشتری') }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-zinc-500 font-mono">{{ \Morilog\Jalali\Jalalian::fromCarbon($user->created_at)->ago() }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500 text-center py-4">کاربری یافت نشد.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4 border-b border-zinc-800/80 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        تراکنش‌های در انتظار بررسی
                    </h3>
                </div>
                <div class="space-y-3">
                    @forelse($pendingTransactions as $trx)
                        <div class="bg-zinc-950/50 p-3 rounded-xl border border-zinc-800 flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-zinc-300">{{ $trx->description ?? 'درخواست واریز' }}</p>
                                <p class="text-[10px] text-zinc-500 mt-1">توسط کاربر #{{ $trx->creator }}</p>
                            </div>
                            <div class="text-left">
                                <p class="text-xs font-black text-amber-500 font-mono">{{ number_format($trx->price) }} <span class="text-[9px] font-sans">تومان</span></p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <span class="inline-block px-3 py-1 bg-emerald-500/10 text-emerald-400 text-xs font-bold rounded-lg">همه تراکنش‌ها بررسی شده‌اند ✅</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <div class="lg:col-span-2">
            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 md:p-6 shadow-xl h-full">
                <div class="flex items-center justify-between mb-6 border-b border-zinc-800/80 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        آخرین رخدادها و لاگ‌های سیستم
                    </h3>
                    <a href="#" class="text-xs text-orange-500 hover:text-orange-400 font-bold transition">مشاهده همه لاگ‌ها &larr;</a>
                </div>

                <div class="space-y-4 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-zinc-800 before:to-transparent">
                    <div class="space-y-4 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-zinc-800 before:to-transparent">
                        @forelse($latestEvents as $event)
                            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">

                                <div class="flex items-center justify-center w-10 h-10 rounded-full border border-zinc-800 bg-zinc-900 text-zinc-500 group-hover:text-orange-500 group-hover:border-orange-500/50 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 transition-colors z-10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>

                                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-2xl bg-zinc-950/60 border border-zinc-800/80 shadow-sm group-hover:border-zinc-700 transition-colors">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-xs text-zinc-300">{{ $event->content ?? 'عملیات سیستمی' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                    <span class="text-[10px] px-2 py-0.5 bg-zinc-800 text-zinc-400 rounded-md">
                        کاربر: {{ $event->user_id ?? 'سیستم' }}
                        @if($event->by) <span class="text-zinc-500">({{ $event->by }})</span> @endif
                    </span>
                                        <span class="text-[10px] text-zinc-500 font-mono">
                        {{ $event->created_at ? jdate($event->created_at)->format('H:i - Y/m/d') : 'نامشخص' }}
                    </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-zinc-500 text-center py-8">رخدادی در سیستم ثبت نشده است.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
