<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-white">مدیریت سرویس RADIUS</h3>
            <p class="text-xs text-zinc-400">وضعیت و کنترل سرویس احراز هویت</p>
        </div>
        <button wire:click="loadStatus" wire:loading.attr="disabled" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition">
            <span wire:loading.remove wire:target="loadStatus">🔄 بروزرسانی وضعیت</span>
            <span wire:loading wire:target="loadStatus">⏳ در حال بروزرسانی...</span>
        </button>
    </div>

    {{-- Status Card --}}
    <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl {{ $status['status'] === 'active' ? 'bg-emerald-500/20' : 'bg-zinc-700/50' }} flex items-center justify-center">
                    <span class="text-2xl">{{ $status['status'] === 'active' ? '🟢' : '⚪' }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <span class="text-xl font-bold text-white">radius_server.service</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $status['status'] === 'active' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-zinc-700/50 text-zinc-400' }}">
                            {{ $status['status'] === 'active' ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-3 text-xs">
                        <div>
                            <span class="text-zinc-500">PID</span>
                            <div class="font-mono text-white">{{ $status['pid'] ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="text-zinc-500">Uptime</span>
                            <div class="font-mono text-white">{{ $status['uptime'] ? gmdate('H:i:s', $status['uptime']) : '-' }}</div>
                        </div>
                        <div>
                            <span class="text-zinc-500">Boot Status</span>
                            <div class="font-mono text-white">{{ isset($status['is_enabled']) ? ($status['is_enabled'] ? 'فعال' : 'غیرفعال') : '-' }}</div>
                        </div>
                        <div>
                            <span class="text-zinc-500">Last Started</span>
                            <div class="font-mono text-white text-[10px]">{{ $status['last_started'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($status['last_error']))
            <div class="mt-4 p-3 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-xs">
                ⚠️ آخرین خطا: {{ $status['last_error'] }}
            </div>
        @endif

        @if(session()->has('radius_message'))
            <div class="mt-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-xs">
                ✅ {{ session('radius_message') }}
            </div>
        @endif
        @if(session()->has('radius_error'))
            <div class="mt-4 p-3 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-xs">
                ❌ {{ session('radius_error') }}
            </div>
        @endif
    </div>

    {{-- Control Buttons --}}
    <div class="flex flex-wrap gap-3">
        <button wire:click="executeAction('start')" wire:loading.attr="disabled" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-emerald-500/20 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            شروع
        </button>
        <button wire:click="executeAction('stop')" wire:loading.attr="disabled" class="px-6 py-3 bg-rose-600 hover:bg-rose-500 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-rose-500/20 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
            توقف
        </button>
        <button wire:click="executeAction('restart')" wire:loading.attr="disabled" class="px-6 py-3 bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-orange-500/20 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            ری‌استارت
        </button>
        <button wire:click="executeAction('reload')" wire:loading.attr="disabled" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-blue-500/20 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            ریلود
        </button>
    </div>

    {{-- Confirmation Modal --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 max-w-md w-full shadow-2xl">
                <h3 class="text-lg font-bold text-white mb-2">تأیید عملیات</h3>
                <p class="text-sm text-zinc-400 mb-6">
                    آیا از {{ $action === 'stop' ? 'توقف' : 'ری‌استارت' }} سرویس RADIUS اطمینان دارید؟
                    <br>
                    <span class="text-xs text-zinc-500">در طول این عملیات ممکن است احراز هویت کاربران برای چند ثانیه مختل شود.</span>
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button wire:click="$set('showConfirmModal', false)" class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white text-sm font-bold rounded-xl transition">انصراف</button>
                    <button wire:click="confirmAction" wire:loading.attr="disabled" class="px-5 py-2.5 {{ $action === 'stop' ? 'bg-rose-600 hover:bg-rose-500' : 'bg-orange-600 hover:bg-orange-500' }} text-white text-sm font-bold rounded-xl transition shadow-lg flex items-center gap-2">
                        <span wire:loading.remove>تأیید</span>
                        <span wire:loading>⏳</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
