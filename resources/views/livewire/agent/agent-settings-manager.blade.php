<div class="space-y-6 pb-12">
    {{-- ============================================ --}}
    {{-- 1. PAGE HEADER                               --}}
    {{-- ============================================ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#F8FAFC] tracking-tight">تنظیمات و پیکربندی</h1>
            <p class="text-xs text-[#94A3B8] mt-1">مدیریت برند، فروشگاه، حساب‌های مالی و قیمت‌گذاری پنل نمایندگی</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-full text-[10px] font-bold bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20">
                ⚙️ مرکز تنظیمات
            </span>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 2. QUICK SETTINGS OVERVIEW                   --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Brand --}}
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-4 shadow-sm hover:border-[#F59E0B]/30 transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#F59E0B]/10 flex items-center justify-center text-[#F59E0B] shrink-0 group-hover:bg-[#F59E0B]/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider block">برند</span>
                    <span class="text-sm font-bold text-[#F8FAFC]">{{ $brand_name ?? 'ثبت نشده' }}</span>
                    <span class="block text-[9px] text-[#94A3B8]">
                        {{ $custom_domain ? ($domain_status === 'approved' ? '✅ دامنه فعال' : ($domain_status === 'pending' ? '⏳ در انتظار' : '🔴 غیرفعال')) : 'بدون دامنه' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Store --}}
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-4 shadow-sm hover:border-[#10B981]/30 transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#10B981]/10 flex items-center justify-center text-[#10B981] shrink-0 group-hover:bg-[#10B981]/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider block">فروشگاه</span>
                    <span class="text-sm font-bold {{ $is_store_active ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">
                        {{ $is_store_active ? 'فعال' : 'غیرفعال' }}
                    </span>
                    <span class="block text-[9px] text-[#94A3B8] truncate max-w-[100px]">{{ $store_title ?: 'بدون عنوان' }}</span>
                </div>
            </div>
        </div>

        {{-- Bank Accounts --}}
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-4 shadow-sm hover:border-[#3B82F6]/30 transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6] shrink-0 group-hover:bg-[#3B82F6]/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider block">حساب‌های مالی</span>
                    <span class="text-sm font-bold text-[#F8FAFC]">{{ $bankAccounts->count() }}</span>
                    <span class="text-[9px] text-[#94A3B8]">حساب ثبت شده</span>
                </div>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="bg-[#111722] border border-[#202938] rounded-2xl p-4 shadow-sm hover:border-[#8B5CF6]/30 transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#8B5CF6]/10 flex items-center justify-center text-[#8B5CF6] shrink-0 group-hover:bg-[#8B5CF6]/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider block">قیمت‌گذاری</span>
                    <span class="text-sm font-bold text-[#F8FAFC]">{{ $sub_agent_markup ?? 0 }}%</span>
                    <span class="text-[9px] text-[#94A3B8]">سود زیرنمایندگان</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 3. MAIN SETTINGS LAYOUT                      --}}
    {{-- ============================================ --}}
    <div class="flex flex-col lg:flex-row gap-6 items-start">

        {{-- Sidebar Navigation --}}
        <div class="w-full lg:w-72 bg-[#111722] border border-[#202938] rounded-2xl p-4 shadow-sm shrink-0 sticky top-24">
            <div class="mb-4 pb-4 border-b border-[#202938]">
                <div class="flex items-center gap-2 text-xs font-bold text-[#94A3B8]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    دسته‌بندی تنظیمات
                </div>
                <p class="text-[9px] text-[#94A3B8]">مدیریت بخش‌های مختلف حساب</p>
            </div>

            <nav class="space-y-1.5">
                <button wire:click="switchTab('branding')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-sm text-right {{ $activeTab === 'branding' ? 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#171E2B]' }}">
                    <span class="w-8 h-8 rounded-lg {{ $activeTab === 'branding' ? 'bg-[#F59E0B]/20 text-[#F59E0B]' : 'bg-[#202938] text-[#94A3B8]' }} flex items-center justify-center shrink-0 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </span>
                    <div class="flex-1">
                        <div class="text-sm font-bold">برند و دامنه</div>
                        <div class="text-[9px] text-[#94A3B8]">شخصی‌سازی هویت فروشگاه</div>
                    </div>
                    @if($activeTab === 'branding')
                        <span class="w-1 h-8 rounded-full bg-[#F59E0B]"></span>
                    @endif
                </button>

                <button wire:click="switchTab('financial')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-sm text-right {{ $activeTab === 'financial' ? 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#171E2B]' }}">
                    <span class="w-8 h-8 rounded-lg {{ $activeTab === 'financial' ? 'bg-[#F59E0B]/20 text-[#F59E0B]' : 'bg-[#202938] text-[#94A3B8]' }} flex items-center justify-center shrink-0 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </span>
                    <div class="flex-1">
                        <div class="text-sm font-bold">حساب‌های بانکی</div>
                        <div class="text-[9px] text-[#94A3B8]">مدیریت حساب‌های واریز</div>
                    </div>
                    @if($activeTab === 'financial')
                        <span class="w-1 h-8 rounded-full bg-[#F59E0B]"></span>
                    @endif
                </button>

                <button wire:click="switchTab('store')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-sm text-right {{ $activeTab === 'store' ? 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#171E2B]' }}">
                    <span class="w-8 h-8 rounded-lg {{ $activeTab === 'store' ? 'bg-[#F59E0B]/20 text-[#F59E0B]' : 'bg-[#202938] text-[#94A3B8]' }} flex items-center justify-center shrink-0 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </span>
                    <div class="flex-1">
                        <div class="text-sm font-bold">فروشگاه اختصاصی</div>
                        <div class="text-[9px] text-[#94A3B8]">مدیریت فروشگاه آنلاین</div>
                    </div>
                    @if($activeTab === 'store')
                        <span class="w-1 h-8 rounded-full bg-[#F59E0B]"></span>
                    @endif
                </button>

                <button wire:click="switchTab('pricing')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-sm text-right {{ $activeTab === 'pricing' ? 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#171E2B]' }}">
                    <span class="w-8 h-8 rounded-lg {{ $activeTab === 'pricing' ? 'bg-[#F59E0B]/20 text-[#F59E0B]' : 'bg-[#202938] text-[#94A3B8]' }} flex items-center justify-center shrink-0 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <div class="flex-1">
                        <div class="text-sm font-bold">تعرفه و قیمت‌گذاری</div>
                        <div class="text-[9px] text-[#94A3B8]">مدیریت قیمت سرویس‌ها</div>
                    </div>
                    @if($activeTab === 'pricing')
                        <span class="w-1 h-8 rounded-full bg-[#F59E0B]"></span>
                    @endif
                </button>
            </nav>
        </div>

        {{-- Content --}}
        <div class="flex-1 w-full space-y-6">

            {{-- ========================================== --}}
            {{-- TAB: BRANDING                               --}}
            {{-- ========================================== --}}
            @if($activeTab === 'branding')
                <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm animate-fade-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-[#F59E0B]/10 border border-[#F59E0B]/20 flex items-center justify-center text-[#F59E0B]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-[#F8FAFC]">هویت برند و دامنه</h2>
                            <p class="text-xs text-[#94A3B8]">اطلاعاتی که مشتریان در فروشگاه و پنل اختصاصی شما مشاهده می‌کنند.</p>
                        </div>
                    </div>

                    @if(session('branding_msg'))
                        <div class="p-4 mb-6 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-sm font-bold flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ session('branding_msg') }}</span>
                        </div>
                    @endif

                    @if($domain_status === 'pending')
                        <div class="p-4 mb-6 rounded-xl bg-[#F59E0B]/10 border border-[#F59E0B]/20 text-[#F59E0B] text-sm font-bold flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div>
                                <span class="font-bold">در انتظار بررسی</span>
                                <span class="block text-[#94A3B8] font-normal text-xs mt-0.5">درخواست دامنه شما ({{ $custom_domain }}) در حال بررسی توسط مدیریت است.</span>
                            </div>
                        </div>
                    @elseif($domain_status === 'approved')
                        <div class="p-4 mb-6 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-sm font-bold flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <div>
                                <span class="font-bold">دامنه تایید شده</span>
                                <span class="block text-[#94A3B8] font-normal text-xs mt-0.5">دامنه شما ({{ $custom_domain }}) با موفقیت فعال شده است.</span>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="saveBranding" class="space-y-6">
                        {{-- Brand Name --}}
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">نام برند شما</label>
                            <div class="flex items-center gap-4">
                                <input wire:model="brand_name" type="text" placeholder="مثال: نت‌اسپید" class="flex-1 bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">
                                <div class="flex items-center gap-2 px-3 py-2 bg-[#080B12] border border-[#202938] rounded-xl">
                                    <span class="text-[10px] text-[#94A3B8]">پیش‌نمایش:</span>
                                    <span class="text-sm font-bold text-[#F8FAFC]">{{ $brand_name ?: 'نام برند' }}</span>
                                </div>
                            </div>
                            @error('brand_name') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Custom Domain --}}
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">دامنه اختصاصی</label>
                            <div class="flex items-center">
                                <span class="px-4 py-3 bg-[#080B12] border border-l-0 border-[#202938] rounded-r-xl text-sm text-[#94A3B8] font-mono">https://</span>
                                <input wire:model="custom_domain" type="text" dir="ltr" placeholder="panel.yourdomain.com" class="flex-1 bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm font-mono rounded-l-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">
                            </div>
                            <p class="text-[10px] text-[#94A3B8] mt-1.5">با اتصال دامنه اختصاصی، فروشگاه و پنل شما با برند شخصی نمایش داده می‌شود.</p>
                            @error('custom_domain') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Logo Upload --}}
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">لوگوی اختصاصی</label>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                @if($current_logo)
                                    <div class="w-20 h-20 rounded-xl overflow-hidden border border-[#202938] bg-[#080B12] flex items-center justify-center shrink-0">
                                        <img src="{{ asset('storage/'.$current_logo) }}" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-[10px] text-[#94A3B8]">لوگوی فعلی</span>
                                @endif
                                <div class="flex-1 w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#202938] rounded-xl cursor-pointer bg-[#080B12] hover:border-[#F59E0B]/50 transition group">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 text-[#94A3B8] group-hover:text-[#F59E0B] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-xs text-[#94A3B8] group-hover:text-[#F8FAFC] transition">لوگوی خود را آپلود کنید</p>
                                            <p class="text-[9px] text-[#94A3B8]">PNG / JPG - حداکثر ۲ مگابایت</p>
                                        </div>
                                        <input wire:model="logo" type="file" accept="image/*" class="hidden">
                                    </label>
                                    @error('logo') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                    @if($logo)
                                        <div class="mt-2 text-xs text-[#10B981] flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>{{ $logo->getClientOriginalName() }} ({{ round($logo->getSize() / 1024) }} KB)</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-[#202938]">
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-3 bg-[#F59E0B] hover:bg-[#D97706] text-white font-bold text-sm rounded-xl shadow-lg shadow-[#F59E0B]/20 transition flex items-center gap-2">
                                <svg wire:loading wire:target="saveBranding" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                                <span wire:loading.remove wire:target="saveBranding">ثبت و ذخیره تغییرات</span>
                                <span wire:loading wire:target="saveBranding">در حال ذخیره...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- TAB: FINANCIAL (BANK ACCOUNTS)             --}}
            {{-- ========================================== --}}
            @if($activeTab === 'financial')
                <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm animate-fade-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-[#3B82F6]/10 border border-[#3B82F6]/20 flex items-center justify-center text-[#3B82F6]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-[#F8FAFC]">حساب‌های بانکی</h2>
                            <p class="text-xs text-[#94A3B8]">مدیریت حساب‌هایی که مشتریان می‌توانند پرداخت‌های خود را به آن انجام دهند.</p>
                        </div>
                        <span class="mr-auto px-3 py-1 rounded-full text-[10px] font-bold bg-[#3B82F6]/10 text-[#3B82F6] border border-[#3B82F6]/20">{{ $bankAccounts->count() }} حساب فعال</span>
                    </div>

                    @if(session('bank_msg'))
                        <div class="p-4 mb-6 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-sm font-bold flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ session('bank_msg') }}</span>
                        </div>
                    @endif

                    {{-- Add Bank Account Form --}}
                    <div class="bg-[#080B12] border border-[#202938] rounded-2xl p-5 mb-6">
                        <h3 class="text-sm font-bold text-[#F8FAFC] mb-4 flex items-center gap-2">
                            <span class="text-lg">➕</span> افزودن حساب بانکی جدید
                        </h3>
                        <form wire:submit.prevent="saveBankAccount" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-[#94A3B8] mb-1">نام بانک</label>
                                <input wire:model="bank_name" type="text" placeholder="مثال: بانک ملت" class="w-full bg-[#111722] border border-[#202938] text-[#F8FAFC] rounded-xl text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#3B82F6] transition">
                                @error('bank_name') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[#94A3B8] mb-1">نام صاحب حساب</label>
                                <input wire:model="account_name" type="text" class="w-full bg-[#111722] border border-[#202938] text-[#F8FAFC] rounded-xl text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#3B82F6] transition">
                                @error('account_name') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[#94A3B8] mb-1">شماره کارت (۱۶ رقم)</label>
                                <input wire:model="card_number" type="text" dir="ltr" placeholder="1234-5678-9012-3456" class="w-full bg-[#111722] border border-[#202938] text-[#F8FAFC] rounded-xl text-sm font-mono px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#3B82F6] transition">
                                @error('card_number') <span class="text-[#EF4444] text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[#94A3B8] mb-1">شماره شبا (بدون IR)</label>
                                <input wire:model="sheba_number" type="text" dir="ltr" class="w-full bg-[#111722] border border-[#202938] text-[#F8FAFC] rounded-xl text-sm font-mono px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#3B82F6] transition">
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit" wire:loading.attr="disabled" class="px-6 py-3 bg-[#3B82F6] hover:bg-[#2563EB] text-white font-bold text-sm rounded-xl shadow-lg shadow-[#3B82F6]/20 transition flex items-center gap-2">
                                    <svg wire:loading wire:target="saveBankAccount" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                                    <span wire:loading.remove wire:target="saveBankAccount">افزودن حساب جدید</span>
                                    <span wire:loading wire:target="saveBankAccount">در حال افزودن...</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Saved Bank Accounts --}}
                    @if($bankAccounts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($bankAccounts as $bank)
                                <div class="bg-[#080B12] border border-[#202938] rounded-2xl p-5 hover:border-[#3B82F6]/30 transition-all group">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-lg">🏦</span>
                                                <span class="text-sm font-bold text-[#F8FAFC]">{{ $bank->bank_name }}</span>
                                            </div>
                                            <p class="text-xs text-[#94A3B8]">{{ $bank->account_name }}</p>
                                            <div class="mt-2 space-y-1">
                                                <div class="flex items-center gap-2 text-xs">
                                                    <span class="text-[#94A3B8]">💳</span>
                                                    <span class="font-mono text-[#F8FAFC]">{{ substr($bank->card_number, 0, 4) }} •••• •••• {{ substr($bank->card_number, -4) }}</span>
                                                </div>
                                                @if($bank->sheba_number)
                                                    <div class="flex items-center gap-2 text-xs">
                                                        <span class="text-[#94A3B8]">🔑</span>
                                                        <span class="font-mono text-[#F8FAFC]">IR••••••••••{{ substr($bank->sheba_number, -4) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <button wire:click="deleteBankAccount({{ $bank->id }})"
                                                wire:loading.attr="disabled"
                                                class="p-2 rounded-lg bg-[#EF4444]/10 text-[#EF4444] hover:bg-[#EF4444] hover:text-white transition group-hover:opacity-100"
                                                title="حذف حساب">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-[#94A3B8] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <h4 class="text-sm font-bold text-[#F8FAFC]">هنوز حساب بانکی ثبت نکرده‌اید</h4>
                                <p class="text-xs text-[#94A3B8] mt-1">برای دریافت پرداخت‌های مشتریان، اولین حساب بانکی خود را اضافه کنید.</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- TAB: STORE                                  --}}
            {{-- ========================================== --}}
            @if($activeTab === 'store')
                <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm animate-fade-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 flex items-center justify-center text-[#10B981]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-[#F8FAFC]">فروشگاه اختصاصی</h2>
                            <p class="text-xs text-[#94A3B8]">با فعال‌سازی فروشگاه، مشتریان با ورود به دامنه شما مستقیماً فرم خرید را می‌بینند.</p>
                        </div>
                    </div>

                    @if(session('store_msg'))
                        <div class="p-4 mb-6 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-sm font-bold flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ session('store_msg') }}</span>
                        </div>
                    @endif

                    <form wire:submit.prevent="saveStoreSettings" class="space-y-6">
                        {{-- Store Status --}}
                        <div class="bg-[#080B12] border border-[#202938] rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-sm font-bold text-[#F8FAFC] flex items-center gap-2">
                                    <span class="text-lg">🛍</span> وضعیت فروشگاه
                                </h3>
                                <p class="text-xs text-[#94A3B8] mt-0.5">امکان خرید مستقیم روی دامنه شما فعال شود.</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-bold {{ $is_store_active ? 'text-[#10B981]' : 'text-[#94A3B8]' }}">
                                    {{ $is_store_active ? '● فعال' : '● غیرفعال' }}
                                </span>
                                <button wire:click="$toggle('is_store_active')" type="button" class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none {{ $is_store_active ? 'bg-[#10B981]' : 'bg-[#94A3B8]' }}">
                                    <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $is_store_active ? 'translate-x-6' : 'translate-x-1' }} shadow-md"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Store Info --}}
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">عنوان فروشگاه (سئو)</label>
                            <input wire:model="store_title" type="text" placeholder="خرید آنلاین فیلترشکن" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#10B981] transition">
                            <p class="text-[10px] text-[#94A3B8] mt-1.5">این عنوان برای موتورهای جستجو و صفحه فروشگاه استفاده می‌شود.</p>
                        </div>

                        {{-- Support --}}
                        <div>
                            <label class="block text-xs font-bold text-[#94A3B8] mb-1.5">آیدی پشتیبانی تلگرام</label>
                            <div class="relative">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#94A3B8]">@</span>
                                <input wire:model="support_id" type="text" dir="ltr" placeholder="SupportID" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm font-mono rounded-xl px-4 py-3 pr-8 focus:outline-none focus:ring-1 focus:ring-[#10B981] transition">
                            </div>
                            <p class="text-[10px] text-[#94A3B8] mt-1.5">مشتریان از طریق این آیدی با شما در ارتباط خواهند بود.</p>
                        </div>

                        <div class="pt-4 border-t border-[#202938]">
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-3 bg-[#10B981] hover:bg-[#059669] text-white font-bold text-sm rounded-xl shadow-lg shadow-[#10B981]/20 transition flex items-center gap-2">
                                <svg wire:loading wire:target="saveStoreSettings" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                                <span wire:loading.remove wire:target="saveStoreSettings">ذخیره تنظیمات فروشگاه</span>
                                <span wire:loading wire:target="saveStoreSettings">در حال ذخیره...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- TAB: PRICING                               --}}
            {{-- ========================================== --}}
            @if($activeTab === 'pricing')
                <div class="bg-[#111722] border border-[#202938] rounded-2xl p-6 shadow-sm animate-fade-in space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#8B5CF6]/10 border border-[#8B5CF6]/20 flex items-center justify-center text-[#8B5CF6]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-[#F8FAFC]">تعرفه و قیمت‌گذاری</h2>
                            <p class="text-xs text-[#94A3B8]">تعیین درصد سود برای زیرنمایندگان و قیمت فروش سرویس‌ها به مشتریان نهایی.</p>
                        </div>
                    </div>

                    {{-- Sub Agent Markup --}}
                    <div class="bg-[#080B12] border border-[#202938] rounded-2xl p-5">
                        <h3 class="text-sm font-bold text-[#F8FAFC] mb-4">سود فروش به زیرنمایندگان</h3>
                        @if(session('markup_msg'))
                            <div class="p-3 mb-4 rounded-xl bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-xs font-bold flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ session('markup_msg') }}</span>
                            </div>
                        @endif
                        <form wire:submit.prevent="saveMarkup" class="flex flex-col sm:flex-row items-end gap-4">
                            <div class="flex-1 w-full">
                                <label class="block text-[11px] font-bold text-[#94A3B8] mb-1.5">درصد افزایش قیمت (Markup %)</label>
                                <div class="relative">
                                    <input wire:model="sub_agent_markup" type="number" step="0.1" dir="ltr" class="w-full bg-[#111722] border border-[#202938] text-[#F8FAFC] text-lg font-mono rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#8B5CF6] transition">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-[#94A3B8] font-bold">%</span>
                                </div>
                                <p class="text-[10px] text-[#94A3B8] mt-1">این درصد به قیمت پایه سرویس‌ها برای زیرنمایندگان اضافه می‌شود.</p>
                            </div>
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-3 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-bold text-sm rounded-xl shadow-lg shadow-[#8B5CF6]/20 transition flex items-center gap-2 shrink-0">
                                <svg wire:loading wire:target="saveMarkup" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"/></svg>
                                <span wire:loading.remove wire:target="saveMarkup">ذخیره سود</span>
                                <span wire:loading wire:target="saveMarkup">در حال ذخیره...</span>
                            </button>
                        </form>
                    </div>

                    {{-- Service Pricing --}}
                    <div>
                        <h3 class="text-sm font-bold text-[#F8FAFC] mb-4">قیمت‌گذاری سرویس‌ها برای فروشگاه (مشتریان نهایی)</h3>
                        @if($availableGroups->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach($availableGroups as $group)
                                    @php
                                        $agentCost = $group->getFinalPriceFor(auth()->user());
                                        $selling = $sellingPrices[$group->id] ?? $agentCost;
                                        $profit = max(0, $selling - $agentCost);
                                        $profitClass = $profit > 0 ? 'text-[#10B981]' : ($profit == 0 ? 'text-[#94A3B8]' : 'text-[#EF4444]');
                                    @endphp
                                    <div class="bg-[#080B12] border border-[#202938] rounded-2xl p-5 hover:border-[#8B5CF6]/30 transition-all">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-bold text-[#F8FAFC]">{{ $group->name }}</span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#202938] text-[#94A3B8]">فعال</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="text-[10px] text-[#94A3B8]">بهای تمام شده برای شما</span>
                                            <div class="text-sm font-bold text-[#F8FAFC] font-mono">{{ number_format(round($agentCost)) }} تومان</div>
                                        </div>
                                        <form wire:submit.prevent="saveSellingPrice({{ $group->id }})" class="space-y-2 border-t border-[#202938] pt-3">
                                            <div>
                                                <label class="block text-[10px] text-[#94A3B8] mb-1">قیمت فروش به مشتری</label>
                                                <div class="flex items-center gap-2">
                                                    <input wire:model="sellingPrices.{{ $group->id }}" type="number" placeholder="قیمت فروش" dir="ltr" class="flex-1 bg-[#111722] border border-[#202938] text-[#F8FAFC] text-sm font-mono rounded-xl px-4 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#8B5CF6] transition">
                                                    <button type="submit" wire:loading.attr="disabled" class="px-4 py-2.5 bg-[#F59E0B] hover:bg-[#D97706] text-white font-bold text-xs rounded-xl transition whitespace-nowrap">
                                                        <span wire:loading.remove wire:target="saveSellingPrice({{ $group->id }})">ثبت</span>
                                                        <span wire:loading wire:target="saveSellingPrice({{ $group->id }})">...</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between text-[10px]">
                                                <span class="text-[#94A3B8]">سود هر فروش:</span>
                                                <span class="font-bold {{ $profitClass }} font-mono">{{ number_format(round($profit)) }} تومان</span>
                                            </div>
                                            @if(session("price_msg_{$group->id}"))
                                                <div class="text-[10px] text-[#10B981] font-bold flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    {{ session("price_msg_{$group->id}") }}
                                                </div>
                                            @endif
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-[#94A3B8] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <h4 class="text-sm font-bold text-[#F8FAFC]">سرویسی برای قیمت‌گذاری وجود ندارد</h4>
                                    <p class="text-xs text-[#94A3B8] mt-1">هیچ سرویس فعالی برای تنظیم قیمت فروش موجود نیست.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
