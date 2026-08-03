<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">مدیریت مشتریان</h1>
            <p class="text-xs text-zinc-500 mt-1 font-medium">لیست مشتریان، مدیریت وضعیت و تغییر نماینده (ایجاد کننده)</p>
        </div>

        <button wire:click="create" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-[0_10px_20px_-10px_rgba(249,115,22,0.4)] transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            ثبت مشتری جدید
        </button>
    </div>

    <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800/60 p-4 rounded-2xl flex flex-col md:flex-row items-center gap-4 mb-6 shadow-inner">
        <div class="relative w-full">
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full bg-zinc-950/60 border border-zinc-800 text-zinc-300 text-sm rounded-xl focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 block p-3 placeholder-zinc-600 transition" placeholder="جستجوی نام، شماره، ایمیل یا یوزرنیم VPN...">
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    @if(count($selectedUsers) > 0)
        <div class="bg-zinc-800/50 p-4 rounded-xl mb-4 flex flex-col md:flex-row items-center gap-4 border border-zinc-700">
            <span class="text-sm font-bold text-orange-400 whitespace-nowrap">{{ count($selectedUsers) }} کاربر انتخاب شد</span>

            <div class="flex-1 flex flex-wrap items-center gap-2 w-full md:w-auto">
                <select wire:model.live="bulkAction" class="bg-zinc-950 border border-zinc-700 text-zinc-300 text-xs rounded-lg p-2 w-full md:w-auto focus:ring-orange-500 outline-none">
                    <option value="">انتخاب عملیات...</option>
                    <option value="activate">فعال‌سازی حساب</option>
                    <option value="deactivate">غیرفعال‌سازی</option>
                    <option value="change_creator">تغییر ایجاد کننده (نماینده)</option>
                    <option value="delete">حذف کاربران</option>
                </select>

                @if($bulkAction === 'change_creator')
                    <select wire:model="newCreatorId" class="bg-zinc-950 border border-zinc-700 text-orange-400 font-bold text-xs rounded-lg p-2 w-full md:w-auto focus:ring-orange-500 outline-none">
                        <option value="">انتخاب نماینده جدید...</option>
                        <option value="0">ثبت به نام مدیر کل (سایت)</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->role }})</option>
                        @endforeach
                    </select>
                @endif

                <button wire:click="executeBulkAction" class="px-5 py-2 bg-zinc-700 hover:bg-zinc-600 text-white font-bold text-xs rounded-lg transition-colors shadow">اجرا</button>
            </div>
        </div>
    @endif

    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden shadow-2xl pb-16">
        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-right border-collapse relative">
                <thead>
                <tr class="bg-zinc-950/80 border-b border-zinc-800/80 text-zinc-400 text-xs font-bold uppercase tracking-wider">
                    <th class="p-5 w-10">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-zinc-700 bg-zinc-950 text-orange-600 focus:ring-orange-500">
                    </th>
                    <th class="p-5">نام و ایجاد کننده</th>
                    <th class="p-5">اطلاعات تماس</th>
                    <th class="p-5 text-center">موجودی کیف پول</th> <th class="p-5">اکانت‌های متصل (VPN)</th>
                    <th class="p-5">وضعیت حساب</th>
                    <th class="p-5 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-sm">
                @forelse($users as $user)
                    <tr class="hover:bg-zinc-800/20 transition-colors">
                        <td class="p-4">
                            <input type="checkbox" wire:model.live="selectedUsers" value="{{ $user->id }}" class="rounded border-zinc-700 bg-zinc-950 text-orange-600 focus:ring-orange-500">
                        </td>

                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <img class="w-10 h-10 rounded-full object-cover border border-zinc-700/50" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=27272a&color=a1a1aa" alt="Avatar">
                                <div>
                                    <h2 class="font-bold text-white leading-none">{{ $user->name }}</h2>
                                    <span class="text-[11px] text-zinc-500 block mt-1">
                                        شناسه: #{{ $user->id }} |
                                        نماینده:
                                        @if($user->creator > 0)
                                            @php $agent = \App\Models\User::find($user->creator); @endphp
                                            @if($agent)
                                                <a href="{{ route('admin.managers.edit', $agent->id) }}" wire:navigate class="font-bold text-orange-400 hover:text-orange-300 transition-colors underline decoration-orange-400/30 underline-offset-4">
                                                    {{ $agent->name }}
                                                </a>
                                            @else
                                                <span class="font-bold text-zinc-400">نامشخص</span>
                                            @endif
                                        @else
                                            <span class="font-bold text-emerald-400">مدیر کل (سایت)</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="p-5 font-mono text-xs text-zinc-300">
                            @if($user->phone) <div class="text-zinc-300">{{ $user->phone }}</div> @endif
                            @if($user->email) <div class="text-zinc-500 mt-1">{{ $user->email }}</div> @endif
                        </td>

                        <td class="p-5 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-sm font-black font-mono-digit {{ $user->balance > 0 ? 'text-emerald-400' : 'text-zinc-500' }}" dir="ltr">
                                    {{ number_format($user->balance) }}
                                </span>
                                <span class="text-[9px] text-zinc-500 mt-0.5">تومان</span>
                            </div>
                        </td>

                        <td class="p-5 relative group">
                            @if($user->vpnAccounts->count() > 0)
                                <span class="inline-flex items-center justify-center px-3 py-1 bg-zinc-800 border border-zinc-700 text-orange-400 text-[11px] font-bold rounded-lg cursor-pointer hover:bg-zinc-700 transition shadow-sm">
                                    {{ $user->vpnAccounts->count() }} اکانت متصل
                                </span>

                                <div class="absolute right-10 top-full mt-2 w-56 bg-zinc-800 border border-zinc-700 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 p-2 space-y-1">
                                    <p class="text-[10px] text-zinc-500 mb-2 px-1">لیست اکانت‌ها (برای مشاهده کلیک کنید)</p>
                                    @foreach($user->vpnAccounts as $acc)
                                        <a href="{{ route('admin.accounts.show', $acc->id) }}" class="flex items-center justify-between p-2 bg-zinc-900/50 hover:bg-zinc-700 rounded-lg transition-colors border border-zinc-700/50">
                                            <span class="text-xs text-zinc-200 font-mono" dir="ltr">{{ $acc->username }}</span>
                                            <div class="flex items-center gap-1">
                                                <span class="text-[10px] {{ $acc->is_enabled ? 'text-emerald-400' : 'text-red-400' }}">{{ $acc->is_enabled ? 'فعال' : 'قطع' }}</span>
                                                <span class="w-2 h-2 rounded-full {{ $acc->is_enabled ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-[11px] text-zinc-600 bg-zinc-900 border border-zinc-800 px-2 py-1 rounded-md">بدون اکانت VPN</span>
                            @endif
                        </td>

                        <td class="p-5">
                            <label class="relative inline-flex items-center cursor-pointer" wire:click.prevent="toggleStatus({{ $user->id }})">
                                <input type="checkbox" class="sr-only peer" {{ $user->is_active ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 border border-zinc-700"></div>
                            </label>
                        </td>

                        <td class="p-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" wire:navigate title="مشاهده پروفایل مشتری" class="p-2 bg-zinc-800/60 hover:bg-zinc-700 text-zinc-400 hover:text-emerald-400 rounded-lg border border-zinc-700/50 transition inline-flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                <button wire:click="edit({{ $user->id }})" title="ویرایش اطلاعات پایه" class="p-2 bg-zinc-800/60 hover:bg-zinc-700 text-zinc-400 hover:text-orange-400 rounded-lg border border-zinc-700/50 transition inline-flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-zinc-500 font-medium">هیچ مشتری‌ای یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="bg-zinc-950/40 p-4 border-t border-zinc-800/60">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    @if($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/70 backdrop-blur-sm p-4">
            <div class="relative w-full max-w-2xl bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-white mb-6">{{ $userId ? 'ویرایش اطلاعات مشتری' : 'ثبت مشتری جدید' }}</h3>

                    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">نام کامل <span class="text-red-500">*</span></label>
                            <input wire:model="name" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-sm rounded-xl p-2.5 focus:ring-orange-500 outline-none">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">شماره تماس</label>
                            <input wire:model="phone" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-sm rounded-xl p-2.5 focus:ring-orange-500 outline-none font-mono" dir="ltr">
                            @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">ایمیل (نام کاربری ورود)</label>
                            <input wire:model="email" type="email" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-sm rounded-xl p-2.5 focus:ring-orange-500 outline-none font-mono" dir="ltr">
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-400 mb-1.5">رمز عبور ورود به پنل @if(!$userId) <span class="text-red-500">*</span> @endif</label>
                            <input wire:model="password" type="text" placeholder="{{ $userId ? 'خالی بگذارید تا تغییر نکند' : '' }}" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-sm rounded-xl p-2.5 focus:ring-orange-500 outline-none font-mono" dir="ltr">
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-1 md:col-span-2 mt-2">
                            <label class="block text-xs font-bold text-orange-400 mb-1.5">تخصیص مشتری به نماینده (ایجاد کننده)</label>
                            <select wire:model="creator" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-sm rounded-xl p-3 focus:ring-orange-500 outline-none">
                                <option value="0">ثبت به نام مدیر کل (سایت)</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->role }})</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-zinc-500 mt-1">با انتخاب یک نماینده، این مشتری به زیرمجموعه آن شخص منتقل خواهد شد.</p>
                        </div>

                        <div class="col-span-1 md:col-span-2 flex items-center mt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 border border-zinc-700"></div>
                                <span class="ms-3 text-sm font-bold text-zinc-300">مجاز به ورود به سیستم (وضعیت حساب)</span>
                            </label>
                        </div>

                        <div class="col-span-1 md:col-span-2 mt-4 flex items-center justify-end gap-3 pt-4 border-t border-zinc-800">
                            <button type="button" wire:click="resetForm" class="px-5 py-2.5 text-sm font-bold text-zinc-400 hover:text-white bg-zinc-800 hover:bg-zinc-700 rounded-xl transition shadow">لغو</button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-orange-600 hover:bg-orange-500 rounded-xl shadow-lg shadow-orange-500/20 transition">ذخیره اطلاعات مشتری</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
