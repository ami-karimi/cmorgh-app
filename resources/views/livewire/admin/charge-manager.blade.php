<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">مدیریت بسته‌های شارژ و قوانین چندگانه</h1>
            <p class="text-xs text-zinc-500 mt-1">امکان تعریف سرعت و حجم مجزا برای ساعات پیک و آف‌پیک در یک بسته</p>
        </div>

        @if(!$isFormOpen)
            <button wire:click="$set('isFormOpen', true)" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-[0_10px_20px_-10px_rgba(249,115,22,0.4)] transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                ایجاد بسته شارژ جدید
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if($isFormOpen)
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800/60 rounded-3xl p-6 md:p-8 mb-8 shadow-2xl relative overflow-hidden transition-all duration-300">
            <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ $chargeId ? 'ویرایش تنظیمات بسته' : 'پیکربندی بسته جدید' }}
            </h2>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-zinc-400 mb-2">نام بسته شارژ</label>
                        <input wire:model="name" type="text" placeholder="مثال: بسته یک‌ماهه 50 گیگ" class="w-full bg-zinc-950/60 border border-zinc-700/80 rounded-xl text-white p-3.5 focus:ring-1 focus:ring-orange-500">
                        @error('name') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-2">وضعیت</label>
                        <select wire:model="status" class="w-full bg-zinc-950/60 border border-zinc-700/80 rounded-xl text-white p-3.5 focus:ring-1 focus:ring-orange-500">
                            <option value="1">فعال (قابل فروش)</option>
                            <option value="0">غیرفعال (آرشیو)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-white border-b border-zinc-800/80 pb-2 mt-6">بازه‌های زمانی و محدودیت‌ها (Roles)</h3>

                    @foreach($roles as $index => $role)
                        <div class="bg-zinc-950/40 p-5 rounded-2xl border border-zinc-800/80 relative transition-all shadow-md group">

                            @if(count($roles) > 1)
                                <button type="button" wire:click="removeRole({{ $index }})" class="absolute left-4 top-4 p-2 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition opacity-0 group-hover:opacity-100 focus:opacity-100" title="حذف این بازه زمانی">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif

                            <h4 class="text-xs font-bold text-orange-500 mb-4 uppercase tracking-wider">بازه زمانی #{{ $index + 1 }}</h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                                <div>
                                    <label class="block text-xs font-bold text-zinc-400 mb-2">محدودیت حجم (Bytes)</label>
                                    <input wire:model="roles.{{ $index }}.max_bandwidth" type="number" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700/80 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-400 mb-2">سرعت مجاز (Kbps)</label>
                                    <input wire:model="roles.{{ $index }}.rate_limit" type="number" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700/80 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-400 mb-2">RAS Access</label>
                                    <input wire:model="roles.{{ $index }}.ras_access" type="text" dir="ltr" class="w-full bg-zinc-900 border border-zinc-700/80 rounded-xl text-zinc-400 p-3 focus:ring-1 focus:ring-orange-500">
                                </div>
                            </div>

                            <label class="block text-xs font-bold text-zinc-400 mb-3">روزهای مجاز اتصال در این بازه</label>
                            <div class="flex flex-wrap gap-2 mb-5" dir="ltr">
                                @foreach(['Saturday' => 'شنبه', 'Sunday' => 'یکشنبه', 'Monday' => 'دوشنبه', 'Tuesday' => 'سه‌شنبه', 'Wednesday' => 'چهارشنبه', 'Thursday' => 'پنج‌شنبه', 'Friday' => 'جمعه'] as $en => $fa)
                                    <label class="relative flex items-center justify-center cursor-pointer">
                                        <input type="checkbox" wire:model="roles.{{ $index }}.access_days" value="{{ $en }}" class="peer sr-only">
                                        <div class="px-3 py-1.5 bg-zinc-900 text-zinc-500 text-[11px] font-bold rounded-lg border border-zinc-800 peer-checked:bg-orange-500/10 peer-checked:text-orange-500 peer-checked:border-orange-500/50 transition-all">
                                            {{ $fa }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('roles.'.$index.'.access_days') <span class="text-red-500 text-[10px] block -mt-3 mb-4">{{ $message }}</span> @enderror

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 w-full md:w-1/2">
                                <div>
                                    <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">از ساعت</label>
                                    <input wire:model="roles.{{ $index }}.start_at" type="time" step="1" dir="ltr" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl text-white p-2.5 focus:ring-1 focus:ring-orange-500 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-zinc-500 mb-1.5">تا ساعت</label>
                                    <input wire:model="roles.{{ $index }}.end_at" type="time" step="1" dir="ltr" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl text-white p-2.5 focus:ring-1 focus:ring-orange-500 font-mono">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addRole" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800/80 hover:bg-zinc-700 text-orange-400 hover:text-white font-bold text-xs rounded-xl border border-zinc-700/50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        افزودن بازه زمانی / قانون جدید
                    </button>
                </div>

                <div class="flex gap-3 pt-6 border-t border-zinc-800/80">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-[0_10px_20px_-10px_rgba(249,115,22,0.4)] transition-all">
                        ذخیره تنظیمات بسته
                    </button>
                    <button type="button" wire:click="resetForm" class="px-8 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-sm rounded-xl border border-zinc-700/50 transition-all">
                        انصراف
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-zinc-950/80 text-zinc-400 font-bold border-b border-zinc-800/80">
                <tr>
                    <th class="p-5">نام بسته (Charge)</th>
                    <th class="p-5">تعداد رول‌ها</th>
                    <th class="p-5">خلاصه بازه‌های زمانی</th>
                    <th class="p-5">وضعیت</th>
                    <th class="p-5 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                @forelse($charges as $charge)
                    <tr class="hover:bg-zinc-800/30 transition-colors">
                        <td class="p-5">
                            <span class="font-bold text-white text-sm">{{ $charge->name }}</span>
                            <span class="block text-zinc-500 text-[10px] mt-1 font-mono">ID: {{ $charge->id }}</span>
                        </td>
                        <td class="p-5">
                            <span class="px-2 py-1 bg-zinc-800 rounded-md text-zinc-400 font-bold font-mono">{{ $charge->roles->count() }} رول</span>
                        </td>
                        <td class="p-5">
                            <div class="space-y-1">
                                @foreach($charge->roles as $index => $rl)
                                    <div class="text-[10px] text-zinc-500 font-mono" dir="ltr">
                                        <span class="text-orange-500/80 mr-1">#{{ $index+1 }}</span>
                                        {{ substr($rl->start_at, 0, 5) }}-{{ substr($rl->end_at, 0, 5) }}
                                        <span class="mx-1">|</span>
                                        {{ $rl->rate_limit }}K
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="p-5">
                            @if($charge->status)
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">فعال</span>
                            @else
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-zinc-800 text-zinc-500 border border-zinc-700">غیرفعال</span>
                            @endif
                        </td>
                        <td class="p-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="edit({{ $charge->id }})" title="ویرایش" class="p-1.5 bg-zinc-800 text-zinc-400 hover:text-orange-400 rounded-md border border-zinc-700/50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button wire:click="delete({{ $charge->id }})" onclick="confirm('آیا از حذف کامل این بسته مطمئن هستید؟') || event.stopImmediatePropagation()" class="p-1.5 bg-zinc-800 text-zinc-400 hover:text-red-400 rounded-md border border-zinc-700/50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-zinc-500 font-medium">هیچ بسته شارژی یافت نشد.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-zinc-950/40 border-t border-zinc-800/60">
            {{ $charges->links() }}
        </div>
    </div>
</div>
