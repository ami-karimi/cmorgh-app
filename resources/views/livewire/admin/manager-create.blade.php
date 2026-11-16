<div class="max-w-3xl mx-auto space-y-6" dir="rtl">
    <div><a href="{{ route('admin.managers.list') }}" wire:navigate class="text-xs text-brand-300">← بازگشت به لیست</a><h1 class="text-2xl font-black text-white mt-4">ایجاد مدیر یا نماینده</h1><p class="text-sm text-zinc-500 mt-2">اطلاعات ورود و سطح دسترسی همکار جدید را ثبت کنید.</p></div>
    <form wire:submit="save" class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-6 grid md:grid-cols-2 gap-5">
        <div class="md:col-span-2"><label class="form-label">نام کامل</label><input wire:model="name" class="form-input">@error('name')<span class="form-error">{{ $message }}</span>@enderror</div>
        <div><label class="form-label">شماره تماس</label><input wire:model="phone" dir="ltr" class="form-input text-left">@error('phone')<span class="form-error">{{ $message }}</span>@enderror</div>
        <div><label class="form-label">ایمیل</label><input wire:model="email" type="email" dir="ltr" class="form-input text-left">@error('email')<span class="form-error">{{ $message }}</span>@enderror</div>
        <div><label class="form-label">رمز عبور</label><input wire:model="password" type="password" dir="ltr" class="form-input text-left">@error('password')<span class="form-error">{{ $message }}</span>@enderror</div>
        <div><label class="form-label">نقش</label><select wire:model="role" class="form-input"><option value="agent">نماینده فروش</option><option value="sub_agent">زیرنماینده</option><option value="manager">مدیر کل</option><option value="admin">مدیر سیستم</option></select>@error('role')<span class="form-error">{{ $message }}</span>@enderror</div>
        <label class="md:col-span-2 flex items-center gap-2 text-sm text-zinc-300"><input type="checkbox" wire:model="is_active" class="rounded"> حساب فعال باشد</label>
        <div class="md:col-span-2 flex justify-end gap-3 border-t border-zinc-800 pt-5"><a href="{{ route('admin.managers.list') }}" wire:navigate class="px-5 py-3 rounded-xl bg-zinc-800 text-zinc-300 text-sm font-bold">انصراف</a><button class="ui-button">ایجاد حساب</button></div>
    </form>
    <style>.form-label{display:block;color:#a5b4c9;font-size:.75rem;font-weight:700;margin-bottom:.5rem}.form-input{width:100%;background:#080f1d;border:1px solid #334155;border-radius:.75rem;color:#e2e8f0;padding:.75rem;font-size:.875rem}.form-input:focus{outline:2px solid #3b82f655}.form-error{display:block;color:#fb7185;font-size:.7rem;margin-top:.35rem}</style>
</div>
