<div x-data="{ openAddTrx: false }">
    <div class="mb-6 flex justify-start">
        <a href="{{ route('admin.managers.list') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-bold text-zinc-400 hover:text-orange-500 bg-zinc-900/60 hover:bg-zinc-900 border border-zinc-800/80 px-4 py-2 rounded-xl transition-all shadow-md group">
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            بازگشت به لیست مدیران و نمایندگان
        </a>
    </div>

    <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800/60 rounded-3xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-inner relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-orange-500/5 rounded-full blur-[60px]"></div>

        <div class="flex items-center gap-5 relative z-10">
            <div class="relative">
                <img class="w-20 h-20 rounded-2xl object-cover border-2 border-zinc-700/50 shadow-lg" src="https://ui-avatars.com/api/?name={{ urlencode($manager->name) }}&background=27272a&color=f97316&size=128" alt="Avatar">
                @if($manager->is_active ?? true)
                    <span class="absolute -bottom-2 -right-2 w-5 h-5 bg-emerald-500 border-4 border-zinc-950 rounded-full"></span>
                @else
                    <span class="absolute -bottom-2 -right-2 w-5 h-5 bg-rose-500 border-4 border-zinc-950 rounded-full"></span>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-wide mb-1">{{ $manager->name }}</h1>
                <div class="flex items-center gap-3">
                    <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-bold border {{ $manager->role_css }}">
                        {{ $manager->role_label }}
                    </span>
                    <span class="text-xs text-zinc-500 font-mono">شناسه همکار: #{{ $manager->id }}</span>
                </div>
            </div>
        </div>

        <div class="relative z-10 flex gap-3 w-full md:w-auto">
            <button wire:click="toggleStatus" class="flex-1 md:flex-none px-5 py-2.5 font-bold text-sm rounded-xl transition border shadow-lg focus:outline-none {{ $manager->is_active ? 'bg-zinc-800 text-zinc-300 hover:text-red-400 hover:bg-red-500/10 border-zinc-700/50 hover:border-red-500/30' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white border-emerald-500/20' }}">
                <span wire:loading.remove wire:target="toggleStatus">
                    {{ $manager->is_active ? 'تعلیق موقت حساب' : 'فعال‌سازی مجدد حساب' }}
                </span>
                <span wire:loading wire:target="toggleStatus" class="animate-pulse">
                    در حال پردازش...
                </span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-5 shadow-lg">
            <p class="text-xs font-bold text-zinc-400 mb-1 uppercase tracking-wider">کل اکانت‌ها (واقعی)</p>
            <p class="text-2xl font-black text-white font-mono">{{ number_format($totalAccounts) }}</p>
        </div>
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-5 shadow-lg">
            <p class="text-xs font-bold text-zinc-400 mb-1 uppercase tracking-wider">اکانت‌های فعال (واقعی)</p>
            <p class="text-2xl font-black text-emerald-400 font-mono">{{ number_format($activeAccounts) }}</p>
        </div>
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-5 shadow-lg">
            <p class="text-xs font-bold text-zinc-400 mb-1 uppercase tracking-wider">موجودی فعلی حساب</p>
            <p class="text-xl font-black text-white font-mono">{{ number_format($balance) }} <span class="text-xs text-zinc-500 font-sans">تومان</span></p>
        </div>
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <p class="text-xs font-bold text-zinc-400 mb-1 uppercase tracking-wider">میزان بدهی به سیستم</p>
            <p class="text-xl font-black {{ $debt > 0 ? 'text-red-400' : 'text-zinc-500' }} font-mono relative z-10">{{ number_format($debt) }} <span class="text-xs font-sans">تومان</span></p>
            @if($debt > 0) <div class="absolute -left-10 -bottom-10 w-24 h-24 bg-red-500/10 rounded-full blur-2xl"></div> @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="space-y-6">
            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-2xl">
                <h2 class="text-base font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    ویرایش اطلاعات هویتی
                </h2>
                @if (session()->has('profile_message'))
                    <div class="p-4 mb-5 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">{{ session('profile_message') }}</div>
                @endif
                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-2">نام و نام خانوادگی</label>
                        <input wire:model="name" type="text" class="w-full bg-zinc-950/60 border border-zinc-700/80 rounded-xl text-white focus:ring-2 focus:ring-orange-500/50 block p-3 transition shadow-inner text-sm">
                        @error('name') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-2">شماره تماس (اجباری)</label>
                        <input wire:model="phone" type="text" dir="ltr" class="w-full bg-zinc-950/60 border border-zinc-700/80 rounded-xl text-white focus:ring-2 focus:ring-orange-500/50 block p-3 transition shadow-inner font-mono text-sm">
                        @error('phone') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-2">آدرس ایمیل</label>
                        <input wire:model="email" type="email" dir="ltr" class="w-full bg-zinc-950/60 border border-zinc-700/80 rounded-xl text-white focus:ring-2 focus:ring-orange-500/50 block p-3 transition shadow-inner font-mono text-sm">
                        @error('email') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 border-t border-zinc-800/80">
                        <label class="block text-xs font-bold text-zinc-400 mb-2">کلمه عبور جدید (اختیاری)</label>
                        <input wire:model="password" type="password" dir="ltr" placeholder="••••••••" class="w-full bg-zinc-950/60 border border-zinc-700/80 rounded-xl text-white focus:ring-2 focus:ring-orange-500/50 block p-3 transition shadow-inner text-sm">
                        @error('password') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" wire:loading.attr="disabled" class="w-full py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="updateProfile">ذخیره مشخصات همکار</span>
                            <span wire:loading wire:target="updateProfile" class="animate-pulse">درحال پردازش...</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
                <h2 class="text-sm font-bold text-white mb-4 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    شبکه فروش
                </h2>

                @if($manager->role === 'sub_agent')
                    <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-xl p-4 flex items-center gap-4">
                        <div class="p-3 bg-zinc-800 rounded-full text-zinc-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500 font-bold mb-1">نماینده بالادستی</p>
                            @if($parentAgent)
                                <a href="{{ route('admin.managers.edit', $parentAgent->id) }}" wire:navigate class="text-sm font-bold text-orange-400 hover:text-orange-300 transition">{{ $parentAgent->name }}</a>
                            @else
                                <span class="text-sm text-zinc-400">نامشخص</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-zinc-500 font-bold mb-1">تعداد زیر نمایندگان فعال</p>
                            <p class="text-lg font-black text-white mt-0.5">{{ $subAgentsCount }} <span class="text-xs text-zinc-500 font-medium">نفر</span></p>
                        </div>
                        <div class="p-2 bg-orange-500/10 text-orange-500 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">

            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 md:p-8 shadow-2xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            پیکربندی فروشگاه و دامنه
                        </h2>
                        <p class="text-xs text-zinc-500 mt-1">مدیریت اطلاعات ظاهری فروشگاه و وضعیت دامنه اختصاصی همکار</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($domain_status == 'approved')
                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold">
                                ✅ دامنه تایید شده
                            </span>
                        @elseif($domain_status == 'rejected')
                            <span class="px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold">
                                ❌ درخواست رد شده
                            </span>
                        @elseif($domain_status == 'pending')
                            <span class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold animate-pulse">
                                ⏳ در انتظار تایید
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-zinc-800 border border-zinc-700 text-zinc-400 text-xs font-bold">
                                🌐 درخواستی ثبت نشده
                            </span>
                        @endif

                        @if($store_is_active)
                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[11px] font-bold">
                                🛒 فروشگاه فعال
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[11px] font-bold">
                                🔒 فروشگاه بسته است
                            </span>
                        @endif
                    </div>
                </div>

                @if (session()->has('store_message'))
                    <div class="p-3 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">{{ session('store_message') }}</div>
                @endif
                @if (session()->has('store_info_message'))
                    <div class="p-3 text-xs text-blue-400 bg-blue-500/10 border border-blue-500/20 rounded-xl font-medium">{{ session('store_info_message') }}</div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div class="bg-zinc-950/50 p-5 rounded-2xl border border-zinc-800/80 space-y-4">
                        <h3 class="text-sm font-bold text-zinc-300 border-b border-zinc-800/80 pb-2">اطلاعات محتوایی فروشگاه</h3>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">عنوان فروشگاه (Title)</label>
                            <input wire:model="store_title" type="text" placeholder="مثلا: فروشگاه اختصاصی علی" class="w-full bg-zinc-900 border border-zinc-700/80 rounded-xl text-white text-xs p-3 focus:ring-2 focus:ring-orange-500/50 outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">آیدی پشتیبانی تلگرام (بدون @)</label>
                            <input wire:model="store_support_id" type="text" dir="ltr" placeholder="my_support" class="w-full bg-zinc-900 border border-zinc-700/80 rounded-xl text-white font-mono text-xs p-3 focus:ring-2 focus:ring-orange-500/50 outline-none">
                        </div>

                        <div class="pt-2 flex items-center gap-2">
                            <button wire:click="updateStoreInfo" class="flex-1 py-2.5 bg-zinc-800 hover:bg-orange-500 hover:text-white text-zinc-300 rounded-xl text-xs font-bold transition shadow-md">
                                ذخیره اطلاعات
                            </button>
                            <button wire:click="toggleStoreStatus" class="flex-1 py-2.5 rounded-xl font-bold text-xs border transition {{ $store_is_active ? 'bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white border-rose-500/20' : 'bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500 hover:text-white border-emerald-500/20' }}">
                                {{ $store_is_active ? 'غیرفعال‌سازی فروشگاه' : 'فعال‌سازی فروشگاه' }}
                            </button>
                        </div>
                    </div>

                    <div class="bg-zinc-950/50 p-5 rounded-2xl border border-zinc-800/80 space-y-4">
                        <h3 class="text-sm font-bold text-zinc-300 border-b border-zinc-800/80 pb-2">تنظیمات برند و دامنه</h3>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">نام برند (Brand Name)</label>
                            <input wire:model="brand_name" type="text" placeholder="نام برند در پنل" class="w-full bg-zinc-900 border border-zinc-700/80 rounded-xl text-white text-xs p-3 focus:ring-2 focus:ring-orange-500/50 outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">آدرس دامنه اختصاصی</label>
                            <div class="relative">
                                <input wire:model="custom_domain" type="text" dir="ltr" placeholder="shop.example.com" class="w-full bg-zinc-900 border border-zinc-700/80 rounded-xl text-white font-mono text-xs p-3 focus:ring-2 focus:ring-orange-500/50 outline-none">
                                <button wire:click="updateStoreDomain" class="absolute right-1 top-1 bottom-1 px-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-[10px] font-bold transition">
                                    ثبت
                                </button>
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">تعیین وضعیت تایید دامنه:</label>
                            <div class="flex items-center gap-2">
                                <button wire:click="rejectDomain" wire:confirm="آیا از رد درخواست اطمینان دارید؟" class="flex-1 py-2.5 bg-zinc-900 hover:bg-rose-500/10 text-rose-500 border border-zinc-700/50 rounded-xl text-xs font-bold transition">
                                    رد (Reject)
                                </button>
                                <button wire:click="approveDomain" class="flex-1 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/20 rounded-xl text-xs font-bold transition">
                                    تایید نهایی
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-2xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            مدیریت تراکنش‌ها و گردش مالی
                        </h2>
                        <p class="text-xs text-zinc-500 mt-1">امکان واریز، برداشت، فیلتر و بررسی تاریخچه مالی</p>
                    </div>
                    <button @click="openAddTrx = !openAddTrx" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-zinc-700/50 self-start sm:self-auto shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        افزودن تراکنش جدید
                    </button>
                </div>

                <div x-show="openAddTrx" x-collapse x-cloak class="bg-zinc-950/50 border border-zinc-800/80 p-5 rounded-xl space-y-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">ثبت واریز یا برداشت جدید برای حساب</h3>

                    @if (session()->has('trx_message'))
                        <div class="p-3 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">{{ session('trx_message') }}</div>
                    @endif

                    <form wire:submit.prevent="addTransaction" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">نوع تراکنش</label>
                            <select wire:model="newType" class="w-full bg-zinc-900 border border-zinc-700 text-zinc-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-orange-500">
                                <option value="plus">واریز / افزایش موجودی (plus)</option>
                                <option value="minus">برداشت / کاهش موجودی (minus)</option>
                                <option value="plus_amn">افزایش اعتبار اختصاصی (plus_amn)</option>
                                <option value="minus_amn">کاهش اعتبار اختصاصی (minus_amn)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">مبلغ تراکنش (تومان)</label>
                            <input wire:model="newPrice" type="number" placeholder="مثلا 50000" class="w-full bg-zinc-900 border border-zinc-700 text-white placeholder-zinc-600 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-orange-500">
                            @error('newPrice') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">توضیحات / بابت چه چیزی</label>
                            <input wire:model="newDescription" type="text" placeholder="شارژ پنل نمایندگی..." class="w-full bg-zinc-900 border border-zinc-700 text-white placeholder-zinc-600 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-orange-500">
                            @error('newDescription') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-3 text-left pt-2">
                            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-xs rounded-lg shadow-lg transition-all">ثبت نهایی تراکنش</button>
                        </div>
                    </form>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-3 bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60 shadow-inner">
                    <div class="relative w-full md:flex-1">
                        <input wire:model.live.debounce.300ms="trxSearch" type="text" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs rounded-lg pr-8 p-2.5 placeholder-zinc-600 focus:ring-1 focus:ring-orange-500" placeholder="جستجو در شرح تراکنش‌ها...">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-zinc-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <div class="w-full md:w-44">
                        <select wire:model.live="trxType" class="w-full bg-zinc-900 border border-zinc-800 text-zinc-400 text-xs rounded-lg p-2.5 cursor-pointer focus:ring-1 focus:ring-orange-500">
                            <option value="">همه تراکنش‌ها</option>
                            <option value="plus">واریزها (plus)</option>
                            <option value="minus">برداشت‌ها (minus)</option>
                            <option value="plus_amn">افزایش اعتبار</option>
                            <option value="minus_amn">کاهش اعتبار</option>
                        </select>
                    </div>
                </div>

                <div class="border border-zinc-800/80 rounded-xl overflow-hidden shadow-lg bg-zinc-900/20">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-zinc-950/80 text-zinc-400 font-bold border-b border-zinc-800/80">
                            <tr>
                                <th class="p-4">شرح تراکنش</th>
                                <th class="p-4">نوع</th>
                                <th class="p-4">وضعیت</th>
                                <th class="p-4">تاریخ ثبت</th>
                                <th class="p-4 text-left">مبلغ (تومان)</th>
                                <th class="p-4 text-center w-10">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                            @forelse($transactions as $trx)
                                <tr class="hover:bg-zinc-800/30 transition-colors {{ $editingTrxId === $trx->id ? 'bg-zinc-800/50' : '' }}">
                                    @if($editingTrxId === $trx->id)
                                        <td class="p-2">
                                            <input wire:model="editDescription" type="text" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-md text-xs p-1.5 focus:ring-1 focus:ring-orange-500">
                                        </td>
                                        <td class="p-2">
                                            <select wire:model="editType" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-md text-xs p-1.5 focus:ring-1 focus:ring-orange-500">
                                                <option value="plus">بستانکار (plus)</option>
                                                <option value="minus">بدهکار (minus)</option>
                                                <option value="plus_amn">بستانکار اعتبار</option>
                                                <option value="minus_amn">بدهکار اعتبار</option>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <select wire:model="editApproved" class="w-full bg-zinc-950 border border-zinc-700 text-white rounded-md text-xs p-1.5 focus:ring-1 focus:ring-orange-500">
                                                <option value="1">تایید شده</option>
                                                <option value="0">رد شده / معلق</option>
                                            </select>
                                        </td>
                                        <td class="p-4 text-zinc-500 font-mono text-[10px]">{{ \Morilog\Jalali\Jalalian::fromCarbon($trx->created_at)->format('Y/m/d H:i') }}</td>
                                        <td class="p-2 text-left">
                                            <input wire:model="editPrice" type="number" dir="ltr" class="w-full text-left bg-zinc-950 border border-zinc-700 text-white rounded-md text-xs p-1.5 focus:ring-1 focus:ring-orange-500">
                                        </td>
                                        <td class="p-2 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button wire:click="updateTransaction" title="ذخیره تغییرات" class="p-1.5 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-md transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                                <button wire:click="cancelEdit" title="لغو" class="p-1.5 bg-zinc-800 text-zinc-400 hover:bg-red-500 hover:text-white rounded-md transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    @else
                                        <td class="p-4 font-medium">{{ $trx->description ?? 'تراکنش سیستمی' }}</td>
                                        <td class="p-4">
                                            @if(in_array($trx->type, ['plus', 'plus_amn']))
                                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20 text-[10px]">بستانکار</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 font-bold border border-red-500/20 text-[10px]">بدهکار</span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            @if($trx->approved)
                                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20 text-[10px]">تایید شده</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded bg-zinc-800 text-zinc-400 font-bold border border-zinc-700 text-[10px]">رد/معلق</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-zinc-500 font-mono">{{ \Morilog\Jalali\Jalalian::fromCarbon($trx->created_at)->format('Y/m/d H:i') }}</td>
                                        <td class="p-4 text-left font-bold font-mono text-sm {{ in_array($trx->type, ['plus', 'plus_amn']) ? 'text-emerald-400' : 'text-red-400' }}" dir="ltr">
                                            {{ in_array($trx->type, ['plus', 'plus_amn']) ? '+' : '-' }}{{ number_format($trx->price) }}
                                        </td>
                                        <td class="p-4 text-center">
                                            <button wire:click="editTransaction({{ $trx->id }})" title="ویرایش تراکنش" class="p-1.5 bg-zinc-800 text-zinc-400 hover:text-orange-400 rounded-md border border-zinc-700/50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-zinc-500 font-medium">تراکنشی یافت نشد.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pt-2">
                    {{ $transactions->links() }}
                </div>
            </div>

            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 md:p-8 shadow-2xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            تنظیمات تخفیف و کارمزد زیر‌نمایندگان
                        </h2>
                        <p class="text-xs text-zinc-500 mt-1">تعیین درصد تخفیف خرید این همکار و درصد مارک‌آ‌پ روی قیمت فروش به زیرنمایندگانش</p>
                    </div>
                </div>

                @if (session()->has('discount_message'))
                    <div class="p-3 mb-4 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">
                        {{ session('discount_message') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateDiscount" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-2">درصد تخفیف روی گروه‌ها (%)</label>
                            <div class="relative">
                                <input wire:model="discount_percent" type="number" step="0.1" min="0" max="100" dir="ltr" class="w-full bg-zinc-950/60 border border-zinc-700/80 rounded-xl text-white focus:ring-2 focus:ring-orange-500/50 block p-3.5 pl-10 transition font-mono text-base font-bold">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500 font-bold">%</div>
                            </div>
                            @error('discount_percent') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-2">درصد سود روی زیر‌نمایندگان (Markup %)</label>
                            <div class="relative">
                                <input wire:model="sub_agent_markup" type="number" step="0.1" min="0" max="500" dir="ltr" class="w-full bg-zinc-950/60 border border-zinc-700/80 rounded-xl text-white focus:ring-2 focus:ring-orange-500/50 block p-3.5 pl-10 transition font-mono text-base font-bold">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500 font-bold">%</div>
                            </div>
                            @error('sub_agent_markup') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2 pt-2">
                            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
                                بروزرسانی درصد تخفیف و کارمزد
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 md:p-8 shadow-2xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            مدیریت دسترسی و نمایش گروه‌های سرویس
                        </h2>
                        <p class="text-xs text-zinc-500 mt-1">گروه‌هایی که غیرفعال یا مخفی شوند، در پنل این نماینده نمایش داده نخواهند شد.</p>
                    </div>
                </div>

                @if (session()->has('group_message'))
                    <div class="p-3 mb-4 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">
                        {{ session('group_message') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($allGroups as $group)
                        @php
                            $basePrice = $group->getFinalPriceFor($manager);
                            $discountPercent = $manager->discount_percent ?? 0;

                            $finalPrice = $basePrice;
                            if ($discountPercent > 0) {
                                $finalPrice = $basePrice - ($basePrice * $discountPercent / 100);
                            }

                            $isHidden = in_array($group->id, $hiddenGroups);
                        @endphp

                        <div class="bg-zinc-950/60 border {{ $isHidden ? 'border-red-500/40 bg-red-500/5' : 'border-zinc-800' }} rounded-xl p-4 flex flex-col justify-between transition-all gap-3">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="block text-sm font-bold text-white">{{ $group->name }}</span>
                                    @if($isHidden)
                                        <span class="text-[10px] bg-red-500/10 text-red-400 px-2 py-0.5 rounded font-bold">مخفی</span>
                                    @else
                                        <span class="text-[10px] bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded font-bold">فعال</span>
                                    @endif
                                </div>

                                <div class="mt-2 space-y-1">
                                    <div class="text-[11px] text-zinc-400 font-mono">
                                        قیمت نماینده: <span class="text-zinc-200">{{ number_format($basePrice) }}</span> تومان
                                    </div>

                                    @if($discountPercent > 0)
                                        <div class="text-[11px] text-orange-400 font-mono">
                                            با احتساب {{ $discountPercent }}% تخفیف:
                                            <span class="font-bold text-emerald-400 text-xs">{{ number_format(round($finalPrice)) }} تومان</span>
                                        </div>
                                    @else
                                        <div class="text-xs font-bold text-emerald-400 font-mono">
                                            قیمت نهایی: {{ number_format(round($finalPrice)) }} تومان
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-zinc-900">
                                <span class="text-[11px] text-zinc-500">وضعیت نمایش برای همکار</span>
                                <button wire:click="toggleGroupVisibility({{ $group->id }})" type="button" class="relative inline-flex items-center cursor-pointer">
                                    <div class="w-11 h-6 rounded-full transition-colors {{ $isHidden ? 'bg-red-600' : 'bg-zinc-700' }}"></div>
                                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $isHidden ? 'translate-x-5' : '' }}"></div>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
