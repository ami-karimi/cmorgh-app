<div class="space-y-6" dir="rtl">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div><span class="text-xs text-brand-400 font-bold">ابزار عیب‌یابی</span><h1 class="text-2xl sm:text-3xl font-black text-white mt-2">بررسی سیستم</h1><p class="text-sm text-zinc-400 mt-2">جست‌وجوی مستقیم کاربر در MikroTik و Peerهای WireGuard</p></div>
        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 px-4 py-3 text-xs text-amber-200 max-w-md">کلید خصوصی فقط برای استخراج کلید عمومی در همین درخواست استفاده می‌شود و در نتیجه یا لاگ ذخیره نمی‌شود.</div>
    </div>

    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-4 sm:p-6 shadow-xl">
        <div class="flex flex-wrap gap-2 border-b border-zinc-800 pb-5 mb-6">
            <button type="button" wire:click="$set('mode', 'mikrotik')" class="px-4 py-2.5 rounded-xl text-sm font-bold transition {{ $mode === 'mikrotik' ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/20' : 'bg-zinc-800 text-zinc-400 hover:text-white' }}">جست‌وجوی نام کاربری MikroTik</button>
            <button type="button" wire:click="$set('mode', 'wireguard')" class="px-4 py-2.5 rounded-xl text-sm font-bold transition {{ $mode === 'wireguard' ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/20' : 'bg-zinc-800 text-zinc-400 hover:text-white' }}">بررسی کلید WireGuard</button>
        </div>

        <form wire:submit="check" class="grid md:grid-cols-2 gap-5">
            <div class="md:col-span-2"><label class="block text-xs font-bold text-zinc-300 mb-2">سرور مورد بررسی</label><select wire:model="serverId" class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-zinc-200"><option value="">انتخاب سرور</option>@foreach($servers as $server)<option value="{{ $server->id }}">{{ $server->name }} — {{ $server->ipaddress }}</option>@endforeach</select>@error('serverId')<span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>@enderror</div>
            @if($mode === 'mikrotik')
                <div class="md:col-span-2"><label class="block text-xs font-bold text-zinc-300 mb-2">نام کاربری</label><input dir="ltr" wire:model="username" type="text" autocomplete="off" placeholder="username" class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-zinc-200 text-left">@error('username')<span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>@enderror</div>
            @else
                <div><label class="block text-xs font-bold text-zinc-300 mb-2">کلید عمومی <span class="text-zinc-500 font-normal">(اختیاری)</span></label><input dir="ltr" wire:model="publicKey" type="text" autocomplete="off" placeholder="Base64 public key" class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-zinc-200 text-left">@error('publicKey')<span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>@enderror</div>
                <div><label class="block text-xs font-bold text-zinc-300 mb-2">کلید خصوصی <span class="text-zinc-500 font-normal">(اختیاری)</span></label><input dir="ltr" wire:model="privateKey" type="password" autocomplete="new-password" placeholder="Base64 private key" class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-zinc-200 text-left"><p class="text-[11px] text-zinc-500 mt-2">با وارد کردن کلید خصوصی، کلید عمومی متناظر به‌صورت محلی محاسبه می‌شود.</p></div>
            @endif
            <div class="md:col-span-2 flex items-center gap-3 pt-2"><button type="submit" wire:loading.attr="disabled" class="ui-button min-w-36"><span wire:loading.remove wire:target="check">شروع بررسی</span><span wire:loading wire:target="check">در حال بررسی...</span></button><button type="button" wire:click="$set('result', null)" class="px-4 py-2.5 rounded-xl text-sm text-zinc-400 hover:text-white">پاک کردن نتیجه</button></div>
        </form>
    </div>

    @error('publicKey')<div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">{{ $message }}</div>@enderror
    @if($error)<div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">{{ $error }}</div>@endif
    @if($result)
        <div class="rounded-2xl border {{ ($result['found'] ?? false) ? 'border-emerald-500/30' : 'border-amber-500/30' }} bg-zinc-900/70 p-5 sm:p-6">
            <div class="flex items-start gap-3"><div class="w-10 h-10 rounded-xl flex items-center justify-center {{ ($result['found'] ?? false) ? 'bg-emerald-500/15 text-emerald-300' : (($result['ok'] ?? false) ? 'bg-amber-500/15 text-amber-300' : 'bg-rose-500/15 text-rose-300') }}">{{ ($result['found'] ?? false) ? '✓' : '!' }}</div><div><h2 class="font-bold text-white">{{ ($result['found'] ?? false) ? 'اطلاعات پیدا شد' : (($result['ok'] ?? false) ? 'موردی پیدا نشد' : 'خطا در ارتباط با سرور') }}</h2><p class="text-sm {{ ($result['ok'] ?? false) ? 'text-zinc-400' : 'text-rose-300' }} mt-1">{{ $result['message'] ?? '' }}</p></div></div>
            @if($result['found'] ?? false)
                <div class="grid md:grid-cols-2 gap-4 mt-6">
                    <div class="rounded-xl bg-zinc-950/70 border border-zinc-800 p-4"><h3 class="text-xs font-bold text-brand-300 mb-3">اطلاعات روی سرور</h3><dl class="space-y-2 text-xs">@foreach(($result['remote'] ?? []) as $key => $value)<div class="flex justify-between gap-3 border-b border-zinc-800/70 pb-2"><dt class="text-zinc-500">{{ $key }}</dt><dd dir="ltr" class="text-zinc-200 text-left max-w-[65%] break-all">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</dd></div>@endforeach</dl></div>
                    <div class="rounded-xl bg-zinc-950/70 border border-zinc-800 p-4"><h3 class="text-xs font-bold text-brand-300 mb-3">تطبیق با دیتابیس</h3>@if(isset($result['account']) && $result['account'])<p class="text-sm text-emerald-300">اکانت مرتبط در دیتابیس پیدا شد.</p><dl class="space-y-2 text-xs mt-3">@foreach($result['account'] as $key => $value)<div class="flex justify-between gap-3"><dt class="text-zinc-500">{{ $key }}</dt><dd class="text-zinc-200">{{ is_bool($value) ? ($value ? 'فعال' : 'غیرفعال') : ($value ?? '—') }}</dd></div>@endforeach</dl>@elseif(isset($result['local']) && $result['local'])<p class="text-sm text-emerald-300">Peer در رکوردهای محلی نیز پیدا شد.</p><pre class="text-xs text-zinc-300 mt-3 whitespace-pre-wrap">{{ json_encode($result['local'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>@else<p class="text-sm text-amber-300">در سرور پیدا شد، اما رکورد متناظر در دیتابیس وجود ندارد.</p>@endif</div>
                </div>
            @endif
        </div>
    @endif
</div>
