<div wire:key="account-manager-wrapper">


    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">مدیریت اکانت‌های کاربران</h1>
            <p class="text-xs text-zinc-500 mt-1">مدیریت اتصالات، ترافیک، فیلترهای پیشرفته و عملیات گروهی</p>
        </div>
        <button wire:click="$set('isFormOpen', true)" class="px-5 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            صدور اکانت جدید
        </button>
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-6 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl font-medium">{{ session('error') }}</div>
    @endif

    <div class="mb-6" wire:key="tools-section">

        @if(count($selectedAccounts) == 0)
            <div wire:key="filters-box" x-data="{ showAdvanced: false }" class="bg-zinc-900/40 border border-zinc-800/80 p-4 md:p-5 rounded-3xl shadow-sm transition-all">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative">
                        <input wire:model.live.debounce.300ms="search" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-xl pr-11 p-3.5 focus:ring-1 focus:ring-orange-500 placeholder-zinc-600" placeholder="جستجو (یوزرنیم، نام، موبایل)...">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-zinc-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <div class="flex gap-3 w-full md:w-auto">
                        <select wire:model.live="filterStatus" class="flex-1 md:w-48 bg-zinc-950 border border-zinc-800 text-zinc-300 text-sm rounded-xl p-3.5 focus:ring-1 focus:ring-orange-500 cursor-pointer">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="online">آنلاین‌ها</option>
                            <option value="offline">آفلاین‌ها</option>
                            <option value="enabled">فعال‌ها</option>
                            <option value="disabled">مسدودها</option>
                            <option value="expired">منقضی‌شده‌ها</option>
                        </select>
                        <button @click="showAdvanced = !showAdvanced" class="md:hidden flex items-center justify-center p-3.5 bg-zinc-800 border border-zinc-700 text-orange-500 rounded-xl hover:bg-zinc-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        </button>
                    </div>
                </div>

                <div :class="showAdvanced ? 'grid' : 'hidden md:grid'" class="grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mt-4 pt-4 border-t border-zinc-800/50">
                    <select wire:model.live="filterGroup" class="bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs rounded-xl p-3 focus:ring-1 focus:ring-orange-500">
                        <option value="">گروه کاربری...</option>
                        @foreach($groups as $grp) <option value="{{ $grp->id }}" wire:key="f-grp-{{ $grp->id }}">{{ $grp->name }}</option> @endforeach
                    </select>

                    <div x-data="{
    open: false,
    search: '',
    selectedName: 'سازنده / نماینده...',
    // لود کردن نام و آیدی نمایندگان در جاوااسکریپت برای سرچ فوق‌سریع
    options: [
        @foreach($creators as $creator)
                        { id: '{{ $creator->id }}', name: '{{ addslashes($creator->name) }}' },
        @endforeach
                        ],
                        get filteredOptions() {
                            if (this.search === '') return this.options;
                            return this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        selectOption(id, name) {
                            this.selectedName = name;
                            this.open = false;
                            // وقتی کاربر کلیک کرد، فقط آیدی به بک‌اند ارسال می‌شود
                            $wire.set('filterCreator', id);
                        }
                    }" class="relative w-full md:w-48" wire:ignore>

                        <button @click="open = !open" type="button" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs rounded-xl p-3 flex justify-between items-center focus:ring-1 focus:ring-orange-500 transition-colors">
                            <span x-text="selectedName" class="truncate"></span>
                            <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition.opacity.duration.200ms class="absolute z-50 w-full mt-2 bg-zinc-900 border border-zinc-700 rounded-xl shadow-2xl overflow-hidden" style="display: none;">

                            <div class="p-2 border-b border-zinc-700 bg-zinc-900/90 backdrop-blur sticky top-0">
                                <input x-model="search" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white text-xs rounded-lg p-2.5 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 placeholder-zinc-600" placeholder="جستجوی نام نماینده...">
                            </div>

                            <div class="overflow-y-auto p-1 scrollbar-thin scrollbar-thumb-zinc-700 scrollbar-track-transparent" style="max-height: 220px;">

                                <div @click="selectOption('', 'همه نمایندگان...')" class="px-3 py-2.5 text-xs text-zinc-400 hover:bg-zinc-800 hover:text-white rounded-lg cursor-pointer transition-colors mb-1">
                                    همه نمایندگان...
                                </div>

                                <template x-for="option in filteredOptions" :key="option.id">
                                    <div @click="selectOption(option.id, option.name)" class="px-3 py-2.5 text-xs text-zinc-300 hover:bg-orange-500/10 hover:text-orange-400 rounded-lg cursor-pointer transition-colors flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span x-text="option.name"></span>
                                    </div>
                                </template>

                                <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-xs text-zinc-500 font-bold">
                                    نماینده‌ای یافت نشد!
                                </div>

                            </div>
                        </div>
                    </div>

                    <div wire:ignore x-data x-init="
    $($refs.dateFrom).persianDatepicker({
        format: 'YYYY/MM/DD',
        initialValue: false,
        autoClose: true,
        persianDigit: false,
        cssClass: 'persian-datepicker-cheetah',
        onSelect: function(unix){
            $wire.set('filterDateFrom', $refs.dateFrom.value);
        }
    });
