@php
    $panel = 'rounded-3xl border border-zinc-800/80 bg-zinc-900/70 shadow-xl shadow-black/5';
    $input = 'w-full rounded-2xl border border-zinc-700/80 bg-zinc-950/60 px-4 py-3 text-sm text-zinc-200 placeholder:text-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10';

    $statusColors = [
        'queued' => 'border-sky-500/20 bg-sky-500/10 text-sky-300',
        'running' => 'border-amber-500/20 bg-amber-500/10 text-amber-300',
        'success' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-300',
        'failed' => 'border-rose-500/20 bg-rose-500/10 text-rose-300',
        'expired' => 'border-zinc-700 bg-zinc-800 text-zinc-400',
    ];

    $tabBase = 'inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-bold transition-all';
    $tabActive = 'bg-brand-500 text-white shadow-lg shadow-brand-500/20';
    $tabInactive = 'text-zinc-400 hover:bg-zinc-800 hover:text-white';
@endphp

<div dir="rtl" class="min-h-full space-y-6 pb-10">

    {{-- Header --}}
    <section class="relative overflow-hidden rounded-3xl border border-zinc-800/80 bg-gradient-to-br from-zinc-900 via-zinc-900 to-brand-950/30 p-5 sm:p-7">
        <div class="absolute -left-20 -top-20 h-56 w-56 rounded-full bg-brand-500/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <div class="mb-3 flex items-center gap-2 text-xs font-bold text-brand-300">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-brand-500/10">⛁</span>
                    زیرساخت و بازیابی
                </div>

                <h1 class="text-2xl font-black text-white sm:text-3xl">مرکز پشتیبان‌گیری</h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500">
                    بکاپ خودکار از روترهای متصل و دیتابیس، با ذخیره‌سازی روی هاست دانلود و پوشه‌بندی بر اساس تاریخ و ساعت
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button wire:click="openStorageSettings" class="rounded-2xl border border-zinc-700 bg-zinc-800/80 px-4 py-3 text-sm font-bold text-zinc-200 transition hover:bg-zinc-800">
                    تنظیمات هاست دانلود
                </button>

                <button wire:click="backupDatabaseNow" class="rounded-2xl bg-brand-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/20 transition hover:bg-brand-600">
                    بکاپ الآن دیتابیس
                </button>

                <button wire:click="openSettings" class="rounded-2xl border border-zinc-700 bg-zinc-800/80 px-4 py-3 text-sm font-bold text-zinc-200 transition hover:bg-zinc-800">
                    تنظیمات بکاپ
                </button>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4 text-sm text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    @if($configError)
        <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4 text-sm leading-6 text-amber-200">
            <p class="font-bold">اتصال به فهرست دستگاه‌ها برقرار نشد</p>
            <p class="mt-1 text-amber-300/80">{{ $configError }}</p>
            <p class="mt-2 text-xs text-amber-300/60">
                مقدار <span class="font-mono">backup.device_model</span> را در <span class="font-mono">config/backup.php</span> با کلاس مدل سرورها تنظیم کنید.
            </p>
        </div>
    @endif

    {{-- Stats --}}
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="{{ $panel }} p-5">
            <p class="text-xs text-zinc-500">آخرین بکاپ موفق</p>
            <p class="mt-3 text-lg font-black text-white">
                {{ $stats['last_success'] ? \Illuminate\Support\Carbon::parse($stats['last_success'])->diffForHumans() : '—' }}
            </p>
        </div>

        <div class="{{ $panel }} p-5">
            <p class="text-xs text-zinc-500">بکاپ‌های موفق</p>
            <p class="mt-3 text-2xl font-black text-emerald-300">{{ number_format($stats['success']) }}</p>
        </div>

        <div class="{{ $panel }} p-5">
            <p class="text-xs text-zinc-500">بکاپ‌های ناموفق</p>
            <p class="mt-3 text-2xl font-black text-rose-300">{{ number_format($stats['failed']) }}</p>
        </div>

        <div class="{{ $panel }} p-5">
            <p class="text-xs text-zinc-500">حجم کل</p>
            <p class="mt-3 text-2xl font-black text-white">
                {{ (new \App\Models\Backup(['size' => $stats['size']]))->size_for_humans }}
            </p>
            <p class="mt-1 text-[11px] text-zinc-600">اجرای بعدی: {{ $stats['next_run'] }}</p>
        </div>
    </section>

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2 border-b border-zinc-800 pb-3">
        <button wire:click="$set('tab','devices')" class="{{ $tabBase }} {{ $tab === 'devices' ? $tabActive : $tabInactive }}">
            روترهای MikroTik
        </button>
        <button wire:click="$set('tab','history')" class="{{ $tabBase }} {{ $tab === 'history' ? $tabActive : $tabInactive }}">
            تاریخچه بکاپ‌ها
        </button>
    </div>

    {{-- Devices --}}
    @if($tab === 'devices')
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button wire:click="enableAllDeviceBackups" class="rounded-xl bg-zinc-800 px-4 py-2.5 text-xs font-bold text-emerald-300 transition hover:bg-zinc-700">
                فعال‌سازی بکاپ همه روترها
            </button>

            <button wire:click="disableAllDeviceBackups" class="rounded-xl bg-zinc-800 px-4 py-2.5 text-xs font-bold text-rose-300 transition hover:bg-zinc-700">
                غیرفعال‌سازی بکاپ همه روترها
            </button>
        </div>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($devices as $device)
                <article class="{{ $panel }} p-5 {{ $device['backup_enabled'] ? '' : 'opacity-70' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="truncate text-base font-black text-white">{{ $device['name'] }}</h2>
                            <p class="mt-1 font-mono text-xs text-zinc-500">{{ $device['host'] }}</p>
                        </div>

                        <button
                            type="button"
                            wire:click="toggleDeviceBackup({{ $device['id'] }})"
                            role="switch"
                            aria-checked="{{ $device['backup_enabled'] ? 'true' : 'false' }}"
                            aria-label="فعال یا غیرفعال کردن بکاپ {{ $device['name'] }}"
                            class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors
                            {{ $device['backup_enabled'] ? 'bg-emerald-500' : 'bg-zinc-700' }}"
                        >
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                {{ $device['backup_enabled'] ? '-translate-x-1' : '-translate-x-6' }}"></span>
                        </button>
                    </div>

                    <div class="mt-4 space-y-2 rounded-2xl bg-zinc-950/60 p-3 text-[11px] text-zinc-500">
                        <p>
                            وضعیت:
                            <span class="font-bold {{ $device['backup_enabled'] ? 'text-emerald-300' : 'text-zinc-500' }}">
                                {{ $device['backup_enabled'] ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </p>

                        <p>زمان‌بندی: <span class="text-zinc-300">{{ $device['schedule_label'] }}</span></p>

                        @if($device['backup_enabled'] && $device['next_run_at'])
                            <p>اجرای بعدی: <span class="text-zinc-300">{{ $device['next_run_at']->diffForHumans() }}</span></p>
                        @endif

                        @if($device['last_backup'])
                            <p>آخرین بکاپ: {{ $device['last_backup']->created_at->diffForHumans() }}</p>
                        @endif

                        @if($device['last_status'] === 'failed')
                            <p class="text-rose-400">آخرین تلاش ناموفق بود</p>
                        @endif
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button wire:click="openDeviceSettings({{ $device['id'] }})"
                                class="flex-1 rounded-xl bg-zinc-800 py-2.5 text-xs font-bold text-zinc-200 transition hover:bg-zinc-700">
                            تنظیمات بکاپ
                        </button>

                        <button wire:click="backupDeviceNow({{ $device['id'] }})" wire:loading.attr="disabled"
                                class="rounded-xl bg-brand-500/10 px-3 py-2.5 text-xs font-bold text-brand-300 transition hover:bg-brand-500/20">
                            بکاپ الآن
                        </button>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-zinc-700 p-12 text-center text-sm text-zinc-500">
                    روتری برای بکاپ ثبت نشده است.
                </div>
            @endforelse
        </section>
    @endif

    {{-- History --}}
    @if($tab === 'history')
        <section class="{{ $panel }} overflow-hidden">
            <div class="flex flex-wrap items-center gap-3 border-b border-zinc-800 p-4">
                <select wire:model.live="typeFilter" class="{{ $input }} sm:max-w-xs">
                    <option value="">همه انواع</option>
                    @foreach(\App\Models\Backup::TYPE_LABELS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="statusFilter" class="{{ $input }} sm:max-w-xs">
                    <option value="">همه وضعیت‌ها</option>
                    @foreach(\App\Models\Backup::STATUS_LABELS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>

                <button wire:click="cleanupNow" class="rounded-2xl bg-zinc-800 px-4 py-3 text-xs font-bold text-amber-300 transition hover:bg-zinc-700">
                    پاکسازی قدیمی‌ها
                </button>
            </div>

            <div class="divide-y divide-zinc-800">
                @forelse($backups as $backup)
                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg border px-2 py-1 text-[10px] font-bold {{ $statusColors[$backup->status] ?? $statusColors['queued'] }}">
                                    {{ $backup->status_label }}
                                </span>

                                <span class="text-xs font-bold text-zinc-300">{{ $backup->type_label }}</span>

                                @if($backup->device_name)
                                    <span class="text-[11px] text-zinc-500">{{ $backup->device_name }}</span>
                                @endif

                                @if($backup->encrypted)
                                    <span class="text-[10px] text-emerald-400">رمزنگاری‌شده</span>
                                @endif
                            </div>

                            <p class="mt-2 break-all font-mono text-[10px] text-zinc-600">{{ $backup->path }}</p>

                            <p class="mt-1 text-[11px] text-zinc-500">
                                {{ $backup->size_for_humans }}
                                · {{ $backup->created_at->format('Y-m-d H:i') }}
                                @if($backup->error_message)
                                    · <span class="text-rose-400">{{ \Illuminate\Support\Str::limit($backup->error_message, 70) }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex shrink-0 gap-2">
                            @if($backup->status === 'success')
                                <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('admin.backups.download', now()->addMinutes((int) config('backup.download_link_minutes', 10)), ['backup' => $backup->id]) }}"
                                   class="rounded-xl bg-zinc-800 px-3 py-2 text-xs font-bold text-brand-300 transition hover:bg-zinc-700">
                                    دانلود
                                </a>
                            @endif

                            <button wire:click="delete({{ $backup->id }})"
                                    wire:confirm="این بکاپ از هاست دانلود حذف شود؟"
                                    class="rounded-xl bg-rose-500/10 px-3 py-2 text-xs font-bold text-rose-300 transition hover:bg-rose-500/20">
                                حذف
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-sm text-zinc-500">بکاپی با این فیلتر پیدا نشد.</div>
                @endforelse
            </div>

            <div class="border-t border-zinc-800 p-4">
                {{ $backups->links() }}
            </div>
        </section>
    @endif

    {{-- Settings modal (سراسری) --}}
    @if($settingsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl border border-zinc-700 bg-zinc-900 p-6">
                <h2 class="text-xl font-black text-white">تنظیمات پشتیبان‌گیری</h2>

                <div class="mt-6 space-y-4">
                    <label class="flex items-center gap-3 rounded-2xl border border-zinc-800 bg-zinc-950/50 p-4">
                        <input wire:model="enabled" type="checkbox" class="h-4 w-4 rounded border-zinc-700 text-brand-500">
                        <span class="text-sm font-bold text-zinc-200">بکاپ خودکار فعال باشد</span>
                    </label>

                    <label class="flex items-center gap-3 rounded-2xl border border-zinc-800 bg-zinc-950/50 p-4">
                        <input wire:model="database_enabled" type="checkbox" class="h-4 w-4 rounded border-zinc-700 text-brand-500">
                        <span class="text-sm font-bold text-zinc-200">بکاپ دیتابیس</span>
                    </label>

                    <label class="flex items-center gap-3 rounded-2xl border border-zinc-800 bg-zinc-950/50 p-4">
                        <input wire:model="mikrotik_enabled" type="checkbox" class="h-4 w-4 rounded border-zinc-700 text-brand-500">
                        <span class="text-sm font-bold text-zinc-200">بکاپ روترهای MikroTik</span>
                    </label>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">ساعت اجرای بکاپ</label>
                        <input wire:model="run_at" type="time" class="{{ $input }}">
                        @error('run_at') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">مدت نگهداری (روز)</label>
                        <input wire:model="retention_days" type="number" min="1" max="365" class="{{ $input }}">
                        @error('retention_days') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-3 rounded-2xl border border-zinc-800 bg-zinc-950/50 p-4">
                        <input wire:model="encrypt_files" type="checkbox" class="h-4 w-4 rounded border-zinc-700 text-brand-500">
                        <span class="text-sm font-bold text-zinc-200">رمزنگاری فایل‌ها قبل از آپلود</span>
                    </label>
                </div>

                <div class="mt-6 flex gap-3">
                    <button wire:click="$set('settingsModal', false)" class="flex-1 rounded-2xl bg-zinc-800 py-3 text-sm font-bold text-zinc-300">
                        انصراف
                    </button>

                    <button wire:click="saveSettings" class="flex-1 rounded-2xl bg-brand-500 py-3 text-sm font-bold text-white">
                        ذخیره تنظیمات
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Device settings modal --}}
    @if($deviceModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl border border-zinc-700 bg-zinc-900 p-6">
                <h2 class="text-xl font-black text-white">تنظیمات بکاپ روتر</h2>
                <p class="mt-2 text-xs text-zinc-500">
                    این تنظیمات فقط روی همین روتر اعمال می‌شود و بر روترهای دیگر اثری ندارد.
                </p>

                <div class="mt-6 space-y-4">
                    <label class="flex items-center gap-3 rounded-2xl border border-zinc-800 bg-zinc-950/50 p-4">
                        <input wire:model="deviceEnabled" type="checkbox" class="h-4 w-4 rounded border-zinc-700 text-brand-500">
                        <span class="text-sm font-bold text-zinc-200">بکاپ خودکار این روتر فعال باشد</span>
                    </label>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">بازه تکرار</label>
                        <select wire:model.live="deviceFrequency" class="{{ $input }}">
                            @foreach(\App\Models\DeviceBackupSetting::FREQUENCIES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(in_array($deviceFrequency, ['daily', 'weekly', 'monthly'], true))
                        <div>
                            <label class="mb-2 block text-xs font-bold text-zinc-400">ساعت اجرا</label>
                            <input wire:model="deviceRunAt" type="time" class="{{ $input }}">
                            @error('deviceRunAt') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    @if($deviceFrequency === 'weekly')
                        <div>
                            <label class="mb-2 block text-xs font-bold text-zinc-400">روز هفته</label>
                            <select wire:model="deviceWeekday" class="{{ $input }}">
                                @foreach(\App\Models\DeviceBackupSetting::WEEKDAYS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($deviceFrequency === 'monthly')
                        <div>
                            <label class="mb-2 block text-xs font-bold text-zinc-400">روز ماه</label>
                            <input wire:model="deviceDayOfMonth" type="number" min="1" max="28" class="{{ $input }}">
                        </div>
                    @endif

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">مدت نگهداری (روز)</label>
                        <input wire:model="deviceRetentionDays" type="number" min="1" max="365" class="{{ $input }}" placeholder="خالی بگذار تا از تنظیمات سراسری استفاده شود">
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">رمزنگاری فایل</label>
                        <select wire:model="deviceEncrypt" class="{{ $input }}">
                            <option value="inherit">مطابق تنظیمات سراسری</option>
                            <option value="on">فعال</option>
                            <option value="off">غیرفعال</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button wire:click="$set('deviceModal', false)" class="flex-1 rounded-2xl bg-zinc-800 py-3 text-sm font-bold text-zinc-300">
                        انصراف
                    </button>

                    <button wire:click="saveDeviceSettings" class="flex-1 rounded-2xl bg-brand-500 py-3 text-sm font-bold text-white">
                        ذخیره تنظیمات
                    </button>
                </div>
            </div>
        </div>
    @endif


    @if($storageModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm">
            <div class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-3xl border border-zinc-700 bg-zinc-900 p-6">
                <h2 class="text-xl font-black text-white">تنظیمات هاست دانلود</h2>
                <p class="mt-2 text-xs text-zinc-500">
                    بکاپ‌ها روی این هاست ذخیره می‌شوند و بر اساس تاریخ و ساعت پوشه‌بندی خواهند شد.
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">نوع اتصال</label>
                        <select wire:model="storageDriver" class="{{ $input }}">
                            <option value="sftp">SFTP (امن)</option>
                            <option value="ftp">FTP</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">پورت</label>
                        <input wire:model="storagePort" type="number" min="1" max="65535" class="{{ $input }}" placeholder="{{ $storageDriver === 'sftp' ? '22' : '21' }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-xs font-bold text-zinc-400">آدرس هاست</label>
                        <input wire:model="storageHost" type="text" class="{{ $input }}" placeholder="download.example.com">
                        @error('storageHost') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-xs font-bold text-zinc-400">نام کاربری</label>
                        <input wire:model="storageUsername" type="text" class="{{ $input }}" placeholder="backupuser">
                        @error('storageUsername') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">رمز عبور</label>
                        <input wire:model="storagePassword" type="password" class="{{ $input }}" placeholder="••••••••">
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">مسیر ریشه (Root)</label>
                        <input wire:model="storageRoot" type="text" class="{{ $input }}" placeholder="/home/backupuser">
                    </div>

                    @if($storageDriver === 'sftp')
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-xs font-bold text-zinc-400">کلید خصوصی (اختیاری)</label>
                            <textarea wire:model="storagePrivateKey" rows="3" class="{{ $input }}" placeholder="-----BEGIN OPENSSH PRIVATE KEY-----"></textarea>
                        </div>
                    @endif

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">پوشه بکاپ‌ها</label>
                        <input wire:model="storageFolderRoot" type="text" class="{{ $input }}" placeholder="cmorgh-backups">
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-zinc-400">مهلت اتصال (ثانیه)</label>
                        <input wire:model="storageTimeout" type="number" min="10" max="600" class="{{ $input }}">
                    </div>
                </div>

                {{-- تست اتصال --}}
                <div class="mt-5 flex items-center gap-3">
                    <button wire:click="testConnection" wire:loading.attr="disabled"
                            class="rounded-xl bg-zinc-800 px-4 py-3 text-sm font-bold text-brand-300 transition hover:bg-zinc-700 disabled:opacity-60">
                        <span wire:loading.remove wire:target="testConnection">تست اتصال</span>
                        <span wire:loading wire:target="testConnection">در حال بررسی...</span>
                    </button>

                    @if($connectionTestResult)
                        <div class="flex-1 rounded-xl border p-3 text-xs
                        {{ $connectionTestStatus ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-300' : 'border-rose-500/20 bg-rose-500/10 text-rose-300' }}">
                            {{ $connectionTestResult }}
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex gap-3">
                    <button wire:click="$set('storageModal', false)" class="flex-1 rounded-2xl bg-zinc-800 py-3 text-sm font-bold text-zinc-300">
                        انصراف
                    </button>

                    <button wire:click="saveStorageSettings" wire:loading.attr="disabled" class="flex-1 rounded-2xl bg-brand-500 py-3 text-sm font-bold text-white disabled:opacity-60">
                        ذخیره تنظیمات
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
