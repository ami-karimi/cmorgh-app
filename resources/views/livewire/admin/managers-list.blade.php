<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">مدیریت مدیران و نمایندگان</h1>
            <p class="text-xs text-zinc-500 mt-1 font-medium">لیست، تغییر سطح دسترسی و مانیتورینگ وضعیت حساب همکاران سیستم</p>
        </div>

        <a href="#" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-[0_10px_20px_-10px_rgba(249,115,22,0.4)] transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            ایجاد مدیر / نماینده جدید
        </a>
    </div>

    <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800/60 p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 mb-6 shadow-inner">
        <div class="relative w-full md:w-96">
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full bg-zinc-950/60 border border-zinc-800 text-zinc-300 text-sm rounded-xl focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 block pr-10 p-3 shadow-inner placeholder-zinc-600 transition" placeholder="جستجو بر اساس نام، شماره یا ایمیل...">
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div class="w-full md:w-48">
            <select wire:model.live="roleFilter" class="w-full bg-zinc-950/60 border border-zinc-800 text-zinc-400 text-sm rounded-xl focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 block p-3 transition cursor-pointer">
                <option value="">همه نقش‌ها</option>
                <option value="manager">مدیران کل (Manager)</option>
                <option value="admin">مدیر (Admin)</option>
                <option value="agent">نماینده فروش (Agent)</option>
                <option value="sub_agent">زیر نماینده فروش (Sub Agent)</option>
            </select>
        </div>


    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if(count($selectedManagers) > 0)
        <div class="bg-zinc-800/50 p-4 rounded-xl mb-4 flex items-center justify-between border border-zinc-700">
            <span class="text-sm font-bold text-zinc-300">{{ count($selectedManagers) }} مورد انتخاب شد</span>
            <div class="flex gap-2">
                <button wire:click="bulkAction('activate')" class="px-3 py-1.5 text-xs bg-emerald-600/20 text-emerald-400 rounded-lg hover:bg-emerald-600/30">فعال‌سازی</button>
                <button wire:click="bulkAction('deactivate')" class="px-3 py-1.5 text-xs bg-zinc-700 text-zinc-300 rounded-lg hover:bg-zinc-600">غیرفعال‌سازی</button>
                <button wire:click="bulkAction('delete')" class="px-3 py-1.5 text-xs bg-red-600/20 text-red-400 rounded-lg hover:bg-red-600/30" onclick="confirm('آیا مطمئن هستید؟') || event.stopImmediatePropagation()">حذف</button>
            </div>
        </div>
    @endif

    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                <tr class="bg-zinc-950/80 border-b border-zinc-800/80 text-zinc-400 text-xs font-bold uppercase tracking-wider">
                    <th class="p-5 w-10">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-zinc-700 bg-zinc-950 text-orange-600 focus:ring-orange-500">
                    </th>
                    <th class="p-5">نام و مشخصات</th>
                    <th class="p-5">اطلاعات تماس</th>
                    <th class="p-5">نقش کاربری</th>
                    <th class="p-5">وضعیت حساب</th>
                    <th class="p-5">تاریخ ثبت‌نام</th>
                    <th class="p-5">موجودی حساب</th>
                    <th class="p-5">آخرین فعالیت</th>
                    <th class="p-5 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-sm">
                @forelse($managers as $user)
                    <tr class="hover:bg-zinc-800/20 transition-colors">
                        <td class="p-4">
                            <input type="checkbox" wire:model.live="selectedManagers" value="{{ $user->id }}" class="rounded border-zinc-700 bg-zinc-950 text-orange-600 focus:ring-orange-500">
                        </td>
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <img class="w-10 h-10 rounded-full object-cover border border-zinc-700/50" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=27272a&color=a1a1aa" alt="Avatar">
                                <div>
                                    <h2 class="font-bold text-white leading-none">{{ $user->name }}</h2>
                                    <span class="text-xs text-zinc-500 block mt-1">شناسه: #{{ $user->id }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="p-5 font-mono text-xs text-zinc-300">
                            <div class="space-y-1" dir="ltr">
                                <div class="flex items-center justify-end gap-1.5">
                                    <span>{{ $user->phone }}</span>
                                    <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                @if($user->email)
                                    <div class="flex items-center justify-end gap-1.5 text-zinc-500">
                                        <span>{{ $user->email }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="p-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border transition-colors {{ $user->role_css }}">{{ $user->role_label }}</span>
                        </td>

                        <td class="p-5">
                            <button wire:click="toggleStatus({{ $user->id }})" class="focus:outline-none group">
                                @if($user->is_active ?? true)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 group-hover:bg-emerald-500/20 transition">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> فعال
                                        </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-zinc-800 text-zinc-500 border border-zinc-700 group-hover:bg-zinc-700 transition">
                                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-500"></span> مسدود
                                        </span>
                                @endif
                            </button>
                        </td>

                        <td class="p-5 text-xs text-zinc-400 font-medium">
                            {{ $user->created_at_shamsi }}
                        </td>
                        <td class="p-5 text-xs text-zinc-400 font-medium">
                            {{ $user->formatted_balance }}
                        </td>
                        <td class="p-5 text-xs text-zinc-400 font-medium">
                            {{ $user->last_login_for_humans }}
                        </td>

                        <td class="p-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="loginAs({{ $user->id }})" title="ورود به پنل این نماینده" class="p-2 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-400 hover:text-white rounded-lg border border-emerald-500/20 transition inline-flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                </button>

                                <a href="{{ route('admin.managers.edit', $user->id) }}" wire:navigate title="مشاهده و ویرایش پروفایل" class="p-2 bg-zinc-800/60 hover:bg-zinc-700 text-zinc-400 hover:text-orange-400 rounded-lg border border-zinc-700/50 transition inline-flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-10 text-center text-zinc-500 font-medium">
                            هیچ مدیر یا نماینده‌ای با مشخصات وارد شده پیدا نشد.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($managers->hasPages())
            <div class="bg-zinc-950/40 p-4 border-t border-zinc-800/60">
                {{ $managers->links() }}
            </div>
        @endif
    </div>
</div>
