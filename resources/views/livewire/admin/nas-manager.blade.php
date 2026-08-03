<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wide">زیرساخت و مدیریت سرورها</h1>
            <p class="text-xs text-zinc-500 mt-1">مدیریت نودها، پروتکل‌های اتصال (Mikrotik, V2Ray, OpenVPN) و مسیریابی</p>
        </div>

        @if(!$isFormOpen)
            <button wire:click="$set('isFormOpen', true)" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                افزودن سرور جدید
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/70 backdrop-blur-sm transition-opacity animate-fade-in">

            <div class="relative w-full max-w-5xl bg-zinc-950 border border-zinc-800/80 rounded-3xl shadow-2xl flex flex-col max-h-[95vh] overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-800/80 bg-zinc-900/50">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                        {{ $serverId ? 'ویرایش پیکربندی سرور' : 'راه‌اندازی سرور جدید' }}
                    </h2>

                    <button wire:click="resetForm" class="p-2 text-zinc-500 hover:text-red-500 hover:bg-red-500/10 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form wire:submit.prevent="save" class="space-y-8">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-zinc-900/30 p-5 rounded-2xl border border-zinc-800/50">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-orange-500 mb-4">پروتکل‌های فعال روی این سرور (با انتخاب هر مورد، تنظیمات آن باز می‌شود)</label>
                                <div class="flex flex-wrap gap-3" dir="ltr">
                                    @foreach(['l2tp' => 'L2TP / IPsec', 'v2ray' => 'V2Ray / Xray', 'openvpn' => 'OpenVPN', 'wireguard' => 'WireGuard'] as $val => $label)
                                        <label class="relative flex items-center justify-center cursor-pointer">
                                            <input type="checkbox" wire:model.live="server_type" value="{{ $val }}" class="peer sr-only">
                                            <div class="px-4 py-2 bg-zinc-950 text-zinc-500 text-xs font-bold rounded-lg border border-zinc-800 peer-checked:bg-orange-500/10 peer-checked:text-orange-500 peer-checked:border-orange-500/50 transition-all shadow-md">
                                                {{ $label }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('server_type') <span class="text-red-500 text-[10px] mt-2 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">نام نمایشی سرور</label>
                                <input wire:model="name" type="text" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">آی‌پی سرور (IP Address)</label>
                                <input wire:model="ipaddress" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500 font-mono">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">موقعیت مکانی (Location)</label>
                                <input wire:model="server_location" type="text" placeholder="مثلا: آلمان - فرانکفورت" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-orange-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-zinc-400 mb-2">پرچم کشور (آپلود عکس)</label>
                                <div class="flex items-center gap-4">
                                    <input wire:model="flag_file" type="file" accept="image/*" class="w-full text-xs text-zinc-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 hover:file:text-white transition cursor-pointer">
                                    @if ($flag)
                                        <img src="{{ asset($flag) }}" class="w-10 h-10 rounded-lg object-cover border border-zinc-700" alt="Flag">
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 mt-4 md:col-span-2">
                                <label class="flex items-center gap-2 text-sm font-bold text-zinc-300 cursor-pointer">
                                    <input type="checkbox" wire:model="is_enabled" class="rounded border-zinc-700 bg-zinc-950 text-emerald-500 focus:ring-emerald-500 w-5 h-5 transition">
                                    سرور فعال و در دسترس باشد
                                </label>
                                <label class="flex items-center gap-2 text-sm font-bold text-zinc-300 cursor-pointer">
                                    <input type="checkbox" wire:model="in_app" class="rounded border-zinc-700 bg-zinc-950 text-orange-500 focus:ring-orange-500 w-5 h-5 transition">
                                    نمایش این سرور در اپلیکیشن
                                </label>
                            </div>
                        </div>

                        @if(is_array($server_type) && in_array('l2tp', $server_type))
                            <div class="bg-zinc-900/30 p-5 rounded-2xl border border-blue-900/30 transition-all animate-fade-in">
                                <h3 class="text-sm font-bold text-blue-400 mb-5 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    تنظیمات پروتکل L2TP / IPSec
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5 border-b border-zinc-800/80 pb-5">
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-400 mb-2">IPSec Secret (رمزنگاری)</label>
                                        <input wire:model="secret" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-400 mb-2">L2TP Address (VPN IP)</label>
                                        <input wire:model="l2tp_address" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3">
                                    </div>
                                </div>

                                <label class="flex items-center gap-2 text-sm font-bold text-zinc-300 mb-5 cursor-pointer">
                                    <input type="checkbox" wire:model.live="mikrotik_server" class="rounded border-zinc-700 bg-zinc-950 text-blue-500 focus:ring-blue-500 w-5 h-5">
                                    اتصال به API میکروتیک (Radius)
                                </label>

                                @if($mikrotik_server)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-zinc-950 p-4 rounded-xl border border-zinc-800">
                                        <div>
                                            <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">API Domain / IP</label>
                                            <input wire:model="mikrotik_domain" type="text" dir="ltr" class="w-full bg-zinc-900 border border-zinc-800 rounded-lg text-white p-2.5">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">پورت اتصال (WWW-SSL / API)</label>
                                            <input wire:model="mikrotik_port" type="text" dir="ltr" placeholder="مثلاً 443" class="w-full bg-zinc-900 border border-zinc-800 rounded-lg text-white p-2.5">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">API Username</label>
                                            <input wire:model="mikrotik_username" type="text" dir="ltr" class="w-full bg-zinc-900 border border-zinc-800 rounded-lg text-white p-2.5">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-zinc-400 mb-1.5">API Password</label>
                                            <input wire:model="mikrotik_password" type="password" dir="ltr" class="w-full bg-zinc-900 border border-zinc-800 rounded-lg text-white p-2.5">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(is_array($server_type) && in_array('v2ray', $server_type))
                            <div class="bg-zinc-900/30 p-5 rounded-2xl border border-emerald-900/30 transition-all animate-fade-in">
                                <h3 class="text-sm font-bold text-emerald-500 mb-5 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                    تنظیمات پروتکل V2Ray / Xray
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-400 mb-2">CDN Address (آدرس دامنه)</label>
                                        <input wire:model="cdn_address_v2ray" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-400 mb-2">V2Ray Port</label>
                                        <input wire:model="port_v2ray" type="text" dir="ltr" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-white p-3 focus:ring-1 focus:ring-emerald-500">
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(is_array($server_type) && in_array('wireguard', $server_type))
                            <div class="bg-zinc-900/30 p-5 rounded-2xl border border-purple-900/30 transition-all animate-fade-in">
                                <h3 class="text-sm font-bold text-purple-400 mb-5 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    پیکربندی سرور WireGuard
                                </h3>

                                <div>
                                    <label class="block text-xs font-bold text-zinc-400 mb-2">فایل کانفیگ یا کلیدهای اختصاصی (فیلد config)</label>
                                    <textarea wire:model="config" dir="ltr" rows="5" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl text-zinc-300 p-3 font-mono text-xs focus:ring-1 focus:ring-purple-500"></textarea>
                                </div>
                            </div>
                        @endif

                        @if(is_array($server_type) && in_array('openvpn', $server_type))
                            <div class="bg-zinc-900/30 p-5 rounded-2xl border border-orange-900/30 transition-all animate-fade-in">
                                <h3 class="text-sm font-bold text-orange-500 mb-5 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    پیکربندی OpenVPN
                                </h3>

                                <div>
                                    <label class="block text-xs font-bold text-zinc-400 mb-2">آپلود فایل پروفایل (.ovpn)</label>
                                    <div class="flex items-center gap-4">
                                        <input wire:model="openvpn_file" type="file" accept=".ovpn,.txt" class="w-full text-xs text-zinc-500 file:mr-4 file:py-3 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-500/10 file:text-orange-500 hover:file:bg-orange-500 hover:file:text-white transition cursor-pointer">
                                    </div>
                                </div>
                            </div>
                        @endif

                    </form>
                </div>

                <div class="px-6 py-4 border-t border-zinc-800/80 bg-zinc-900/50 flex items-center gap-3 justify-end">
                    <button wire:click="resetForm" class="px-6 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-sm rounded-xl transition-all">
                        انصراف
                    </button>
                    <button wire:click="save" class="px-8 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
                        ذخیره تنظیمات سرور
                    </button>
                </div>

            </div>
        </div>
    @endif

    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-zinc-950/80 text-zinc-400 font-bold border-b border-zinc-800/80">
                <tr>
                    <th class="p-5">نام و موقعیت</th>
                    <th class="p-5">IP سرور</th>
                    <th class="p-5">پروتکل‌های فعال</th>
                    <th class="p-5">وضعیت</th>
                    <th class="p-5 text-center">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                @forelse($servers as $srv)
                    <tr class="hover:bg-zinc-800/30 transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-4">
                                @if($srv->flag)
                                    <img src="{{ asset($srv->flag) }}" class="w-10 h-10 rounded-full object-cover border border-zinc-700 shadow-md" alt="{{ $srv->name }}">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($srv->name) }}&background=27272a&color=f97316&size=128" class="w-10 h-10 rounded-full object-cover border border-zinc-700 shadow-md" alt="{{ $srv->name }}">
                                @endif

                                <div>
                                    <span class="font-bold text-white text-sm block">{{ $srv->name }}</span>
                                    <span class="text-[10px] text-zinc-500 mt-0.5 block flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ $srv->server_location ?? 'نامشخص' }}
            </span>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 font-mono text-orange-400" dir="ltr">{{ $srv->ipaddress }}</td>
                        <td class="p-5">
                            <div class="flex flex-wrap gap-1.5" dir="ltr">
                                @php
                                    // جلوگیری از خطا در صورتی که دیتای قدیمی در دیتابیس رشته ساده باشد
                                    $protocols = is_array($srv->server_type) ? $srv->server_type : explode(',', $srv->server_type);
                                @endphp

                                @foreach($protocols as $protocol)
                                    @if($protocol)
                                        <span class="px-2 py-0.5 rounded bg-zinc-800 text-zinc-300 text-[10px] uppercase font-bold border border-zinc-700">
                                                {{ $protocol }}
                                            </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="p-5">
                            <div class="flex items-center">
                                <button wire:click="toggleStatus({{ $srv->id }})"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none border {{ $srv->is_enabled ? 'bg-emerald-500 border-emerald-600' : 'bg-slate-200 border-slate-300 dark:bg-zinc-800 dark:border-zinc-700' }}">

                                    <span class="pointer-events-none rounded-full bg-white shadow-md h-3.5 w-3.5 transition-all duration-200 ease-in-out absolute top-[2px] {{ $srv->is_enabled ? 'left-[2px]' : 'right-[2px]' }}"></span>
                                </button>

                                <span class="mr-2 text-[11px] font-bold tracking-wide {{ $srv->is_enabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-zinc-500' }}">
            {{ $srv->is_enabled ? 'روشن' : 'خاموش' }}
        </span>
                            </div>
                        </td>
                        <td class="p-5 text-center">
                            <button wire:click="edit({{ $srv->id }})" title="ویرایش پیکربندی" class="p-2 bg-zinc-800 text-zinc-400 hover:text-orange-400 rounded-lg border border-zinc-700/50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-zinc-500 font-medium">سروری ثبت نشده است.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-zinc-950/40 border-t border-zinc-800/60">
            {{ $servers->links() }}
        </div>
    </div>
</div>
