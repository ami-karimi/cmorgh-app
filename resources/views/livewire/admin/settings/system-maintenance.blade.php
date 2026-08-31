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
    {{-- بخش سلامت سیستم در system-maintenance.blade.php --}}
    @if($activeSubTab === 'health')
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-bold text-white">بررسی مغایرت سرویس‌ها</h4>
                    <p class="text-xs text-zinc-400">مقایسه دیتابیس با سرورهای MikroTik, WireGuard</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($healthIssues->whereIn('issue_type', ['orphan_peer_only', 'orphan_full', 'orphan_peer_config', 'config_without_account'])->where('status', 'open')->count() > 0)
                        <button wire:click="cleanupOrphanQueuesByServer"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-purple-500/20 flex items-center gap-2">
                            <span wire:loading.remove>🧹 پاکسازی Queueهای اورفان (گروهی)</span>
                            <span wire:loading>⏳</span>
                        </button>
                    @endif

                    <button wire:click="runHealthCheck"
                            wire:loading.attr="disabled"
                            class="px-5 py-2.5 bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs rounded-xl transition shadow-lg shadow-orange-500/20 flex items-center gap-2">
                        <span wire:loading.remove wire:target="runHealthCheck">🚀 شروع بررسی کامل</span>
                        <span wire:loading wire:target="runHealthCheck">⏳ در حال بررسی...</span>
                    </button>
                </div>
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

            <div class="bg-zinc-950/30 border border-zinc-800 rounded-xl p-4 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-zinc-400 ml-1">فیلتر:</span>

                    {{-- فیلتر سرویس --}}
                    <select wire:model.live="filterService" class="bg-zinc-900 border border-zinc-700 text-white text-xs rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-orange-500">
                        <option value="all">همه سرویس‌ها</option>
                        <option value="wireguard">WireGuard</option>
                        <option value="mikrotik">MikroTik</option>
                    </select>

                    {{-- فیلتر نوع Issue --}}
                    <select wire:model.live="filterIssueType" class="bg-zinc-900 border border-zinc-700 text-white text-xs rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-orange-500">
                        <option value="all">همه انواع</option>
                        <option value="orphan_peer_only">Orphan Peer Only</option>
                        <option value="orphan_peer_config">Orphan Peer + Config</option>
                        <option value="orphan_full">Orphan کامل</option>
                        <option value="account_without_service">اکانت بدون سرویس</option>
                        <option value="missing_peer">Peer گم‌شده</option>
                        <option value="status_mismatch">ناهماهنگی وضعیت</option>
                        <option value="expired_account">اکانت منقضی</option>
                        <option value="speed_mismatch">ناهماهنگی سرعت</option>
                        <option value="config_without_account">کانفیگ بدون اکانت</option>
                    </select>

                    {{-- فیلتر شدت --}}
                    <select wire:model.live="filterSeverity" class="bg-zinc-900 border border-zinc-700 text-white text-xs rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-orange-500">
                        <option value="all">همه شدت‌ها</option>
                        <option value="critical">⚠️ حیاتی</option>
                        <option value="warning">⚡ هشدار</option>
                        <option value="info">ℹ️ اطلاع‌رسانی</option>
                    </select>

                    {{-- فیلتر وضعیت --}}
                    <select wire:model.live="filterStatus" class="bg-zinc-900 border border-zinc-700 text-white text-xs rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-orange-500">
                        <option value="open">باز</option>
                        <option value="resolved">حل شده</option>
                        <option value="ignored">نادیده گرفته</option>
                        <option value="all">همه</option>
                    </select>

                    {{-- فیلتر سرور --}}
                    <select wire:model.live="filterServerId" class="bg-zinc-900 border border-zinc-700 text-white text-xs rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-orange-500">
                        <option value="all">همه سرورها</option>
                        @foreach($servers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>

                    {{-- جستجوی متنی --}}
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="filterSearch"
                               type="text"
                               placeholder="جستجوی نام کاربری..."
                               class="bg-zinc-900 border border-zinc-700 text-white text-xs rounded-lg px-3 py-1.5 pr-8 focus:ring-1 focus:ring-orange-500 w-36">
                        <svg class="w-3.5 h-3.5 text-zinc-500 absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    {{-- دکمه پاک کردن فیلترها --}}
                    <button wire:click="resetFilters" class="text-xs text-orange-400 hover:text-orange-300 transition font-bold px-2 py-1">
                        ✕ پاک کردن همه
                    </button>
                </div>

                {{-- نمایش تعداد Issues --}}
                <div class="text-xs text-zinc-500">
                    تعداد Issues: <span class="text-white font-bold">{{ $healthIssues->total() }}</span>
                    @if($healthIssues->total() > 0)
                        <span class="text-zinc-600">({{ $healthIssues->firstItem() ?? 0 }} - {{ $healthIssues->lastItem() ?? 0 }})</span>
                    @endif
                </div>
            </div>

            {{-- Issues Table with Pagination --}}
            @if($healthIssues && $healthIssues->count() > 0)


                <div class="bg-zinc-950/50 border border-zinc-800/80 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-zinc-900/50 text-zinc-400 border-b border-zinc-800">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">کاربر</th>
                                <th class="p-3">اکانت</th>
                                <th class="p-3">کانفیگ</th>
                                <th class="p-3">Peer</th>
                                <th class="p-3">نوع مشکل</th>
                                <th class="p-3">شدت</th>
                                <th class="p-3">جزئیات</th>
                                <th class="p-3 text-center">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800/50">
                            @foreach($healthIssues as $index => $issue)
                                <tr class="hover:bg-zinc-900/30 transition">
                                    <td class="p-3 text-zinc-500">{{ $healthIssues->firstItem() + $index }}</td>
                                    <td class="p-3 font-mono text-white">{{ $issue->username }}</td>
                                    <td class="p-3">
                                        <span class="text-[10px] font-bold {{ $issue->has_account ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $issue->has_account ? '✅ دارد' : '❌ ندارد' }}
                                        </span>
                                        @if($issue->is_expired)
                                            <span class="text-[9px] text-rose-400 block">منقضی</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <span class="text-[10px] font-bold {{ $issue->has_config ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $issue->has_config ? '✅ دارد' : '❌ ندارد' }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="text-[10px] font-bold {{ $issue->has_peer ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $issue->has_peer ? '✅ دارد' : '❌ ندارد' }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold
                                            {{ $issue->issue_type === 'missing_peer' ? 'bg-rose-500/20 text-rose-400' : '' }}
                                        {{ $issue->issue_type === 'orphan_peer_config' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                        {{ $issue->issue_type === 'account_without_service' ? 'bg-orange-500/20 text-orange-400' : '' }}
                                        {{ $issue->issue_type === 'orphan_full' ? 'bg-red-500/20 text-red-400' : '' }}
                                        {{ $issue->issue_type === 'orphan_peer_only' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                        {{ $issue->issue_type === 'config_without_account' ? 'bg-purple-500/20 text-purple-400' : '' }}
                                        {{ $issue->issue_type === 'speed_mismatch' ? 'bg-blue-500/20 text-blue-400' : '' }}">
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
                                    <td class="p-3 text-center">
                                        @if($issue->service === 'wireguard' && $issue->status === 'open')
                                            <div class="flex flex-wrap items-center justify-center gap-1.5">
                                                @if(in_array($issue->issue_type, ['account_without_service', 'orphan_peer_config']))
                                                    <button wire:click="handleWireguardAction({{ $issue->id }}, 'create_config_and_peer')"
                                                            wire:loading.attr="disabled"
                                                            class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[9px] font-bold rounded-lg transition">
                                                        ➕ ایجاد کانفیگ
                                                    </button>
                                                @endif

                                                @if($issue->issue_type === 'orphan_peer_only')
                                                    <button wire:click="handleWireguardAction({{ $issue->id }}, 'create_account')"
                                                            wire:loading.attr="disabled"
                                                            class="px-2.5 py-1 bg-green-600 hover:bg-green-500 text-white text-[9px] font-bold rounded-lg transition">
                                                        ➕ ایجاد اکانت
                                                    </button>
                                                @endif

                                                @if(in_array($issue->issue_type, ['missing_peer']))
                                                    <button wire:click="handleWireguardAction({{ $issue->id }}, 'recreate_peer')"
                                                            wire:loading.attr="disabled"
                                                            class="px-2.5 py-1 bg-blue-600 hover:bg-blue-500 text-white text-[9px] font-bold rounded-lg transition">
                                                        🔄 بازسازی Peer
                                                    </button>
                                                @endif

                                                @if(in_array($issue->issue_type, ['orphan_peer_only', 'orphan_full', 'orphan_peer_config', 'config_without_account']))
                                                    <button wire:click="handleWireguardAction({{ $issue->id }}, 'delete_orphan')"
                                                            wire:loading.attr="disabled"
                                                            class="px-2.5 py-1 bg-rose-600 hover:bg-rose-500 text-white text-[9px] font-bold rounded-lg transition">
                                                        🗑️ حذف
                                                    </button>
                                                @endif

                                                @if($issue->issue_type === 'speed_mismatch')
                                                    <button wire:click="handleWireguardAction({{ $issue->id }}, 'sync_speed')"
                                                            wire:loading.attr="disabled"
                                                            class="px-2.5 py-1 bg-purple-600 hover:bg-purple-500 text-white text-[9px] font-bold rounded-lg transition">
                                                        ⚡ همگام‌سازی
                                                    </button>
                                                @endif

                                                @if($issue->issue_type === 'expired_account')
                                                    <button wire:click="handleWireguardAction({{ $issue->id }}, 'delete_orphan')"
                                                            wire:loading.attr="disabled"
                                                            class="px-2.5 py-1 bg-rose-600 hover:bg-rose-500 text-white text-[9px] font-bold rounded-lg transition">
                                                        🗑️ حذف منقضی
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <button wire:click="ignoreIssue({{ $issue->id }})"
                                                    class="px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-white text-[9px] rounded-lg transition">
                                                نادیده گرفتن
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($healthIssues->hasPages())
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 p-4 border-t border-zinc-800 bg-zinc-900/30">
                            <div class="text-xs text-zinc-500">
                                نمایش {{ $healthIssues->firstItem() }} تا {{ $healthIssues->lastItem() }} از {{ $healthIssues->total() }} Issue
                            </div>
                            <div>
                                {{ $healthIssues->links() }}
                            </div>
                        </div>
                    @endif
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
                        @if($stats['total'] > 0)
                            <button wire:click="openBulkDeleteConfirm" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-rose-500/20 flex items-center gap-2">
                                🗑️ پاکسازی کلی ({{ $stats['total'] }})
                            </button>
                        @endif
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


                @if($showBulkConfirmModal)
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
                        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 max-w-md w-full shadow-2xl">
                            <h3 class="text-lg font-bold text-white mb-2">⚠️ تأیید پاکسازی کلی</h3>
                            <p class="text-sm text-zinc-400 mb-4">
                                آیا از حذف <span class="text-white font-bold">{{ $bulkTotalCount }}</span> کاربر منقضی اطمینان دارید؟
                                <br>
                                <span class="text-xs text-zinc-500">این عملیات غیرقابل بازگشت است و کاربران ابتدا از سرورهای خارجی حذف می‌شوند.</span>
                            </p>
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="$set('showBulkConfirmModal', false)" class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white text-sm font-bold rounded-xl transition">انصراف</button>
                                <button wire:click="bulkDeleteAll" wire:loading.attr="disabled" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-sm font-bold rounded-xl transition shadow-lg flex items-center gap-2">
                                    <span wire:loading.remove>🗑️ تأیید و حذف همه</span>
                                    <span wire:loading>⏳ در حال پردازش...</span>
                                </button>
                            </div>
                        </div>
                    </div>
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
