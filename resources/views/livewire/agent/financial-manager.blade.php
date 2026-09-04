<div class="space-y-6">
    {{-- ============================================ --}}
    {{-- 1. PAGE HEADER + BALANCE                     --}}
    {{-- ============================================ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#F8FAFC] tracking-tight">مدیریت مالی و کیف پول</h1>
            <p class="text-xs text-[#94A3B8] mt-1">مدیریت موجودی، فیش‌های واریزی و حساب‌های زیرنمایندگان</p>
        </div>

        <div class="bg-[#111722] border border-[#202938] rounded-2xl px-6 py-4 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-full bg-[#F59E0B]/10 flex items-center justify-center text-[#F59E0B] shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] text-[#94A3B8] font-bold uppercase tracking-wider">موجودی قابل استفاده</p>
                <div class="text-xl font-black text-[#F8FAFC] font-mono tabular-nums">{{ number_format($balance) }} <span class="text-xs text-[#94A3B8] font-sans">تومان</span></div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 2. BANK ACCOUNT CARD                         --}}
    {{-- ============================================ --}}
    @if($bankAccount)
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#F59E0B]/10 border border-[#F59E0B]/20 flex items-center justify-center text-[#F59E0B] shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="text-sm font-bold text-[#F8FAFC]">اطلاعات حساب جهت واریز</h3>
                            @if(auth()->user()->role === 'manager')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20">حساب مدیر اصلی</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#6366F1]/10 text-[#6366F1] border border-[#6366F1]/20">حساب نماینده بالادستی</span>
                            @endif
                        </div>
                        <p class="text-[10px] text-[#94A3B8] mt-0.5">شماره حساب زیر برای واریز وجه استفاده می‌شود</p>
                    </div>
                </div>

                {{-- دکمه نمایش/مخفی کردن اطلاعات --}}
                <button wire:click="toggleBankDetails"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $showBankDetails ? 'bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20' : 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($showBankDetails)
                            <path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        @else
                            <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        @endif
                    </svg>
                    {{ $showBankDetails ? 'مخفی کردن اطلاعات' : 'نمایش اطلاعات حساب' }}
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4 pt-4 border-t border-[#202938]">
                {{-- بانک --}}
                <div>
                    <span class="text-[10px] text-[#94A3B8] block">بانک</span>
                    <span class="text-sm font-bold text-[#F8FAFC]">{{ $bankAccount->bank_name }}</span>
                </div>

                {{-- صاحب حساب --}}
                <div>
                    <span class="text-[10px] text-[#94A3B8] block">صاحب حساب</span>
                    <span class="text-sm font-bold text-[#F8FAFC]">{{ $bankAccount->account_name }}</span>
                </div>

                {{-- شماره کارت --}}
                @if($bankAccount->card_number)
                    <div>
                        <span class="text-[10px] text-[#94A3B8] block">شماره کارت</span>
                        <div class="flex items-center gap-2">
                    <span class="text-sm font-mono font-bold text-[#F8FAFC] tracking-wider" dir="ltr">
                        @if($showBankDetails)
                            {{ $bankAccount->card_number }}
                        @else
                            {{ $this->maskCardNumber($bankAccount->card_number) }}
                        @endif
                    </span>
                            <button onclick="copyToClipboard('{{ $bankAccount->card_number }}', 'شماره کارت')"
                                    class="p-1.5 rounded-lg bg-[#202938] hover:bg-[#F59E0B] text-[#94A3B8] hover:text-white transition group"
                                    title="کپی شماره کارت">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- شماره شبا --}}
                @if($bankAccount->sheba_number)
                    <div>
                        <span class="text-[10px] text-[#94A3B8] block">شماره شبا</span>
                        <div class="flex items-center gap-2">
                    <span class="text-sm font-mono font-bold text-[#F8FAFC]" dir="ltr">
                        @if($showBankDetails)
                            {{ $bankAccount->sheba_number }}
                        @else
                            {{ $this->maskShebaNumber($bankAccount->sheba_number) }}
                        @endif
                    </span>
                            <button onclick="copyToClipboard('{{ $bankAccount->sheba_number }}', 'شماره شبا')"
                                    class="p-1.5 rounded-lg bg-[#202938] hover:bg-[#F59E0B] text-[#94A3B8] hover:text-white transition group"
                                    title="کپی شماره شبا">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- هشدار امنیتی --}}
            @if($showBankDetails)
                <div class="mt-3 pt-3 border-t border-[#202938] text-[10px] text-[#F59E0B] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>اطلاعات حساب به صورت کامل نمایش داده می‌شود. پس از اتمام، حتماً آن را مخفی کنید.</span>
                </div>
            @endif
        </div>
    @else
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#F59E0B]/10 border border-[#F59E0B]/20 flex items-center justify-center text-[#F59E0B] shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-[#F8FAFC]">اطلاعات حساب بانکی در دسترس نیست</h4>
                    <p class="text-xs text-[#94A3B8] mt-0.5">در حال حاضر هیچ حساب بانکی فعالی برای دریافت واریز ثبت نشده است.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 3. TABS                                      --}}
    {{-- ============================================ --}}
    <div class="flex gap-2 border-b border-[#202938] pb-3">
        <button wire:click="$set('activeTab', 'my_wallet')"
                class="px-5 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 {{ $activeTab === 'my_wallet' ? 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938]/50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            کیف پول من
            @php $pendingCount = $myTransactions->where('approved', 0)->count(); @endphp
            @if($pendingCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#F59E0B]/20 text-[#F59E0B]">{{ $pendingCount }}</span>
            @endif
        </button>
        @if(auth()->user()->role === 'agent')
            <button wire:click="$set('activeTab', 'auto_charge')"
                    class="px-5 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 {{ $activeTab === 'auto_charge' ? 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938]/50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                 کارت به کارت تایید آنی
            </button>
        @endif
        <button wire:click="$set('activeTab', 'sub_agents')"
                class="px-5 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 {{ $activeTab === 'sub_agents' ? 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938]/50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            امور مالی زیرنمایندگان
            @php $subPendingCount = $subTransactions->where('approved', 0)->count(); @endphp
            @if($subPendingCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#F59E0B]/20 text-[#F59E0B]">{{ $subPendingCount }}</span>
            @endif
        </button>
    </div>


    {{-- ============================================ --}}
    {{-- 6. TAB: AUTO CHARGE (فقط برای agent)        --}}
    {{-- ============================================ --}}
    @if($activeTab === 'auto_charge' && auth()->user()->role === 'agent')
        <div class="animate-fade-in">
            @if(!$isWaiting)
                {{-- فرم ورود مبلغ --}}
                <div class="bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm max-w-2xl mx-auto">
                    <h3 class="text-sm font-bold text-[#F8FAFC] mb-1">شارژ خودکار کیف پول</h3>
                    <p class="text-[10px] text-[#94A3B8] mb-4">مبلغ موردنظر را وارد کنید تا مبلغ نهایی قابل پرداخت را دریافت کنید.</p>

                    @if (session()->has('error'))
                        <div class="p-3 mb-4 rounded-xl bg-[#EF4444]/10 border border-[#EF4444]/20 text-[#EF4444] text-[11px] font-bold flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <form wire:submit.prevent="createTopupRequest" class="space-y-4">
                        <div>
                            <label class="block text-[11px] text-[#94A3B8] font-bold mb-1.5">مبلغ موردنظر برای شارژ (تومان) <span class="text-[#EF4444]">*</span></label>
                            <div class="relative">
                                <input wire:model.live.debounce.300ms="chargeAmount"
                                       type="number"
                                       dir="ltr"
                                       class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl px-4 py-3 font-mono tabular-nums focus:ring-1 focus:ring-[#F59E0B] focus:outline-none transition"
                                       placeholder="مثال: 1000000">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[#94A3B8] font-bold pointer-events-none">تومان</span>
                            </div>
                            @error('chargeAmount') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full py-3 bg-[#F59E0B] hover:bg-[#D97706] text-white font-black text-xs rounded-xl transition shadow-lg shadow-[#F59E0B]/20 flex items-center justify-center gap-2">
                            <svg wire:loading wire:target="createTopupRequest" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                            <span wire:loading.remove wire:target="createTopupRequest">ادامه و دریافت مبلغ پرداخت</span>
                            <span wire:loading wire:target="createTopupRequest">در حال ثبت...</span>
                        </button>
                    </form>
                </div>
            @else
                {{-- نمایش اطلاعات درخواست فعال --}}
                <div class="bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm max-w-2xl mx-auto" wire:poll.5s="checkStatus">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#F59E0B] animate-pulse"></span>
                            در انتظار واریز
                        </h3>
                        <button wire:click="cancelRequest" class="text-[10px] text-[#94A3B8] hover:text-[#EF4444] transition font-bold">
                            لغو درخواست
                        </button>
                    </div>

                    {{-- مبلغ شارژ --}}
                    <div class="bg-[#080B12] border border-[#202938] rounded-xl p-4 text-center">
                        <span class="text-[10px] text-[#94A3B8]">مبلغ شارژ کیف پول</span>
                        <div class="text-2xl font-black text-[#F8FAFC] mt-1">{{ number_format($activeRequest->requested_amount) }} <span class="text-sm text-[#94A3B8] font-sans">تومان</span></div>
                    </div>

                    {{-- مبلغ قابل پرداخت --}}
                    <div class="mt-4 bg-[#F59E0B]/5 border border-[#F59E0B]/30 rounded-xl p-4 text-center relative">
                        <span class="text-[10px] text-[#F59E0B] font-bold">مبلغی که باید واریز کنید</span>
                        <div class="text-3xl font-black text-[#F59E0B] mt-1 font-mono" dir="ltr">
                            {{ number_format($activeRequest->payable_amount) }}
                        </div>
                        <span class="text-[10px] text-[#94A3B8] block mt-1">تومان</span>
                        <button onclick="copyToClipboard('{{ $activeRequest->payable_amount }}', 'مبلغ')"
                                class="absolute left-3 top-3 p-1.5 rounded-lg bg-[#202938] hover:bg-[#F59E0B] text-[#94A3B8] hover:text-white transition group">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- هشدار --}}
                    <div class="mt-3 flex items-start gap-2 text-[10px] text-[#EF4444] bg-[#EF4444]/5 border border-[#EF4444]/20 rounded-xl p-3">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span><strong>توجه:</strong> برای تأیید خودکار، دقیقاً مبلغ <strong>{{ number_format($activeRequest->payable_amount) }}</strong> تومان را به شماره کارت زیر واریز کنید.</span>
                    </div>

                    {{-- اطلاعات حساب مقصد --}}
                    @if($bankAccount)
                        <div class="mt-4 border-t border-[#202938] pt-4">
                            <h4 class="text-[11px] text-[#94A3B8] font-bold mb-2">واریز به حساب</h4>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <span class="text-[#94A3B8]">بانک</span>
                                    <div class="font-bold text-[#F8FAFC]">{{ $bankAccount->bank_name }}</div>
                                </div>
                                <div>
                                    <span class="text-[#94A3B8]">صاحب حساب</span>
                                    <div class="font-bold text-[#F8FAFC]">{{ $bankAccount->account_name }}</div>
                                </div>
                                @if($bankAccount->card_number)
                                    <div class="col-span-2">
                                        <span class="text-[#94A3B8]">شماره کارت</span>
                                        <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-[#F8FAFC] tracking-wider" dir="ltr">
                                            @if($showBankDetails)
                                                {{ $bankAccount->card_number }}
                                            @else
                                                {{ $this->maskCardNumber($bankAccount->card_number) }}
                                            @endif
                                        </span>
                                            <button onclick="copyToClipboard('{{ $bankAccount->card_number }}', 'شماره کارت')"
                                                    class="p-1.5 rounded-lg bg-[#202938] hover:bg-[#F59E0B] text-[#94A3B8] hover:text-white transition group">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button wire:click="toggleBankDetails"
                                    class="mt-3 text-[10px] text-[#94A3B8] hover:text-[#F8FAFC] transition flex items-center gap-1">
                                {{ $showBankDetails ? 'مخفی کردن اطلاعات حساب' : 'نمایش اطلاعات حساب' }}
                            </button>
                        </div>
                    @endif

                    {{-- تایمر --}}
                    <div class="mt-4 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[#94A3B8]">وضعیت:</span>
                            <span class="font-bold text-[#F59E0B] mr-1">در انتظار واریز</span>
                        </div>
                        <div x-data="{
    timeLeft: {{ max(0, (int) now()->diffInSeconds($activeRequest->expires_at)) }},
    interval: null
}"
                             x-init="interval = setInterval(() => { if (timeLeft > 0) timeLeft--; }, 1000);
         $watch('timeLeft', value => { if (value <= 0) clearInterval(interval); })"
                             class="flex items-center gap-1 text-[#94A3B8]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>زمان باقی‌مانده:</span>
                            <span class="font-mono font-bold text-[#F8FAFC]"
                                  x-text="String(Math.floor(timeLeft / 60)).padStart(2, '0') + ':' + String(Math.floor(timeLeft % 60)).padStart(2, '0')">
    </span>
                        </div>
                    </div>

                    {{-- دکمه تست (فقط برای محیط توسعه) --}}

                </div>
            @endif
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 4. TAB: MY WALLET                            --}}
    {{-- ============================================ --}}
    @if($activeTab === 'my_wallet')
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 animate-fade-in">
            {{-- Left: Upload Receipt Form --}}
            <div class="lg:col-span-2 bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm">
                <h3 class="text-sm font-bold text-[#F8FAFC] mb-1">ثبت درخواست شارژ کیف پول</h3>
                <p class="text-[10px] text-[#94A3B8] mb-4">پس از واریز وجه، اطلاعات پرداخت و تصویر فیش را ارسال کنید.</p>

                {{-- Alert --}}
                @if (session()->has('success'))
                    <div class="p-3 mb-4 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-[11px] font-bold flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="submitMyReceipt" class="space-y-4">
                    {{-- Amount --}}
                    <div>
                        <label class="block text-[11px] text-[#94A3B8] font-bold mb-1.5">مبلغ واریزی (تومان) <span class="text-[#EF4444]">*</span></label>
                        <div class="relative">
                            <input wire:model.live.debounce.300ms="myAmount"
                                   type="number"
                                   dir="ltr"
                                   class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl px-4 py-3 font-mono tabular-nums focus:ring-1 focus:ring-[#F59E0B] focus:outline-none transition"
                                   placeholder="مثال: 1500000">
                            {{-- برای RTL، right-3 یعنی سمت چپ عدد --}}
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[#94A3B8] font-bold pointer-events-none">تومان</span>
                        </div>
                        @error('myAmount') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        @if($myAmount && $myAmount > 0)
                            <div class="text-[13px] text-green-600  mt-1.5 text-center">
                                {{ $this->convertToPersianWords((int)$myAmount) }} تومان
                            </div>
                        @endif
                    </div>

                    {{-- Receipt Upload --}}
                    <div>
                        <label class="block text-[11px] text-[#94A3B8] font-bold mb-1.5">تصویر فیش <span class="text-[#EF4444]">*</span></label>
                        <div class="relative">
                            <input wire:model="myReceipt"
                                   type="file"
                                   accept="image/*"
                                   class="w-full bg-[#080B12] border border-[#202938] text-[#94A3B8] text-xs rounded-xl p-3 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#202938] file:text-[#F8FAFC] hover:file:bg-[#171E2B] focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition cursor-pointer">
                        </div>
                        @error('myReceipt') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        @if($myReceipt)
                            <div class="flex items-center gap-3 mt-2 p-2 bg-[#080B12] border border-[#202938] rounded-xl">
                                <svg class="w-4 h-4 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-xs text-[#F8FAFC]">{{ $myReceipt->getClientOriginalName() }}</span>
                                <span class="text-[10px] text-[#94A3B8]">({{ round($myReceipt->getSize() / 1024) }} KB)</span>
                                <button wire:click="$set('myReceipt', null)" class="mr-auto text-[#94A3B8] hover:text-[#EF4444] transition">✕</button>
                            </div>
                        @endif
                        <p class="text-[9px] text-[#94A3B8] mt-1">فرمت‌های مجاز: JPG, PNG, GIF • حداکثر حجم: ۲ مگابایت</p>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-[11px] text-[#94A3B8] font-bold mb-1.5">توضیحات (اختیاری)</label>
                        <textarea wire:model="myDescription"
                                  rows="2"
                                  class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl px-4 py-3 focus:ring-1 focus:ring-[#F59E0B] focus:outline-none transition resize-none"
                                  placeholder="مثلاً: واریز بابت افزایش موجودی کیف پول"></textarea>
                    </div>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-3 bg-[#F59E0B] hover:bg-[#D97706] text-white font-black text-xs rounded-xl transition shadow-lg shadow-[#F59E0B]/20 flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="submitMyReceipt" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                        <span wire:loading.remove wire:target="submitMyReceipt">آپلود و ثبت فیش</span>
                        <span wire:loading wire:target="submitMyReceipt">در حال ارسال درخواست...</span>
                    </button>
                </form>
            </div>

            {{-- Right: Transaction History --}}
            <div class="lg:col-span-3 bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <h3 class="text-sm font-bold text-[#F8FAFC]">تاریخچه تراکنش‌های من</h3>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <svg class="w-4 h-4 absolute right-3 top-2.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input wire:model.live.debounce.300ms="myTransactionSearch"
                                   type="text"
                                   placeholder="جستجوی شرح..."
                                   class="w-40 sm:w-48 bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-lg py-2 pr-9 pl-3 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">
                        </div>
                        @if($myTransactionSearch)
                            <button wire:click="$set('myTransactionSearch', '')" class="text-[#94A3B8] hover:text-[#F8FAFC] text-xs">✕</button>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="text-[#94A3B8] bg-[#080B12] border-b border-[#202938]">
                        <tr>
                            <th class="p-3 rounded-r-xl">مبلغ</th>
                            <th class="p-3">شرح</th>
                            <th class="p-3">وضعیت</th>
                            <th class="p-3 rounded-l-xl">تاریخ</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-[#202938] text-[#F8FAFC]">
                        @forelse($myTransactions as $tx)
                            @php
                                $isPlus = in_array($tx->type, ['plus', 'plus_amn']);
                                $color = $isPlus ? 'text-[#10B981]' : 'text-[#EF4444]';
                                $sign = $isPlus ? '+' : '−';
                                $statusMap = [
                                    1 => ['label' => 'تایید شده', 'class' => 'bg-[#10B981]/10 text-[#10B981] border-[#10B981]/20'],
                                    0 => ['label' => 'در انتظار', 'class' => 'bg-[#F59E0B]/10 text-[#F59E0B] border-[#F59E0B]/20'],
                                    2 => ['label' => 'رد شده', 'class' => 'bg-[#EF4444]/10 text-[#EF4444] border-[#EF4444]/20'],
                                ];
                                $status = $statusMap[$tx->approved] ?? $statusMap[1];
                            @endphp
                            <tr class="hover:bg-[#171E2B]/40 transition">
                                <td class="p-3 font-mono font-bold {{ $color }} tabular-nums" dir="ltr">{{ $sign }}{{ number_format($tx->price) }}</td>
                                <td class="p-3 text-[11px] max-w-[150px] truncate">{{ $tx->description ?? 'ثبت سیستمی' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold border {{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                                <td class="p-3 text-[10px] text-[#94A3B8] font-mono">{{ \Morilog\Jalali\Jalalian::fromCarbon($tx->created_at)->format('Y/m/d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-[#94A3B8] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        <h4 class="text-sm font-bold text-[#F8FAFC]">هنوز تراکنشی ثبت نشده است</h4>
                                        <p class="text-xs text-[#94A3B8] mt-1">پس از ثبت یا تایید تراکنش‌ها، تاریخچه مالی شما در این بخش نمایش داده می‌شود.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($myTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $myTransactions->hasPages())
                    <div class="pt-4 border-t border-[#202938] mt-4">
                        {{ $myTransactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 5. TAB: SUB AGENTS                           --}}
    {{-- ============================================ --}}
    @if($activeTab === 'sub_agents')
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 animate-fade-in">
            {{-- Left: Manage Sub Agent --}}
            <div class="lg:col-span-2 bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm">
                <h3 class="text-sm font-bold text-[#F8FAFC] mb-1">مدیریت موجودی زیرنماینده</h3>
                <p class="text-[10px] text-[#94A3B8] mb-4">انتقال وجه بین کیف پول خود و زیرنمایندگان</p>

                @if (session()->has('success_sub'))
                    <div class="p-3 mb-4 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-[11px] font-bold flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success_sub') }}</span>
                    </div>
                @endif
                @if (session()->has('error_sub'))
                    <div class="p-3 mb-4 rounded-xl bg-[#EF4444]/10 border border-[#EF4444]/20 text-[#EF4444] text-[11px] font-bold flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('error_sub') }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="manageSubAgentBalance" class="space-y-4">
                    {{-- Sub Agent Select --}}
                    <div>
                        <label class="block text-[11px] text-[#94A3B8] font-bold mb-1.5">انتخاب زیرنماینده <span class="text-[#EF4444]">*</span></label>
                        <select wire:model="subAgentId"
                                class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl px-4 py-3 focus:ring-1 focus:ring-[#F59E0B] focus:outline-none transition">
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($subAgents as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->username }})</option>
                            @endforeach
                        </select>
                        @error('subAgentId') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Sub Agent Info (when selected) --}}
                    @if($subAgentId)
                        @php $selectedSub = $subAgents->firstWhere('id', $subAgentId); @endphp
                        @if($selectedSub)
                            <div class="p-3 bg-[#080B12] border border-[#202938] rounded-xl flex items-center justify-between text-xs">
                                <span class="text-[#94A3B8]">موجودی <span class="text-[#F8FAFC] font-bold">{{ $selectedSub->name }}</span></span>
                                <span class="font-mono font-bold text-[#F8FAFC]">{{ number_format($selectedSub->balance) }} تومان</span>
                            </div>
                        @endif
                    @endif

                    {{-- Type --}}
                    <div>
                        <label class="block text-[11px] text-[#94A3B8] font-bold mb-1.5">نوع عملیات <span class="text-[#EF4444]">*</span></label>
                        <select wire:model="subType"
                                class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl px-4 py-3 focus:ring-1 focus:ring-[#F59E0B] focus:outline-none transition">
                            <option value="plus">افزایش موجودی (کسر از کیف پول من)</option>
                            <option value="minus">کسر موجودی (بازگشت به کیف پول من)</option>
                        </select>
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label class="block text-[11px] text-[#94A3B8] font-bold mb-1.5">مبلغ (تومان) <span class="text-[#EF4444]">*</span></label>
                        <input wire:model="subAmount"
                               type="number"
                               dir="ltr"
                               class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl px-4 py-3 font-mono tabular-nums focus:ring-1 focus:ring-[#F59E0B] focus:outline-none transition"
                               placeholder="مثال: 500000">
                        @error('subAmount') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Summary --}}
                    @if($subAgentId && $subAmount && $subAmount > 0)
                        <div class="p-3 bg-[#080B12] border border-[#202938] rounded-xl space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">کیف پول شما</span>
                                <span class="font-mono font-bold text-[#F8FAFC]">{{ number_format($balance) }} تومان</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">نوع عملیات</span>
                                <span class="font-bold {{ $subType === 'plus' ? 'text-[#10B981]' : 'text-[#EF4444]' }}">
                                    {{ $subType === 'plus' ? 'افزایش زیرنماینده' : 'کسر از زیرنماینده' }}
                                </span>
                            </div>
                            <div class="flex justify-between text-[#F59E0B] font-bold">
                                <span>مبلغ انتقال</span>
                                <span class="font-mono">{{ number_format($subAmount) }} تومان</span>
                            </div>
                            <div class="border-t border-[#202938] pt-1.5 flex justify-between">
                                <span class="text-[#94A3B8]">موجودی شما پس از عملیات</span>
                                <span class="font-mono font-bold {{ ($subType === 'plus' && $subAmount > $balance) ? 'text-[#EF4444]' : 'text-[#F8FAFC]' }}">
                                    {{ number_format($subType === 'plus' ? $balance - $subAmount : $balance + $subAmount) }} تومان
                                </span>
                            </div>
                        </div>
                    @endif

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-3 bg-[#F59E0B] hover:bg-[#D97706] text-white font-black text-xs rounded-xl transition shadow-lg shadow-[#F59E0B]/20 flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="manageSubAgentBalance" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                        <span wire:loading.remove wire:target="manageSubAgentBalance">اعمال تغییرات</span>
                        <span wire:loading wire:target="manageSubAgentBalance">در حال پردازش...</span>
                    </button>
                </form>
            </div>

            {{-- Right: Sub Agent Transactions --}}
            <div class="lg:col-span-3 bg-[#111722] border border-[#202938] rounded-2xl p-5 shadow-sm overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <h3 class="text-sm font-bold text-[#F8FAFC]">فیش‌ها و تراکنش‌های زیرنمایندگان</h3>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <svg class="w-4 h-4 absolute right-3 top-2.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input wire:model.live.debounce.300ms="subTransactionSearch"
                                   type="text"
                                   placeholder="جستجوی شرح..."
                                   class="w-40 sm:w-48 bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-lg py-2 pr-9 pl-3 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">
                        </div>
                        @if($subTransactionSearch)
                            <button wire:click="$set('subTransactionSearch', '')" class="text-[#94A3B8] hover:text-[#F8FAFC] text-xs">✕</button>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="text-[#94A3B8] bg-[#080B12] border-b border-[#202938]">
                        <tr>
                            <th class="p-3 rounded-r-xl">زیرنماینده</th>
                            <th class="p-3">مبلغ</th>
                            <th class="p-3">شرح / فیش</th>
                            <th class="p-3">وضعیت</th>
                            <th class="p-3 rounded-l-xl">عملیات</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-[#202938] text-[#F8FAFC]">
                        @forelse($subTransactions as $tx)
                            @php
                                $isPlus = in_array($tx->type, ['plus', 'plus_amn']);
                                $color = $isPlus ? 'text-[#10B981]' : 'text-[#EF4444]';
                                $sign = $isPlus ? '+' : '−';
                                $subUser = $subAgents->firstWhere('id', $tx->for);
                                $statusMap = [
                                    1 => ['label' => 'تایید شده', 'class' => 'bg-[#10B981]/10 text-[#10B981] border-[#10B981]/20'],
                                    0 => ['label' => 'در انتظار', 'class' => 'bg-[#F59E0B]/10 text-[#F59E0B] border-[#F59E0B]/20'],
                                    2 => ['label' => 'رد شده', 'class' => 'bg-[#EF4444]/10 text-[#EF4444] border-[#EF4444]/20'],
                                ];
                                $status = $statusMap[$tx->approved] ?? $statusMap[1];
                            @endphp
                            <tr class="hover:bg-[#171E2B]/40 transition">
                                <td class="p-3 text-[11px] font-bold text-[#F8FAFC]">{{ $subUser->name ?? 'کاربر نامشخص' }}</td>
                                <td class="p-3 font-mono font-bold {{ $color }} tabular-nums" dir="ltr">{{ $sign }}{{ number_format($tx->price) }}</td>
                                <td class="p-3 text-[11px]">
                                    <span class="block truncate max-w-[120px]">{{ $tx->description }}</span>
                                    @if($tx->attachment)
                                        <button wire:click="previewReceipt('{{ $tx->id }}')"
                                                class="text-[#3B82F6] hover:text-[#2563EB] text-[10px] font-bold transition block mt-0.5">
                                            🖼 مشاهده فیش
                                        </button>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold border {{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                                <td class="p-3">
                                    @if($tx->approved == 0)
                                        <div class="flex gap-1.5">
                                            <button wire:click="openReviewModal({{ $tx->id }})"
                                                    class="px-2.5 py-1.5 rounded-lg bg-[#F59E0B] hover:bg-[#D97706] text-white text-[10px] font-bold transition">
                                                بررسی
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[#94A3B8] text-[10px]">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-[#94A3B8] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <h4 class="text-sm font-bold text-[#F8FAFC]">هیچ تراکنشی برای زیرنمایندگان ثبت نشده است</h4>
                                        <p class="text-xs text-[#94A3B8] mt-1">پس از ثبت یا تایید تراکنش‌ها، در این بخش نمایش داده می‌شوند.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $subTransactions->hasPages())
                    <div class="pt-4 border-t border-[#202938] mt-4">
                        {{ $subTransactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 6. REVIEW RECEIPT MODAL                      --}}
    {{-- ============================================ --}}
    @if($isReviewModalOpen && $reviewTransaction)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-md">
            <div class="fixed inset-0" wire:click="$set('isReviewModalOpen', false)"></div>
            <div class="relative w-full max-w-md bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-5 border-b border-[#202938]">
                    <h3 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        بررسی فیش واریزی
                    </h3>
                    <button wire:click="$set('isReviewModalOpen', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-[#94A3B8] block">زیرنماینده</span>
                            <span class="font-bold text-[#F8FAFC]">{{ $reviewSubAgentName }}</span>
                        </div>
                        <div>
                            <span class="text-[#94A3B8] block">مبلغ</span>
                            <span class="font-mono font-bold text-[#F8FAFC]">{{ number_format($reviewTransaction->price) }} تومان</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[#94A3B8] block">توضیحات</span>
                            <span class="text-[#F8FAFC]">{{ $reviewTransaction->description ?? 'بدون توضیح' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[#94A3B8] block">تاریخ ثبت</span>
                            <span class="text-[#F8FAFC] font-mono">{{ \Morilog\Jalali\Jalalian::fromCarbon($reviewTransaction->created_at)->format('Y/m/d H:i') }}</span>
                        </div>
                    </div>

                    @if($reviewTransaction->attachment)
                        <div class="bg-[#080B12] border border-[#202938] rounded-xl p-3 text-center">
                            <a href="{{ asset('storage/' . $reviewTransaction->attachment) }}" target="_blank"
                               class="inline-flex items-center gap-2 text-[#3B82F6] hover:text-[#2563EB] text-xs font-bold transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                مشاهده فیش
                            </a>
                        </div>
                    @endif

                    <div class="border-t border-[#202938] pt-4 flex items-center gap-3">
                        <button wire:click="toggleSubAgentReceipt({{ $reviewTransaction->id }}, 2)"
                                class="flex-1 py-2.5 rounded-xl bg-[#EF4444] hover:bg-[#DC2626] text-white text-xs font-bold transition">
                            رد فیش
                        </button>
                        <button wire:click="toggleSubAgentReceipt({{ $reviewTransaction->id }}, 1)"
                                wire:loading.attr="disabled"
                                class="flex-1 py-2.5 rounded-xl bg-[#10B981] hover:bg-[#059669] text-white text-xs font-bold transition flex items-center justify-center gap-2">
                            <svg wire:loading wire:target="toggleSubAgentReceipt({{ $reviewTransaction->id }}, 1)" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                            <span wire:loading.remove wire:target="toggleSubAgentReceipt({{ $reviewTransaction->id }}, 1)">تایید فیش</span>
                            <span wire:loading wire:target="toggleSubAgentReceipt({{ $reviewTransaction->id }}, 1)">در حال تایید...</span>
                        </button>
                    </div>
                    <p class="text-[10px] text-[#94A3B8] text-center">با تایید این درخواست، مبلغ به کیف پول زیرنماینده اضافه خواهد شد.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- 7. TOAST NOTIFICATION                        --}}
    {{-- ============================================ --}}
    @if(session()->has('toast_message'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 5000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="fixed bottom-6 left-6 z-[200] max-w-sm bg-[#111722] border border-[#202938] rounded-2xl p-4 shadow-2xl">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full {{ session('toast_type') === 'success' ? 'bg-[#10B981]/20 text-[#10B981]' : 'bg-[#EF4444]/20 text-[#EF4444]' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="{{ session('toast_type') === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-[#F8FAFC]">{{ session('toast_title') }}</p>
                    <p class="text-xs text-[#94A3B8] mt-0.5">{{ session('toast_message') }}</p>
                </div>
                <button @click="show = false" class="mr-auto text-[#94A3B8] hover:text-[#F8FAFC]">✕</button>
            </div>
        </div>
    @endif
</div>