">
                        <input x-ref="dateFrom" type="text" readonly placeholder="ثبت از تاریخ" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-xs rounded-xl p-3 focus:ring-1 focus:ring-orange-500 font-mono text-center cursor-pointer">
                    </div>


                    <div wire:ignore x-data x-init="
    $($refs.dateTo).persianDatepicker({
        format: 'YYYY/MM/DD',
        initialValue: false,
        autoClose: true,
        persianDigit: false,
        cssClass: 'persian-datepicker-cheetah',
        onSelect: function(unix){
            $wire.set('filterDateTo', $refs.dateTo.value);
        }
    });
">
                        <input x-ref="dateTo" type="text" readonly placeholder="ثبت تا تاریخ" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 text-xs rounded-xl p-3 focus:ring-1 focus:ring-orange-500 font-mono text-center cursor-pointer">
                    </div>

                    <select wire:model.live="perPage" class="bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs rounded-xl p-3 focus:ring-1 focus:ring-orange-500 font-mono">
                        <option value="10">10 رکورد در صفحه</option>
                        <option value="25">25 رکورد در صفحه</option>
                        <option value="50">50 رکورد در صفحه</option>
                        <option value="100">100 رکورد در صفحه</option>
                    </select>
                </div>
            </div>

        @else
            <div wire:key="bulk-actions-box" class="bg-orange-500/10 border border-orange-500/30 p-4 md:p-5 rounded-3xl shadow-[0_0_20px_rgba(249,115,22,0.15)] flex flex-col lg:flex-row items-center gap-4 transition-all">

                <div class="flex items-center gap-3 w-full lg:w-auto px-4 py-3 lg:py-2 bg-orange-500/20 text-orange-400 rounded-xl text-sm font-black justify-center border border-orange-500/20">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse shadow-[0_0_8px_rgba(249,115,22,0.8)]"></span>
                    {{ count($selectedAccounts) }} کاربر انتخاب شده
                </div>

                <div class="flex-1 w-full flex flex-col md:flex-row items-center gap-3">
                    <select wire:model.live="bulkAction" class="w-full md:w-64 bg-zinc-950 border border-orange-500/40 text-white text-sm rounded-xl p-3.5 md:p-3 focus:ring-1 focus:ring-orange-500 cursor-pointer shadow-inner">
                        <option value="">انتخاب عملیات گروهی...</option>
                        <option value="enable">فعال‌سازی حساب‌ها</option>
                        <option value="disable">مسدود / غیرفعال‌سازی</option>
                        <option value="recharge">شارژ مجدد و صفر کردن مصرف</option>
                        <option value="add_days">افزودن روز (تمدید زمانی)</option>
                        <option value="reduce_days">کسر روز (کاهش زمانی)</option>
                        <option value="add_volume">افزودن حجم (ترافیک اضافه)</option>
                        <option value="reduce_volume">کسر حجم (کاهش ترافیک)</option>
                        <option value="change_group">تغییر گروه کاربری</option>
                        <option value="change_creator">تغییر سازنده / نماینده</option>
                        <option value="set_expire">تنظیم تاریخ انقضای دستی</option>
                        <option value="delete">حذف کامل حساب‌ها</option>
                    </select>

                    <div class="flex-1 w-full" wire:key="dynamic-bulk-inputs">
                        @if($bulkAction === 'change_group')
                            <select wire:model="bulkGroupId" wire:key="in-grp" class="w-full md:w-64 bg-zinc-900 border border-zinc-700 text-white text-sm rounded-xl p-3.5 md:p-3 animate-fade-in">
                                <option value="">انتخاب گروه جدید...</option>
                                @foreach($groups as $grp) <option value="{{ $grp->id }}" wire:key="o-grp-{{ $grp->id }}">{{ $grp->name }}</option> @endforeach
                            </select>
                        @elseif($bulkAction === 'change_creator')
                            <select wire:model="bulkCreatorId" wire:key="in-crt" class="w-full md:w-64 bg-zinc-900 border border-zinc-700 text-white text-sm rounded-xl p-3.5 md:p-3 animate-fade-in">
                                <option value="">انتخاب نماینده جدید...</option>
                                @foreach($creators as $creator) <option value="{{ $creator->id }}" wire:key="o-crt-{{ $creator->id }}">{{ $creator->name }}</option> @endforeach
                            </select>
                        @elseif($bulkAction === 'set_expire')
                            <input wire:model="bulkExpireDate" wire:key="in-exp" type="date" dir="ltr" class="w-full md:w-64 bg-zinc-900 border border-zinc-700 text-white text-sm rounded-xl p-3.5 md:p-3 font-mono animate-fade-in">
                        @elseif($bulkAction === 'add_days')
                            <input wire:model="bulkAddDays" wire:key="in-add-d" type="number" dir="ltr" placeholder="مثلاً: 30 (روز)" class="w-full md:w-64 bg-zinc-900 border border-zinc-700 text-white text-sm rounded-xl p-3.5 md:p-3 font-mono animate-fade-in">
                        @elseif($bulkAction === 'reduce_days')
                            <input wire:model="bulkReduceDays" wire:key="in-red-d" type="number" dir="ltr" placeholder="مثلاً: 7 (روز)" class="w-full md:w-64 bg-zinc-900 border border-zinc-700 text-white text-sm rounded-xl p-3.5 md:p-3 font-mono animate-fade-in">
                        @elseif($bulkAction === 'add_volume')
                            <input wire:model="bulkAddVolume" wire:key="in-add-v" type="number" step="0.1" dir="ltr" placeholder="حجم اضافه (GB)" class="w-full md:w-64 bg-zinc-900 border border-zinc-700 text-white text-sm rounded-xl p-3.5 md:p-3 font-mono animate-fade-in">
                        @elseif($bulkAction === 'reduce_volume')
                            <input wire:model="bulkReduceVolume" wire:key="in-red-v" type="number" step="0.1" dir="ltr" placeholder="حجم کسر (GB)" class="w-full md:w-64 bg-zinc-900 border border-zinc-700 text-white text-sm rounded-xl p-3.5 md:p-3 font-mono animate-fade-in">
                        @endif
                    </div>

                    <div class="flex items-center gap-3 w-full lg:w-auto justify-between lg:justify-end mt-2 lg:mt-0">
                        <button wire:click="$set('selectedAccounts', [])" class="px-4 py-2 text-zinc-400 hover:text-white text-xs transition underline whitespace-nowrap">انصراف و لغو</button>
                        @if($bulkAction)
                            <button wire:click="executeBulkAction" onclick="confirm('آیا از اعمال این تغییرات گروهی مطمئن هستید؟') || event.stopImmediatePropagation()" class="flex-1 lg:flex-none px-6 py-3.5 md:py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all whitespace-nowrap">
                                اجرای تغییرات
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl overflow-hidden shadow-2xl relative" wire:key="accounts-table-container">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-zinc-950/80 text-zinc-400 font-bold border-b border-zinc-800/80">
                <tr>
                    <th class="p-4 w-10 text-center">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-zinc-700 bg-zinc-900 text-orange-500 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                    </th>
                    <th class="p-4 pl-2">مشخصات کاربر</th>
                    <th class="p-4 w-48">مصرف ترافیک</th>
                    <th class="p-4">اعتبار و انقضا</th>
                    <th class="p-4">سرویس / سازنده</th>
                    <th class="p-4 text-center">وضعیت</th>
                    <th class="p-4 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                @forelse($accounts as $acc)
                    <tr wire:key="acc-row-{{ $acc->id }}" class="hover:bg-zinc-800/30 transition-colors {{ in_array($acc->id, $selectedAccounts) ? 'bg-orange-500/5' : '' }}">

                        <td class="p-4 text-center">
                            <input type="checkbox" wire:key="chk-{{ $acc->id }}" wire:model.live="selectedAccounts" value="{{ (string)$acc->id }}" class="rounded border-zinc-700 bg-zinc-900 text-orange-500 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                        </td>

                        <td class="p-4 pl-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-orange-500 font-bold uppercase shadow-sm">
                                    {{ substr($acc->username, 0, 2) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <a href="{{ route('admin.accounts.show', $acc->id) }}"
                                           wire:navigate
                                           class="font-bold text-white text-sm tracking-wide hover:text-orange-500 transition-colors duration-150"
                                           dir="ltr">
                                            {{ $acc->username }}
                                        </a>

                                        @if($acc->is_online)
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> آنلاین
        </span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded bg-zinc-800 text-zinc-500 border border-zinc-700 text-[9px] font-bold">آفلاین</span>
                                        @endif
                                    </div>
                                    @if($acc->name)
                                        <span class="text-[10px] text-orange-400/80 font-bold block mb-0.5">{{ $acc->name }}</span>
                                    @endif
                                    <span class="text-[10px] text-zinc-500 flex items-center gap-1 font-mono">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                            {{ $acc->password }}
                                        </span>
                                </div>
                            </div>
                        </td>

                        <td class="p-4">
                            @php
                                $usagePercent = $acc->max_usage > 0 ? min(100, round(($acc->usage / $acc->max_usage) * 100)) : 0;
                                $barColor = $usagePercent > 90 ? 'bg-red-500' : ($usagePercent > 70 ? 'bg-orange-500' : 'bg-emerald-500');
                            @endphp
                            <div class="flex justify-between items-end mb-1.5 text-[10px]">
                                <span class="text-zinc-400">{{ $acc->formatBytes($acc->usage) }}</span>
                                <span class="font-mono text-zinc-500">{{ $acc->max_usage == 0 ? 'نامحدود' : $acc->formatBytes($acc->max_usage) }}</span>
                            </div>
                            <div class="w-full bg-zinc-800 rounded-full h-1.5 overflow-hidden border border-zinc-700/50">
                                <div class="{{ $barColor }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $acc->max_usage == 0 ? 100 : $usagePercent }}%"></div>
                            </div>
                        </td>

                        <td class="p-4">
                                <span class="px-2 py-0.5 bg-zinc-800 border border-zinc-700 rounded-md text-[10px] text-zinc-300 font-bold mb-1.5 inline-block">
                                    {{ $acc->group->name ?? 'بدون گروه' }}
                                </span>
                            <div class="text-[10px] font-bold">
                                @if($acc->expired)
                                    <span class="text-red-500 bg-red-500/10 px-1.5 py-0.5 rounded">منقضی شده</span>
                                @elseif($acc->expire_date)
                                    @php $daysLeft = now()->diffInDays($acc->expire_date, false); @endphp
                                    @if($daysLeft > 0)
                                        <span class="{{ $daysLeft < 3 ? 'text-orange-400' : 'text-emerald-400' }}">{{ floor($daysLeft) }} روز مانده</span>
                                    @elseif($daysLeft == 0)
                                        <span class="text-orange-500 animate-pulse">امروز منقضی می‌شود</span>
                                    @else
                                        <span class="text-red-500">منقضی شده</span>
                                    @endif
                                    <span class="block text-zinc-500 text-[9px] mt-0.5 font-mono" dir="ltr">
                                            {{ \Morilog\Jalali\Jalalian::forge($acc->expire_date)->format('Y/m/d - H:i') }}
                                        </span>
                                @else
                                    <span class="text-zinc-500">شروع نشده</span>
                                @endif
                            </div>
                        </td>

                        <td class="p-4">
                                <span class="px-2 py-0.5 bg-zinc-900 border border-zinc-700 rounded-md text-[10px] text-zinc-400 font-mono uppercase tracking-wider mb-1.5 inline-block">
                                    {{ str_replace('_', ' / ', $acc->service_group) }}
                                </span>
                            <div class="mt-1">
                                <span class="text-[9px] text-zinc-500">ثبت:</span>
                                @if($acc->creatorUser)
                                    <a href="{{ route('admin.managers.edit', $acc->creatorUser->id) }}" wire:navigate class="text-[10px] font-bold text-orange-400 hover:text-orange-300">
                                        {{ $acc->creatorUser->name }}
                                    </a>
                                @else
                                    <span class="text-[10px] text-zinc-400 font-bold">سیستم</span>
                                @endif
                            </div>
                        </td>

                        <td class="p-4 text-center">
                            <button wire:click="toggleStatus({{ $acc->id }})" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out border {{ $acc->is_enabled ? 'bg-emerald-500 border-emerald-600' : 'bg-slate-200 border-slate-300 dark:bg-zinc-800 dark:border-zinc-700' }}">
                                <span class="pointer-events-none rounded-full bg-white shadow-md h-3.5 w-3.5 transition-all duration-200 ease-in-out absolute top-[2px] {{ $acc->is_enabled ? 'left-[2px]' : 'right-[2px]' }}"></span>
                            </button>
                        </td>

                        <td class="p-4 text-center">
                            <button wire:click="edit({{ $acc->id }})" title="ویرایش" class="p-2 bg-zinc-800 text-zinc-400 hover:text-orange-400 rounded-lg border border-zinc-700/50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-12 text-center text-zinc-500 font-bold">هیچ کاربری یافت نشد.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-zinc-950/40 border-t border-zinc-800/60">
            {{ $accounts->links() }}
        </div>
    </div>

    @if($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm transition-opacity animate-fade-in" wire:key="modal-overlay">
            <div class="relative w-full max-w-4xl bg-zinc-950 border border-zinc-800/80 rounded-3xl shadow-2xl flex flex-col max-h-[95vh] overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-800/80 bg-zinc-900/50">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        {{ $accountId ? 'ویرایش اطلاعات کاربر' : 'صدور اکانت جدید' }}
                    </h2>
                    <button wire:click="resetForm" class="p-2 text-zinc-500 hover:text-red-500 hover:bg-red-500/10 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form wire:submit.prevent="save" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-5 bg-zinc-900/30 p-5 rounded-2xl border border-zinc-800/50">
                                <h3 class="text-sm font-bold text-orange-500 border-b border-zinc-800/80 pb-2">اطلاعات کاربری</h3>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-400 mb-2">نام کاربری (Username)</label>
                                    <input wire:model="username" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500 font-mono">
                                    @error('username') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-400 mb-2">رمز عبور (Password)</label>
                                    <input wire:model="password" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500 font-mono">
                                    @error('password') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-400 mb-2">گروه تعرفه (پکیج)</label>
                                    <select wire:model="group_id" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500">
                                        <option value="">انتخاب گروه کاربری...</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }} ({{ number_format($group->price) }} تومان)</option>
                                        @endforeach
                                    </select>
                                    @error('group_id') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">اتصال همزمان</label>
                                        <input wire:model="multi_login" type="number" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-2.5 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">سرویس پیش‌فرض</label>
                                        <input wire:model="service_group" type="text" dir="ltr" placeholder="l2tp_cisco" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-zinc-500 p-2.5 font-mono text-[10px]">
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-5 bg-zinc-900/30 p-5 rounded-2xl border border-zinc-800/50">
                                <h3 class="text-sm font-bold text-orange-500 border-b border-zinc-800/80 pb-2">سطوح دسترسی پروتکل‌ها</h3>
                                <div class="space-y-4 pt-2">
                                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-zinc-950/50 border border-zinc-800 rounded-xl hover:border-emerald-500/50 transition">
                                        <input type="checkbox" wire:model="can_create_v2" class="rounded border-zinc-700 bg-zinc-900 text-emerald-500 focus:ring-emerald-500 w-5 h-5">
                                        <div class="text-sm font-bold text-zinc-300">دسترسی به V2Ray / Xray</div>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-zinc-950/50 border border-zinc-800 rounded-xl hover:border-purple-500/50 transition">
                                        <input type="checkbox" wire:model="can_create_wg" class="rounded border-zinc-700 bg-zinc-900 text-purple-500 focus:ring-purple-500 w-5 h-5">
                                        <div class="text-sm font-bold text-zinc-300">دسترسی به WireGuard</div>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-zinc-950/50 border border-zinc-800 rounded-xl hover:border-blue-500/50 transition">
                                        <input type="checkbox" wire:model="can_create_op" class="rounded border-zinc-700 bg-zinc-900 text-blue-500 focus:ring-blue-500 w-5 h-5">
                                        <div class="text-sm font-bold text-zinc-300">دسترسی به OpenVPN</div>
                                    </label>
                                </div>
                                <div class="mt-6 pt-4 border-t border-zinc-800/80 space-y-3">
                                    <label class="flex items-center gap-2 text-sm font-bold text-zinc-300 cursor-pointer">
                                        <input type="checkbox" wire:model="is_enabled" class="rounded border-zinc-700 bg-zinc-950 text-emerald-500 focus:ring-emerald-500 w-4 h-4"> اکانت فعال باشد
                                    </label>
                                    <label class="flex items-center gap-2 text-sm font-bold text-zinc-300 cursor-pointer">
                                        <input type="checkbox" wire:model="in_app" class="rounded border-zinc-700 bg-zinc-950 text-orange-500 focus:ring-orange-500 w-4 h-4"> نمایش در اپلیکیشن
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="px-6 py-4 border-t border-zinc-800/80 bg-zinc-900/50 flex items-center gap-3 justify-end">
                    <button wire:click="resetForm" class="px-6 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-sm rounded-xl">لغو</button>
                    <button wire:click="save" class="px-8 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 text-white font-bold text-sm rounded-xl">ذخیره اکانت</button>
                </div>
            </div>
        </div>
    @endif

</div>
