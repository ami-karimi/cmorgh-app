<div class="space-y-6 animate-fade-in">
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-zinc-500 block">کاربران منقضی</span>
                <span class="text-xl font-bold text-white">{{ $stats['expired'] ?? 0 }}</span>
                <span class="text-xs text-zinc-500">نفر</span>
            </div>
        </div>

        <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-zinc-500 block">حجم تمام شده</span>
                <span class="text-xl font-bold text-white">{{ $stats['volume_finished'] ?? 0 }}</span>
                <span class="text-xs text-zinc-500">نفر</span>
            </div>
        </div>

        <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-zinc-500 block">قابل پاکسازی</span>
                <span class="text-xl font-bold text-white">{{ $stats['total'] ?? 0 }}</span>
                <span class="text-xs text-zinc-500">نفر</span>
            </div>
        </div>
    </div>

    {{-- Sub Tabs --}}
    <div class="flex gap-2 border-b border-zinc-800 pb-3">
        <button wire:click="$set('activeSubTab', 'health')" class="px-4 py-2 text-xs font-bold rounded-xl transition {{ $activeSubTab === 'health' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-zinc-400 hover:text-white' }}">🔍 سلامت سیستم</button>
        <button wire:click="$set('activeSubTab', 'cleanup')" class="px-4 py-2 text-xs font-bold rounded-xl transition {{ $activeSubTab === 'cleanup' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-zinc-400 hover:text-white' }}">🧹 پاکسازی</button>
        <button wire:click="$set('activeSubTab', 'logs')" class="px-4 py-2 text-xs font-bold rounded-xl transition {{ $activeSubTab === 'logs' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-zinc-400 hover:text-white' }}">📋 لاگ‌ها</button>
    </div>

    {{-- Health Sub Tab --}}
    @if($activeSubTab === 'health')
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-bold text-white">بررسی مغایرت سرویس‌ها</h4>
                    <p class="text-xs text-zinc-400">مقایسه دیتابیس با سرورهای MikroTik, WireGuard, OpenVPN, V2Ray</p>
                </div>
                <button wire:click="runHealthCheck" wire:loading.attr="disabled" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs rounded-xl transition shadow-lg shadow-orange-500/20 flex items-center gap-2">
                    <span wire:loading.remove wire:target="runHealthCheck">🚀 شروع بررسی کامل</span>
                    <span wire:loading wire:target="runHealthCheck">⏳ در حال بررسی...</span>
                </button>
            </div>

            @if(!empty($healthResults))
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($healthResults as $service => $result)
                        <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl p-4 text-center">
                            <div class="text-lg font-bold text-white mb-1">{{ ucfirst($service) }}</div>
                            <div class="text-2xl font-bold {{ $result['issues_count'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                {{ $result['issues_count'] }}
                            </div>
                            <div class="text-xs text-zinc-500">مغایرت</div>
                            <div class="text-[10px] text-zinc-600 mt-1">وضعیت: {{ $result['status'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($healthIssues->count() > 0)
                <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-zinc-900/50 text-zinc-400 border-b border-zinc-800">
                            <tr>
                                <th class="p-3">سرویس</th>
                                <th class="p-3">کاربر</th>
                                <th class="p-3">نوع مشکل</th>
                                <th class="p-3">شدت</th>
                                <th class="p-3">جزئیات</th>
                                <th class="p-3">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800/50">
                            @foreach($healthIssues as $issue)
                                <tr class="hover:bg-zinc-900/30 transition">
                                    <td class="p-3 text-white">{{ $issue->service }}</td>
                                    <td class="p-3 font-mono text-white">{{ $issue->username }}</td>
                                    <td class="p-3">
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold
                                                {{ $issue->issue_type === 'orphan' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                            {{ $issue->issue_type === 'missing' ? 'bg-rose-500/20 text-rose-400' : '' }}
                                            {{ $issue->issue_type === 'mismatch' ? 'bg-blue-500/20 text-blue-400' : '' }}">
                                                {{ $issue->issue_type }}
                                            </span>
                                    </td>
                                    <td class="p-3">
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold
                                                {{ $issue->severity === 'critical' ? 'bg-rose-500/20 text-rose-400' : '' }}
                                            {{ $issue->severity === 'warning' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                            {{ $issue->severity === 'info' ? 'bg-blue-500/20 text-blue-400' : '' }}">
                                                {{ $issue->severity }}
                                            </span>
                                    </td>
                                    <td class="p-3 text-zinc-400 max-w-xs truncate">{{ $issue->details }}</td>
                                    <td class="p-3">
                                        <button wire:click="ignoreIssue({{ $issue->id }})" class="px-3 py-1 bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] rounded-lg transition">نادیده گرفتن</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="py-8 text-center text-zinc-500 text-sm">✅ همه سرویس‌ها سالم هستند. هیچ مغایرتی یافت نشد.</div>
            @endif
        </div>
    @endif

    {{-- Cleanup Sub Tab --}}
    @if($activeSubTab === 'cleanup')
        <div class="space-y-6">
            {{-- Logs Cleanup --}}
            <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl p-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-white">پاکسازی لاگ‌های سیستم</h4>
                        <div class="grid grid-cols-2 gap-4 mt-2 text-xs">
                            <div><span class="text-zinc-500">حجم فایل‌ها:</span> <span class="text-white font-mono">{{ $logsInfo['size_human'] ?? '0 B' }}</span></div>
                            <div><span class="text-zinc-500">تعداد فایل:</span> <span class="text-white font-mono">{{ $logsInfo['file_count'] ?? 0 }}</span></div>
                            <div class="col-span-2"><span class="text-zinc-500">مسیر:</span> <span class="text-zinc-400 font-mono text-[10px]">{{ $logsInfo['path'] ?? '-' }}</span></div>
                        </div>
                    </div>
                    <button wire:click="cleanLogs" wire:loading.attr="disabled" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl transition shadow-lg shadow-rose-500/20 flex items-center gap-2">
                        <span wire:loading.remove>🧹 پاکسازی لاگ‌ها</span>
                        <span wire:loading>⏳ در حال پاکسازی...</span>
                    </button>
                </div>
            </div>

            {{-- Expired Users Cleanup --}}
            <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl p-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-white">پاکسازی کاربران منقضی شده</h4>
                        <p class="text-xs text-zinc-400">کاربرانی که بیش از ۱۵ روز از انقضا یا تمام‌شدن حجم آن‌ها گذشته است.</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="loadExpiredUsers" wire:loading.attr="disabled" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition">
                            <span wire:loading.remove>🔍 بررسی</span>
                            <span wire:loading>⏳</span>
                        </button>
                    </div>
                </div>

                {{-- بخش کاربران منقضی --}}
                @if($expiredUsers && $expiredUsers->count() > 0)
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-zinc-900/50 text-zinc-400 border-b border-zinc-800">
                            <tr>
                                <th class="p-2"><input type="checkbox" wire:model="selectedUsers" value="{{ $expiredUsers->first()->id ?? '' }}" class="rounded border-zinc-700 bg-zinc-900 text-orange-500"></th>
                                <th class="p-2">کاربر</th>
                                <th class="p-2">سرویس</th>
                                <th class="p-2">تاریخ انقضا</th>
                                <th class="p-2">روز گذشته</th>
                                <th class="p-2">دلیل</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800/50">
                            @foreach($expiredUsers as $account)
                                @php
                                    $expireDate = $account->expire_date ? Carbon\Carbon::parse($account->expire_date) : null;
                                    $daysSinceExpire = $expireDate ? $expireDate->diffInDays(now()) : 0;
                                    $reason = [];
                                    if ($expireDate && $expireDate->isPast()) {
                                        $reason[] = 'منقضی شده ('.$daysSinceExpire.' روز)';
                                    }
                                    if ($account->max_usage > 0 && $account->download_usage >= $account->max_usage) {
                                        $reason[] = 'حجم تمام شده';
                                    }
                                    $reasonText = implode(' - ', $reason);
                                @endphp
                                <tr class="hover:bg-zinc-900/30 transition">
                                    <td class="p-2"><input type="checkbox" wire:model="selectedUsers" value="{{ $account->id }}" class="rounded border-zinc-700 bg-zinc-900 text-orange-500"></td>
                                    <td class="p-2 font-mono text-white">{{ $account->username }}</td>
                                    <td class="p-2 text-zinc-400">{{ $account->service_group }}</td>
                                    <td class="p-2 text-zinc-400">{{ $expireDate ? $expireDate->toDateString() : '-' }}</td>
                                    <td class="p-2 font-mono {{ $daysSinceExpire > 15 ? 'text-rose-400' : 'text-zinc-400' }}">{{ $daysSinceExpire }}</td>
                                    <td class="p-2 text-zinc-400 text-[10px]">{{ $reasonText }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($expiredUsers->hasPages())
                        <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-3">
                            <div class="text-xs text-zinc-500">
                                نمایش {{ $expiredUsers->firstItem() }} تا {{ $expiredUsers->lastItem() }} از {{ $expiredUsers->total() }} کاربر
                            </div>
                            <div>
                                {{ $expiredUsers->links() }}
                            </div>
                        </div>
                    @endif
                @elseif($isLoadingExpired)
                    <div class="py-8 text-center text-zinc-500 text-sm">⏳ در حال بارگذاری...</div>
                @else
                    <div class="py-8 text-center text-zinc-500 text-sm">✅ هیچ کاربر منقضی یا تمام‌شده‌ای یافت نشد.</div>
                @endif

            </div>
        </div>
    @endif

    {{-- Logs Sub Tab --}}
    @if($activeSubTab === 'logs')
        <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl p-5">
            <h4 class="text-sm font-bold text-white mb-4">لاگ‌های عملیات تعمیراتی</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-zinc-900/50 text-zinc-400 border-b border-zinc-800">
                    <tr>
                        <th class="p-3">ادمین</th>
                        <th class="p-3">عملیات</th>
                        <th class="p-3">هدف</th>
                        <th class="p-3">وضعیت</th>
                        <th class="p-3">پیام</th>
                        <th class="p-3">زمان</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                    @forelse(\App\Models\SystemMaintenanceLog::with('admin')->latest()->limit(50)->get() as $log)
                        <tr class="hover:bg-zinc-900/30 transition">
                            <td class="p-3 text-white">{{ $log->admin->name ?? '-' }}</td>
                            <td class="p-3 text-zinc-400">{{ $log->action }}</td>
                            <td class="p-3 text-zinc-400">{{ $log->target ?? '-' }}</td>
                            <td class="p-3">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $log->status === 'success' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                        {{ $log->status }}
                                    </span>
                            </td>
                            <td class="p-3 text-zinc-400 max-w-xs truncate">{{ $log->message }}</td>
                            <td class="p-3 text-zinc-500 text-[10px]">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-zinc-500">هیچ لاگی ثبت نشده است.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
