<div class="space-y-6 pb-12 animate-fade-in">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">تنظیمات حساب کاربری</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">مدیریت امنیت حساب، تغییر رمز عبور و دستگاه‌های متصل</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">تغییر رمز عبور</h3>
                        <p class="text-[11px] text-zinc-500 mt-0.5">برای حفظ امنیت، رمز عبور قوی انتخاب کنید.</p>
                    </div>
                </div>

                @if (session()->has('success_password'))
                    <div class="p-3 mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success_password') }}
                    </div>
                @endif

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 mb-1.5">رمز عبور فعلی</label>
                        <input wire:model="current_password" type="password" class="w-full bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/50 font-mono-digit" dir="ltr">
                        @error('current_password') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 mb-1.5">رمز عبور جدید</label>
                        <input wire:model="password" type="password" class="w-full bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/50 font-mono-digit" dir="ltr">
                        @error('password') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 mb-1.5">تکرار رمز عبور جدید</label>
                        <input wire:model="password_confirmation" type="password" class="w-full bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/50 font-mono-digit" dir="ltr">
                    </div>

                    <button type="submit" class="w-full mt-2 px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 dark:text-zinc-900 text-white text-xs font-bold transition shadow-sm">
                        ذخیره رمز عبور جدید
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#111827] border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 shadow-sm">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">دستگاه‌های متصل</h3>
                            <p class="text-[11px] text-zinc-500 mt-0.5">مدیریت دستگاه‌هایی که هم‌اکنون به حساب شما دسترسی دارند.</p>
                        </div>
                    </div>

                    @if(count($sessions) > 1)
                        <button wire:click="logoutOtherBrowserSessions" wire:confirm="آیا مطمئن هستید؟ از تمام دستگاه‌های دیگر خارج خواهید شد." class="px-4 py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span>خروج از سایر دستگاه‌ها</span>
                        </button>
                    @endif
                </div>

                @if (session()->has('success_session'))
                    <div class="p-3 mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success_session') }}
                    </div>
                @endif
                @if (session()->has('error_session'))
                    <div class="p-3 mb-6 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold">
                        {{ session('error_session') }}
                    </div>
                @endif

                <div class="space-y-3">
                    @forelse($sessions as $session)
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 transition-colors">

                            <div class="w-12 h-12 rounded-xl bg-white dark:bg-zinc-800 shadow-sm flex items-center justify-center shrink-0 border border-zinc-100 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400">
                                @if($session->agent->is_desktop)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-zinc-900 dark:text-white truncate">
                                        {{ $session->agent->platform }} - {{ $session->agent->browser }}
                                    </h4>
                                    @if($session->is_current_device)
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-bold">دستگاه فعلی شما</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                                    <p class="text-[11px] text-zinc-500 font-mono-digit" dir="ltr">{{ $session->ip_address }}</p>
                                    <p class="text-[11px] text-zinc-400 flex items-center gap-1">
                                        <span class="w-1 h-1 rounded-full bg-zinc-300 dark:bg-zinc-600"></span>
                                        آخرین فعالیت: {{ str_replace('ago', 'پیش', $session->last_active) }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="p-8 text-center text-zinc-500 text-xs border border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
                            اطلاعاتی از نشست‌های فعال یافت نشد.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

    </div>
</div>
