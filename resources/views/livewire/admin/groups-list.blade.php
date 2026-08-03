<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">گروه‌های کاربری و بسته‌های فروش</h1>
            <p class="text-xs text-zinc-500 mt-1">امکان تعریف تعرفه، اولویت‌بندی با کشیدن و رها کردن (Drag & Drop)</p>
        </div>

        <button wire:click="$set('isFormOpen', true)" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            ایجاد گروه تعرفه جدید
        </button>
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div wire:sortable="updateOrder" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($groups as $group)
            <div wire:sortable.item="{{ $group->id }}" wire:key="group-{{ $group->id }}" class="bg-zinc-900/40 border {{ $group->is_enabled ? 'border-zinc-700/80' : 'border-red-900/30' }} rounded-3xl p-5 relative shadow-lg group hover:bg-zinc-800/40 transition-all flex flex-col">

                <div wire:sortable.handle class="absolute top-4 left-4 cursor-grab active:cursor-grabbing p-1.5 bg-zinc-800 rounded-md text-zinc-500 hover:text-orange-500 hover:bg-zinc-700 transition opacity-50 group-hover:opacity-100" title="برای جابجایی بکشید">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                </div>

                <div class="mb-4 pr-2">
                    <span class="px-2 py-0.5 rounded bg-zinc-800 text-zinc-400 text-[10px] font-mono border border-zinc-700 mb-2 inline-block">ID: {{ $group->id }}</span>
                    <h3 class="text-orange-500 font-black text-lg truncate">{{ $group->name }}</h3>
                </div>

                <div class="flex justify-between items-center bg-zinc-950 p-3 rounded-xl mb-4 border border-zinc-800/80 shadow-inner">
                    <div class="text-right">
                        <span class="block text-[10px] font-bold text-zinc-500 mb-0.5">قیمت مصرف‌کننده</span>
                        <span class="font-bold text-white font-mono">{{ number_format($group->price) }} <span class="text-[9px] font-sans text-zinc-500">تومان</span></span>
                    </div>
                    <div class="w-px h-8 bg-zinc-800"></div>
                    <div class="text-left">
                        <span class="block text-[10px] font-bold text-emerald-600/70 mb-0.5">قیمت نماینده</span>
                        <span class="font-bold text-emerald-400 font-mono">{{ number_format($group->price_reseler) }} <span class="text-[9px] font-sans text-emerald-600/50">تومان</span></span>
                    </div>
                </div>

                <div class="space-y-2 mb-6 flex-grow">
                    <div class="flex justify-between items-center text-xs border-b border-zinc-800/50 pb-2">
                        <span class="text-zinc-500">ترافیک مجاز</span>
                        <span class="font-bold text-zinc-300 font-mono" dir="ltr">{{ $group->group_volume > 0 ? number_format($group->group_volume) . ' GB' : 'نامحدود' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs border-b border-zinc-800/50 pb-2">
                        <span class="text-zinc-500">مدت اعتبار</span>
                        <span class="font-bold text-zinc-300 font-mono" dir="ltr">{{ $group->expire_value }} {{ $group->expire_type }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs pb-1">
                        <span class="text-zinc-500">محدودیت کاربر</span>
                        <span class="font-bold text-zinc-300 font-mono" dir="ltr">{{ $group->multi_login }} کاربره</span>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-auto pt-4 border-t border-zinc-800/80">
                    <button wire:click="toggleStatus({{ $group->id }})" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out border {{ $group->is_enabled ? 'bg-emerald-500 border-emerald-600' : 'bg-zinc-800 border-zinc-700' }}">
                        <span class="pointer-events-none rounded-full bg-white shadow-md h-3.5 w-3.5 transition-all duration-200 ease-in-out absolute top-[2px] {{ $group->is_enabled ? 'left-[2px]' : 'right-[2px]' }}"></span>
                    </button>

                    <button wire:click="edit({{ $group->id }})" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-orange-400 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 border border-zinc-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        ویرایش
                    </button>
                </div>

            </div>
        @empty
            <div class="col-span-full p-12 text-center border-2 border-dashed border-zinc-800/80 rounded-3xl">
                <p class="text-zinc-500 font-bold mb-3">هیچ پکیج یا گروه کاربری ثبت نشده است.</p>
                <button wire:click="$set('isFormOpen', true)" class="text-orange-500 hover:text-orange-400 text-sm font-bold underline">اولین گروه را بسازید</button>
            </div>
        @endforelse
    </div>

    @if($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/70 backdrop-blur-sm transition-opacity animate-fade-in">
            <div class="relative w-full max-w-4xl bg-zinc-950 border border-zinc-800/80 rounded-3xl shadow-2xl flex flex-col max-h-[95vh] overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-800/80 bg-zinc-900/50">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ $groupId ? 'ویرایش مشخصات گروه' : 'تعریف گروه کاربری جدید' }}
                    </h2>
                    <button wire:click="resetForm" class="p-2 text-zinc-500 hover:text-red-500 hover:bg-red-500/10 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form wire:submit.prevent="save" class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 bg-zinc-900/30 p-5 rounded-2xl border border-zinc-800/50">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-zinc-400 mb-2">نام گروه / پکیج (نمایش به کاربر)</label>
                                <input wire:model="name" type="text" placeholder="مثال: حجمی - 15 گیگابایت - ۱ ماهه" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500">
                                @error('name') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-emerald-500 mb-2">قیمت برای نماینده (تومان)</label>
                                <input wire:model="price_reseler" type="number" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-emerald-400 p-3 focus:ring-1 focus:ring-emerald-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-orange-500 mb-2">قیمت فروش عادی (تومان)</label>
                                <input wire:model="price" type="number" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500 font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 bg-zinc-900/30 p-5 rounded-2xl border border-zinc-800/50">
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">پروفایل شارژ (Charge Role)</label>
                                <select wire:model="charge_id" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500">
                                    <option value="">انتخاب پروفایل...</option>
                                    @foreach($charges as $charge)
                                        <option value="{{ $charge->id }}">{{ $charge->name }} (ID: {{ $charge->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">نوع گروه</label>
                                <select wire:model="group_type" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500">
                                    <option value="expire">اعتباری (Expire)</option>
                                    <option value="volume">حجمی (Volume)</option>
                                    <option value="both">هر دو</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">ترافیک مجاز (GB/MB)</label>
                                <input wire:model="group_volume" type="number" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500 font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 bg-zinc-900/30 p-5 rounded-2xl border border-zinc-800/50">
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">واحد زمان اعتبار</label>
                                <select wire:model="expire_type" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500">
                                    <option value="no_expire">بدون انقضا</option>
                                    <option value="minutes">دقیقه (Minutes)</option>
                                    <option value="hours">ساعت (Hours)</option>
                                    <option value="days">روز (Days)</option>
                                    <option value="months">ماه (Months)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">مقدار زمان اعتبار</label>
                                <input wire:model="expire_value" type="number" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">محدودیت اتصالات همزمان</label>
                                <input wire:model="multi_login" type="number" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500 font-mono">
                            </div>

                            <div class="md:col-span-3 flex items-center gap-6 mt-2 pt-4 border-t border-zinc-800/50">
                                <label class="flex items-center gap-2 text-sm font-bold text-zinc-300 cursor-pointer">
                                    <input type="checkbox" wire:model="first_login" class="rounded border-zinc-700 bg-zinc-950 text-orange-500 focus:ring-orange-500 w-5 h-5">
                                    شروع اعتبار از اولین اتصال (First Login)
                                </label>
                                <label class="flex items-center gap-2 text-sm font-bold text-zinc-300 cursor-pointer">
                                    <input type="checkbox" wire:model="is_enabled" class="rounded border-zinc-700 bg-zinc-950 text-emerald-500 focus:ring-emerald-500 w-5 h-5">
                                    وضعیت بسته فعال باشد
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="px-6 py-4 border-t border-zinc-800/80 bg-zinc-900/50 flex items-center gap-3 justify-end">
                    <button wire:click="resetForm" class="px-6 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-sm rounded-xl transition-all">انصراف</button>
                    <button wire:click="save" class="px-8 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all">ذخیره گروه</button>
                </div>

            </div>
        </div>
    @endif
</div>
