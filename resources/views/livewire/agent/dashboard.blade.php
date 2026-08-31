<div class="space-y-6 pb-12">
    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- ============================================ --}}
    {{-- 1. WELCOME HEADER + QUICK ACTIONS            --}}
    {{-- ============================================ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#111722] rounded-2xl p-6 border border-[#202938] shadow-sm">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#F8FAFC] tracking-tight">
                خوش آمدید، {{ auth()->user()->name ?? 'همکار گرامی' }} 👋
            </h2>
            <p class="text-[#94A3B8] text-sm mt-1">
                نمای کلی عملکرد فروشگاه و حساب نمایندگی شما
            </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            @if($this->balance < 50000)
                <a href="{{ route('reseller.financial') }}" wire:navigate class="px-5 py-3 rounded-xl bg-[#EF4444] hover:bg-[#DC2626] text-white font-bold text-sm shadow-lg shadow-[#EF4444]/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    شارژ کیف پول
                </a>
            @endif
            <a href="{{ route('reseller.accounts.create') }}" wire:navigate class="px-5 py-3 rounded-xl bg-[#6366F1] hover:bg-[#4F46E5] text-white font-bold text-sm shadow-lg shadow-[#6366F1]/25 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                صدور اکانت جدید
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 2. CRITICAL ALERTS                           --}}
    {{-- ============================================ --}}
    <div class="space-y-3">
        @if($this->balance < 50000)
            <div class="p-5 rounded-2xl bg-[#EF4444]/5 border border-[#EF4444]/20 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-[#EF4444]/10 text-[#EF4444] border border-[#EF4444]/20 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#EF4444] text-base">موجودی کیف پول رو به اتمام است</h4>
                        <p class="text-xs text-[#94A3B8] mt-0.5">موجودی فعلی شما کمتر از حد تعیین‌شده است. لطفاً حساب خود را شارژ کنید.</p>
                    </div>
                </div>
                <a href="{{ route('reseller.financial') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-[#EF4444] hover:bg-[#DC2626] text-white font-bold text-xs shadow-md shadow-[#EF4444]/20 transition-all">
                    شارژ کیف پول
                </a>
            </div>
        @endif

        @if(auth()->user()->debt_balance > 0)
            <div class="p-5 rounded-2xl bg-[#F59E0B]/5 border border-[#F59E0B]/20 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#F59E0B] text-base">بدهی به سیستم</h4>
                        <p class="text-xs text-[#94A3B8] mt-0.5">مبلغ بدهی فعلی: <span class="text-[#F8FAFC] font-bold">{{ number_format(auth()->user()->debt_balance) }}</span> تومان</p>
                    </div>
                </div>
                <a href="{{ route('reseller.financial') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-[#F59E0B] hover:bg-[#D97706] text-white font-bold text-xs shadow-md shadow-[#F59E0B]/20 transition-all">
                    مشاهده امور مالی
                </a>
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- 3. BUSINESS KPI CARDS                        --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Balance --}}
        <div class="p-5 rounded-2xl bg-[#111722] border border-[#202938] shadow-sm hover:border-[#6366F1]/30 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">موجودی کیف پول</span>
                <div class="w-8 h-8 rounded-lg bg-[#6366F1]/10 text-[#6366F1] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl font-bold font-mono text-[#F8FAFC] {{ $this->balance >= 0 ? '' : 'text-[#EF4444]' }}">
                    {{ number_format($this->balance) }}
                </span>
                <span class="text-xs font-medium text-[#94A3B8]">تومان</span>
            </div>
            <div class="mt-2 pt-3 border-t border-[#202938]/50">
                <a href="{{ route('reseller.financial') }}" wire:navigate class="text-[11px] text-[#6366F1] hover:text-[#4F46E5] transition-colors font-bold">مشاهده کیف پول &larr;</a>
            </div>
        </div>

        {{-- Debt --}}
        <div class="p-5 rounded-2xl bg-[#111722] border border-[#202938] shadow-sm hover:border-[#202938]/80 transition-all group relative overflow-hidden">
            @if(auth()->user()->debt_balance > 0)
                <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-[#EF4444]/10 rounded-full blur-2xl pointer-events-none"></div>
            @endif
            <div class="flex items-center justify-between mb-1 relative z-10">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">بدهی به سیستم</span>
                <div class="w-8 h-8 rounded-lg {{ auth()->user()->debt_balance > 0 ? 'bg-[#EF4444]/10 text-[#EF4444]' : 'bg-[#10B981]/10 text-[#10B981]' }} flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5 relative z-10">
                <span class="text-2xl font-bold font-mono {{ auth()->user()->debt_balance > 0 ? 'text-[#EF4444]' : 'text-[#10B981]' }}">
                    {{ number_format(auth()->user()->debt_balance) }}
                </span>
                <span class="text-xs font-medium text-[#94A3B8]">تومان</span>
            </div>
            <div class="mt-2 pt-3 border-t border-[#202938]/50 relative z-10">
                <span class="text-[11px] font-bold text-[#94A3B8]">
                    {{ auth()->user()->debt_balance > 0 ? 'وضعیت بدهکار' : 'وضعیت تسویه' }}
                </span>
            </div>
        </div>

        {{-- Customers --}}
        <div class="p-5 rounded-2xl bg-[#111722] border border-[#202938] shadow-sm hover:border-[#202938]/80 transition-all">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">مشتریان</span>
                <div class="w-8 h-8 rounded-lg bg-[#3B82F6]/10 text-[#3B82F6] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl font-bold font-mono text-[#F8FAFC]">{{ $totalCustomers }}</span>
                <span class="text-xs font-medium text-[#94A3B8]">نفر</span>
            </div>
            <div class="mt-2 pt-3 border-t border-[#202938]/50">
                <span class="text-[11px] font-bold text-[#94A3B8]">مشتری فعال شما</span>
            </div>
        </div>

        {{-- Active Accounts --}}
        <div class="p-5 rounded-2xl bg-[#111722] border border-[#202938] shadow-sm hover:border-[#10B981]/30 transition-all">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">اکانت‌های فعال</span>
                <div class="w-8 h-8 rounded-lg bg-[#10B981]/10 text-[#10B981] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl font-bold font-mono text-[#10B981]">{{ $activeAccounts }}</span>
                <span class="text-xs font-medium text-[#94A3B8]">از {{ $totalAccounts }} اکانت</span>
            </div>
            <div class="mt-2 pt-3 border-t border-[#202938]/50">
                <span class="text-[11px] font-bold text-[#94A3B8]">سرویس‌های در حال استفاده</span>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 4. PERFORMANCE OVERVIEW + QUICK ACTIONS      --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Chart: Performance Overview --}}
        {{-- Chart: Performance Overview --}}
        <div class="lg:col-span-2 bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm flex flex-col h-full relative overflow-hidden">
            <!-- یک هاله نور بسیار ملایم پس‌زمینه -->
            <div class="absolute top-0 right-1/2 w-64 h-64 bg-[#6366F1]/5 rounded-full blur-[80px] pointer-events-none"></div>

            <div class="flex items-center justify-between mb-6 relative z-10">
                <h3 class="text-base font-bold text-[#F8FAFC]">مقایسه عملکرد فروش</h3>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-lg bg-[#6366F1]/10 text-[#6366F1] border border-[#6366F1]/20 text-[11px] font-bold">۳۰ روز اخیر</span>
                </div>
            </div>
            <div class="flex-1 w-full h-[240px] relative z-10"
                 x-data="{
                    initChart() {
                        const ctx = this.$refs.canvas.getContext('2d');

                        // گرادیانت خط این ماه (رنگ نیلی اصلی)
                        const gradientCurrent = ctx.createLinearGradient(0, 0, 0, 300);
                        gradientCurrent.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
                        gradientCurrent.addColorStop(1, 'rgba(99, 102, 241, 0)');

                        // گرادیانت خط ماه قبل (رنگ خاکستری ملایم)
                        const gradientPrevious = ctx.createLinearGradient(0, 0, 0, 300);
                        gradientPrevious.addColorStop(0, 'rgba(148, 163, 184, 0.1)');
                        gradientPrevious.addColorStop(1, 'rgba(148, 163, 184, 0)');

                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @js($chartLabels),
                                datasets: [
                                    {
                                        label: '۳۰ روز اخیر',
                                        data: @js($currentMonthSales),
                                        borderColor: '#6366F1',
                                        backgroundColor: gradientCurrent,
                                        borderWidth: 2,
                                        pointBackgroundColor: '#111722',
                                        pointBorderColor: '#6366F1',
                                        pointBorderWidth: 2,
                                        pointRadius: 0, // مخفی بودن نقاط در حالت عادی برای تمیزی بیشتر
                                        pointHoverRadius: 5,
                                        fill: true,
                                        tension: 0.4 // انحنای زیبای لاین
                                    },
                                    {
                                        label: '۳۰ روز قبل‌تر',
                                        data: @js($previousMonthSales),
                                        borderColor: '#475569',
                                        backgroundColor: gradientPrevious,
                                        borderWidth: 2,
                                        borderDash: [5, 5], // خط‌چین بودن ماه قبل برای تمایز
                                        pointBackgroundColor: '#111722',
                                        pointBorderColor: '#475569',
                                        pointBorderWidth: 2,
                                        pointRadius: 0,
                                        pointHoverRadius: 4,
                                        fill: true,
                                        tension: 0.4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        align: 'end',
                                        labels: {
                                            color: '#94A3B8',
                                            font: { family: 'Vazirmatn', size: 11 },
                                            usePointStyle: true,
                                            boxWidth: 8
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: '#171E2B',
                                        titleColor: '#F8FAFC',
                                        bodyColor: '#94A3B8',
                                        borderColor: '#202938',
                                        borderWidth: 1,
                                        titleFont: { family: 'Vazirmatn' },
                                        bodyFont: { family: 'Vazirmatn' },
                                        padding: 12,
                                        boxPadding: 6,
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) { label += ': '; }
                                                if (context.parsed.y !== null) {
                                                    // فرمت کردن عدد به شکل سه‌رقم سه‌رقم
                                                    label += new Intl.NumberFormat('fa-IR').format(context.parsed.y) + ' تومان';
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { color: 'rgba(32, 41, 56, 0.4)', drawBorder: false },
                                        ticks: {
                                            color: '#64748B',
                                            font: { family: 'Vazirmatn', size: 10 },
                                            maxTicksLimit: 8 // جلوگیری از شلوغی محور X
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: 'rgba(32, 41, 56, 0.4)', drawBorder: false },
                                        ticks: {
                                            color: '#64748B',
                                            font: { family: 'JetBrains Mono', size: 10 },
                                            callback: function(value) {
                                                if(value === 0) return '0';
                                                return (value / 1000) + 'k'; // نمایش خواناتر (مثلاً 50k به جای 50000)
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                 }"
                 x-init="initChart()">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm flex flex-col h-full">
            <h3 class="text-base font-bold text-[#F8FAFC] mb-5">عملیات سریع</h3>
            <div class="grid grid-cols-2 gap-3 flex-1">
                <a href="{{ route('reseller.accounts.create') }}" wire:navigate class="p-4 rounded-xl bg-[#171E2B] border border-[#202938] hover:border-[#6366F1]/40 transition-all text-center group flex flex-col justify-center">
                    <div class="w-10 h-10 rounded-xl bg-[#6366F1]/10 text-[#6366F1] flex items-center justify-center mx-auto mb-2.5 group-hover:bg-[#6366F1] group-hover:text-white transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-[#F8FAFC]">صدور اکانت</span>
                </a>
                <a href="{{ route('reseller.customers') }}" wire:navigate class="p-4 rounded-xl bg-[#171E2B] border border-[#202938] hover:border-[#10B981]/40 transition-all text-center group flex flex-col justify-center">
                    <div class="w-10 h-10 rounded-xl bg-[#10B981]/10 text-[#10B981] flex items-center justify-center mx-auto mb-2.5 group-hover:bg-[#10B981] group-hover:text-white transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m-3-3v3m6-3v3m-9 3H4m10-8a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-[#F8FAFC]">مشتریان</span>
                </a>
                <a href="{{ route('reseller.financial') }}" wire:navigate class="p-4 rounded-xl bg-[#171E2B] border border-[#202938] hover:border-[#F59E0B]/40 transition-all text-center group flex flex-col justify-center">
                    <div class="w-10 h-10 rounded-xl bg-[#F59E0B]/10 text-[#F59E0B] flex items-center justify-center mx-auto mb-2.5 group-hover:bg-[#F59E0B] group-hover:text-white transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-[#F8FAFC]">شارژ کیف پول</span>
                </a>
                <a href="{{ route('reseller.store.orders') }}" wire:navigate class="p-4 rounded-xl bg-[#171E2B] border border-[#202938] hover:border-[#3B82F6]/40 transition-all text-center group flex flex-col justify-center">
                    <div class="w-10 h-10 rounded-xl bg-[#3B82F6]/10 text-[#3B82F6] flex items-center justify-center mx-auto mb-2.5 group-hover:bg-[#3B82F6] group-hover:text-white transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-[#F8FAFC]">سفارشات</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 5. EXPIRING ACCOUNTS + RECENT TRANSACTIONS   --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Expiring Accounts --}}
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm flex flex-col max-h-[450px]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-[#F8FAFC]">اکانت‌های رو به انقضا</h3>
                <span class="text-[11px] font-bold text-[#94A3B8] bg-[#171E2B] px-3 py-1.5 rounded-lg border border-[#202938]">۷ روز آینده</span>
            </div>

            <div class="space-y-3 overflow-y-auto pr-1">
                @forelse($expiringAccounts as $acc)
                    @php
                        $daysLeft = \Carbon\Carbon::parse($acc->expire_date)->diffInDays(now());
                        $statusColor = $daysLeft > 7 ? '#10B981' : ($daysLeft > 3 ? '#F59E0B' : '#EF4444');
                    @endphp
                    <div class="flex items-center justify-between p-4 rounded-xl bg-[#171E2B] border border-[#202938] hover:border-[#6366F1]/30 transition-all">
                        <div class="flex items-center gap-3.5">
                            <div class="w-2.5 h-2.5 rounded-full shadow-sm" style="background-color: {{ $statusColor }}; box-shadow: 0 0 8px {{ $statusColor }}80;"></div>
                            <div>
                                <span class="font-mono font-bold text-sm text-[#F8FAFC]" dir="ltr">{{ $acc->username }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[11px] font-mono text-[#94A3B8]">
                                        {{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($acc->expire_date))->format('%Y/%m/%d') }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background-color: {{ $statusColor }}15; color: {{ $statusColor }};">
                                        {{ $daysLeft }} روز تا انقضا
                                    </span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('reseller.accounts.show', ['id' => $acc->id]) }}" wire:navigate class="px-4 py-2 rounded-xl bg-[#202938] hover:bg-[#6366F1] border border-[#202938] text-[#94A3B8] hover:text-white text-[11px] font-bold transition-all shadow-sm">
                            تمدید سرویس
                        </a>
                    </div>
                @empty
                    <div class="py-16 text-center flex flex-col items-center">
                        <div class="w-14 h-14 rounded-full bg-[#10B981]/10 text-[#10B981] flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#F8FAFC]">نیازی به تمدید نیست</h4>
                        <p class="text-[11px] text-[#94A3B8] mt-1.5 font-medium">در حال حاضر هیچ اکانتی در ۷ روز آینده منقضی نمی‌شود.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm flex flex-col max-h-[450px]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-[#F8FAFC]">آخرین تراکنش‌های مالی</h3>
                <span class="text-[11px] font-bold text-[#94A3B8] bg-[#171E2B] px-3 py-1.5 rounded-lg border border-[#202938]">۵ تراکنش اخیر</span>
            </div>

            <div class="space-y-3 overflow-y-auto pr-1">
                @forelse($recentTransactions as $tx)
                    @php
                        $isCredit = in_array($tx->type, ['plus', 'plus_amn']);
                        $txColor = $isCredit ? '#10B981' : '#94A3B8';
                        $txIcon = $isCredit ? '+' : '-';
                    @endphp
                    <div class="flex items-center justify-between p-4 rounded-xl bg-[#171E2B] border border-[#202938] hover:border-[#202938]/80 transition-all">
                        <div class="flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-sm" style="background-color: {{ $txColor }}15; color: {{ $txColor }};">
                                {{ $txIcon }}
                            </div>
                            <div>
                                <span class="text-[13px] font-bold text-[#F8FAFC]">
                                    {{ $isCredit ? 'شارژ حساب (واریز)' : 'خرید یا تمدید سرویس' }}
                                </span>
                                <span class="block text-[11px] text-[#94A3B8] mt-1 font-mono">
                                    {{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($tx->created_at))->format('%Y/%m/%d - H:i') }}
                                </span>
                            </div>
                        </div>
                        <span class="font-mono font-black text-[13px]" style="color: {{ $txColor }};" dir="ltr">
                            {{ $txIcon }}{{ number_format($tx->price) }} <span class="text-[9px] font-sans font-medium text-[#94A3B8]">تومان</span>
                        </span>
                    </div>
                @empty
                    <div class="py-16 text-center flex flex-col items-center">
                        <div class="w-14 h-14 rounded-full bg-[#94A3B8]/10 text-[#94A3B8] flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#F8FAFC]">تراکنشی ثبت نشده است</h4>
                        <p class="text-[11px] text-[#94A3B8] mt-1.5 font-medium">هنوز هیچ تراکنش مالی در سیستم ثبت نشده است.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 6. AVAILABLE SERVICES                        --}}
    {{-- ============================================ --}}
    <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-bold text-[#F8FAFC] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#6366F1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    تعرفه سرویس‌های قابل فروش
                </h3>
                <p class="text-[11px] text-[#94A3B8] mt-1.5 font-medium">
                    @if($discountPercent > 0)
                        قیمت‌های زیر با احتساب <span class="text-[#F59E0B] font-bold font-mono">{{ $discountPercent }}%</span> تخفیف اختصاصی شما محاسبه شده‌اند.
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
                    $hasDiscount = $discountPercent > 0;
                @endphp
                <div class="p-5 rounded-2xl bg-[#171E2B] border border-[#202938] hover:border-[#6366F1]/30 transition-all hover:-translate-y-0.5 flex flex-col justify-between h-full group">
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <span class="font-bold text-sm text-[#F8FAFC] leading-snug">{{ $group->name }}</span>
                            <span class="px-2 py-1 bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-[9px] font-black rounded-lg whitespace-nowrap uppercase">مجاز</span>
                        </div>

                        @if($hasDiscount)
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-bold text-[#10B981] bg-[#10B981]/10 px-2 py-0.5 rounded border border-[#10B981]/20">{{ $discountPercent }}% تخفیف نماینده</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-auto pt-4 border-t border-[#202938]">
                        @if($hasDiscount)
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-[10px] text-[#94A3B8]">قیمت پایه</span>
                                <div class="text-[11px] text-[#94A3B8] line-through font-mono">
                                    {{ number_format($basePrice) }}
                                </div>
                            </div>
                        @else
                            <div class="text-[10px] font-bold text-[#94A3B8] mb-1">قیمت نهایی:</div>
                        @endif

                        <div class="flex items-end justify-between">
                            <span class="text-[10px] font-bold text-[#94A3B8]">{{ $hasDiscount ? 'با تخفیف' : '' }}</span>
                            <div class="text-xl font-black text-[#6366F1] font-mono tracking-tight group-hover:text-[#4F46E5] transition-colors">
                                {{ number_format(round($group->getFinalPriceFor(auth()->user()))) }}
                                <span class="text-[10px] font-medium text-[#94A3B8] font-sans">تومان</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 flex flex-col items-center justify-center bg-[#171E2B] rounded-2xl border border-dashed border-[#202938]">
                    <div class="w-14 h-14 rounded-full bg-[#94A3B8]/10 text-[#94A3B8] flex items-center justify-center mb-4 border border-[#202938]/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h4 class="text-sm font-bold text-[#F8FAFC]">هیچ سرویس فعالی وجود ندارد</h4>
                    <p class="text-[11px] text-[#94A3B8] mt-1.5 font-medium">هیچ سرویس فعالی برای شما تعریف نشده است.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
