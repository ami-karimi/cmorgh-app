<div class="space-y-10 animate-fade-in pb-10 relative">

    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">وضعیت سیستم و سرورها</h1>
        <p class="text-sm text-zinc-500 mt-1">مانیتورینگ لحظه‌ای لتنسی سرورها و مدیریت دستی وضعیت سرویس‌ها</p>
    </div>

    @if (session()->has('success_service'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success_service') }}
        </div>
    @endif

    <div class="space-y-4">
        <h2 class="text-sm font-black text-zinc-400 uppercase tracking-widest flex items-center gap-2">
            <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
            ترافیک و لتنسی سرورها (Nodes)
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($servers as $server)
                <div class="bg-[#111827] border border-zinc-800 rounded-3xl p-5 relative overflow-hidden">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $server->health === 'good' ? 'bg-emerald-400' : ($server->health === 'warning' ? 'bg-amber-400' : 'bg-rose-400') }}"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 {{ $server->health === 'good' ? 'bg-emerald-500' : ($server->health === 'warning' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                            </span>
                            <h3 class="font-bold text-white font-mono" dir="ltr">{{ $server->nasname }}</h3>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-zinc-800 text-zinc-400">{{ $server->type ?? 'VPN' }}</span>
                    </div>

                    <div class="flex items-end gap-2 mb-2">
                        <span class="text-3xl font-black font-mono tracking-tighter {{ $server->health === 'good' ? 'text-emerald-500' : ($server->health === 'warning' ? 'text-amber-500' : 'text-rose-500') }}">
                            {{ $server->latency }}
                        </span>
                        <span class="text-xs text-zinc-500 mb-1 font-mono">ms</span>
                    </div>

                    <p class="text-[10px] text-zinc-500 mb-4">وضعیت اتصال: {{ $server->health === 'good' ? 'عالی و پایدار' : ($server->health === 'warning' ? 'ترافیک بالا' : 'کندی شدید') }}</p>

                    <div class="w-full h-1.5 bg-zinc-900 rounded-full overflow-hidden">
                        <div class="h-full {{ $server->health === 'good' ? 'bg-emerald-500' : ($server->health === 'warning' ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ min(100, ($server->latency / 200) * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-sm font-black text-zinc-400 uppercase tracking-widest flex items-center gap-2">
                <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                وضعیت زیرساخت‌ها
            </h2>

            <button wire:click="openServiceModal" class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-orange-500/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                افزودن سرویس / تغییر وضعیت
            </button>
        </div>

        <div class="bg-[#111827] border border-zinc-800 rounded-3xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-zinc-900/50 border-b border-zinc-800">
                    <tr class="text-zinc-500 text-[10px] font-black uppercase tracking-wider">
                        <th class="p-4">نام سرویس / زیرساخت</th>
                        <th class="p-4">وضعیت فعلی</th>
                        <th class="p-4">اطلاعیه / آخرین تغییرات</th>
                        <th class="p-4">بروزرسانی</th>
                        <th class="p-4 text-center">عملیات</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800 text-sm">
                    @forelse($services as $srv)
                        <tr class="hover:bg-zinc-900/30 transition">
                            <td class="p-4 font-bold text-white">{{ $srv->service_name }}</td>
                            <td class="p-4">
                                @if($srv->status === 'operational')
                                    <span class="px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-500 text-[10px] font-black flex items-center gap-1.5 w-max">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> نرمال و پایدار
                                        </span>
                                @elseif($srv->status === 'degraded')
                                    <span class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-500 text-[10px] font-black flex items-center gap-1.5 w-max animate-pulse">
                                            اختلال / کندی
                                        </span>
                                @else
                                    <span class="px-2.5 py-1 rounded bg-rose-500/10 text-rose-500 text-[10px] font-black flex items-center gap-1.5 w-max">
                                            قطعی کامل
                                        </span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-zinc-400 max-w-[200px] truncate" title="{{ $srv->last_change_log }}">
                                {{ $srv->last_change_log ?? 'بدون اطلاعیه' }}
                            </td>
                            <td class="p-4 text-[10px] text-zinc-500 font-mono">{{ jdate($srv->updated_at)->ago() }}</td>

                            <td class="p-4 flex items-center justify-center gap-2">
                                <button wire:click="openServiceModal({{ $srv->id }})" class="p-2 rounded-xl bg-zinc-800 text-zinc-400 hover:text-orange-400 transition" title="ویرایش وضعیت">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button wire:click="deleteService({{ $srv->id }})" wire:confirm="آیا از حذف این سرویس مطمئن هستید؟" class="p-2 rounded-xl bg-zinc-800 text-zinc-400 hover:text-rose-400 transition" title="حذف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-zinc-500 text-xs">تا کنون هیچ وضعیتی برای سرویس‌ها ثبت نکرده‌اید.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($isServiceModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in overflow-y-auto">
            <div class="w-full max-w-md bg-[#111827] border border-zinc-800 rounded-3xl shadow-2xl overflow-hidden my-8 flex flex-col">

                <div class="p-5 border-b border-zinc-800 flex items-center justify-between bg-[#09090b]">
                    <h3 class="text-base font-black text-white">
                        {{ $serviceId ? 'ویرایش وضعیت سرویس' : 'ثبت سرویس و وضعیت جدید' }}
                    </h3>
                    <button wire:click="$set('isServiceModalOpen', false)" class="text-zinc-500 hover:text-rose-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveService" class="p-6 space-y-5">

                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">انتخاب سرور <span class="text-rose-500">*</span></label>
                        <select wire:model="service_name" class="w-full bg-[#09090b] border border-zinc-800 text-white rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-500 outline-none appearance-none">
                            <option value="">-- لطفاً یک سرور را انتخاب کنید --</option>

                            <optgroup label="لیست سرورهای شما">
                                @foreach($nasList as $nas)
                                    <option value="{{ $nas->name }}">{{ $nas->name }} ({{ $nas->type ?? 'VPN' }})</option>
                                @endforeach
                            </optgroup>

                            <optgroup label="زیرساخت کلی">
                                <option value="🌐 کل شبکه و زیرساخت">🌐 کل شبکه و زیرساخت</option>
                                <option value="⚡ سیستم تانلینگ و روتینگ">⚡ سیستم تانلینگ و روتینگ</option>
                                <option value="🖥️ پنل مدیریت و فروش">🖥️ پنل مدیریت و فروش</option>
                            </optgroup>
                        </select>
                        @error('service_name') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">وضعیت عملیاتی <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" wire:click="$set('status', 'operational')" class="py-2.5 rounded-xl border text-xs font-bold transition-all {{ $status === 'operational' ? 'bg-emerald-500/10 border-emerald-500 text-emerald-500' : 'bg-[#09090b] border-zinc-800 text-zinc-500' }}">
                                نرمال
                            </button>
                            <button type="button" wire:click="$set('status', 'degraded')" class="py-2.5 rounded-xl border text-xs font-bold transition-all {{ $status === 'degraded' ? 'bg-amber-500/10 border-amber-500 text-amber-500' : 'bg-[#09090b] border-zinc-800 text-zinc-500' }}">
                                اختلال
                            </button>
                            <button type="button" wire:click="$set('status', 'outage')" class="py-2.5 rounded-xl border text-xs font-bold transition-all {{ $status === 'outage' ? 'bg-rose-500/10 border-rose-500 text-rose-500' : 'bg-[#09090b] border-zinc-800 text-zinc-500' }}">
                                قطعی
                            </button>
                        </div>
                        @error('status') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">اطلاعیه و جزئیات رخداد (اختیاری)</label>
                        <textarea wire:model="last_change_log" rows="3" placeholder="مثال: سرورها در حال ارتقا سخت‌افزاری هستند و تا ۲ ساعت آینده اختلال خواهیم داشت..." class="w-full bg-[#09090b] border border-zinc-800 text-white rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-500 outline-none"></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="$set('isServiceModalOpen', false)" class="flex-1 py-3 rounded-xl bg-zinc-800 text-white text-sm font-bold hover:bg-zinc-700 transition">انصراف</button>
                        <button type="submit" class="flex-1 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-black transition shadow-lg shadow-orange-500/20">
                            {{ $serviceId ? 'ثبت تغییرات' : 'افزودن به لیست' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>
