@php
    $failedBackupsCount = cache()->remember(
         'failed_backups_count',
         120,
         fn () => \App\Models\Backup::where('status', 'failed')->count()
     );
     // Match detail/create pages to exactly one menu entry.
     $adminMenuGroups = [
         ['id' => 'people', 'label' => 'کاربران و اکانت‌ها', 'icon' => 'users', 'items' => [
             ['label' => 'مشتریان', 'route' => 'admin.users.index', 'match' => ['admin.users.*']],
             ['label' => 'مدیران و نمایندگان', 'route' => 'admin.managers.list', 'match' => ['admin.managers.*']],
             ['label' => 'اکانت‌های سرویس', 'route' => 'admin.accounts.list', 'match' => ['admin.accounts.list', 'admin.accounts.show']],
             ['label' => 'صدور اکانت', 'route' => 'admin.accounts.create', 'match' => ['admin.accounts.create']],
             ['label' => 'رخدادهای اکانت', 'route' => 'admin.accounts.logs', 'match' => ['admin.accounts.logs']],
         ]],
         ['id' => 'finance', 'label' => 'سفارش‌ها و مالی', 'icon' => 'wallet', 'items' => [
             ['label' => 'سفارشات فروشگاه', 'route' => 'admin.store.orders', 'match' => ['admin.store.*']],
             ['label' => 'تراکنش‌های مالی', 'route' => 'admin.financial', 'match' => ['admin.financial']],
             ['label' => 'روش‌های پرداخت', 'route' => 'admin.payment.methods', 'match' => ['admin.payment.*']],
             ['label' => 'تأیید پرداخت', 'route' => 'admin.manage_charges', 'match' => ['admin.manage_charges']],
             ['label' => 'تخفیف و بونوس', 'route' => 'admin.promotions', 'match' => ['admin.promotions']],
         ]],
         ['id' => 'infra', 'label' => 'سیستم و زیرساخت', 'icon' => 'shield', 'items' => [
             ['label' => 'سرورها', 'route' => 'admin.nas.list', 'match' => ['admin.nas.*']],
             ['label' => 'گروه‌های سرویس', 'route' => 'admin.groups.list', 'match' => ['admin.groups.*']],
             ['label' => 'تعرفه‌های شارژ', 'route' => 'admin.charge.list', 'match' => ['admin.charge.*']],
             ['label' => 'وضعیت سرورها', 'route' => 'admin.system.monitor', 'match' => ['admin.system.monitor']],
             ['label' => 'بررسی سیستم', 'route' => 'admin.system.check', 'match' => ['admin.system.check']],
             ['label' => 'زیرساخت DNS Game', 'route' => 'admin.dns.infrastructure', 'match' => ['admin.dns.*']],
         ]],
         ['id' => 'content', 'label' => 'محتوا و پشتیبانی', 'icon' => 'ticket', 'items' => [
             ['label' => 'مدیریت پشتیبانی', 'route' => 'admin.support', 'match' => ['admin.support']],
             ['label' => 'مرکز دانلود', 'route' => 'admin.downloads', 'match' => ['admin.downloads']],
             ['label' => 'راهنماهای اتصال', 'route' => 'admin.tutorial', 'match' => ['admin.tutorial']],
             ['label' => 'پشتیبان‌گیری', 'route' => 'admin.backups', 'match' => ['admin.backups', 'admin.backups.*'], 'badge' => $failedBackupsCount ?? 0],
             ['label' => 'اطلاعیه‌ها', 'route' => 'admin.announcements', 'match' => ['admin.announcements']],
         ]],
     ];
@endphp
<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-zinc-950/80 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-cloak></div>
<aside id="admin-sidebar" :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'" class="admin-sidebar fixed inset-y-0 right-0 z-50 transition-transform duration-200 flex flex-col" aria-label="ناوبری پنل مدیریت">
    <div class="admin-sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" wire:navigate aria-label="داشبورد مدیریت"><x-ui.brand :name="setting('SITE_TITLE', 'همراه سیمرغ')" :logo="setting('SITE_LOGO')" subtitle="پنل مدیریت" /></a>
        <button type="button" @click="sidebarOpen = false" class="admin-sidebar-close lg:hidden" aria-label="بستن منو"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m6 6 12 12M6 18 18 6" /></svg></button>
    </div>
    <nav class="admin-sidebar-nav" aria-label="منوی مدیریت" @click="if ($event.target.closest('a')) sidebarOpen = false">
        <p class="admin-sidebar-caption">فضای مدیریت</p>
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="admin-menu-row {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
            <span class="admin-menu-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg></span>
            <span class="admin-menu-text">داشبورد</span>
        </a>
        <div class="admin-sidebar-divider"></div>
        @foreach($adminMenuGroups as $group)
            @php $groupActive = collect($group['items'])->contains(fn ($item) => request()->routeIs(...$item['match'])); @endphp
            <section class="admin-menu-group" x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }" wire:key="admin-nav-{{ $group['id'] }}">
                <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="admin-subnav-{{ $group['id'] }}" class="admin-menu-row admin-menu-toggle {{ $groupActive ? 'is-current-group' : '' }}">
                    <span class="admin-menu-icon"><x-ui.icon :name="$group['icon']" /></span>
                    <span class="admin-menu-text">{{ $group['label'] }}</span>
                    <svg class="admin-menu-chevron" :class="open ? '-rotate-90' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m14 7-5 5 5 5" /></svg>
                </button>
                <div id="admin-subnav-{{ $group['id'] }}" x-show="open" x-cloak x-transition.opacity.duration.150ms class="admin-menu-children">
                    @foreach($group['items'] as $item)
                        @php $itemActive = request()->routeIs(...$item['match']); @endphp
                        <a href="{{ route($item['route']) }}" wire:navigate class="admin-menu-child {{ $itemActive ? 'is-active' : '' }}" @if($itemActive) aria-current="page" @endif>
                            <span class="admin-menu-dot" aria-hidden="true"></span>
                            <span class="admin-menu-text">{{ $item['label'] }}</span>
                            @if($item['route'] === 'admin.store.orders' && ($pendingOrdersCount ?? 0) > 0)<span class="admin-menu-badge">{{ $pendingOrdersCount }}</span>@endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
        <div class="admin-sidebar-divider"></div>
        <a href="{{ route('admin.settings') }}" wire:navigate class="admin-menu-row {{ request()->routeIs('admin.settings') ? 'is-active' : '' }}" @if(request()->routeIs('admin.settings')) aria-current="page" @endif>
            <span class="admin-menu-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 6h16M4 12h16M4 18h16"/><circle cx="9" cy="6" r="2" fill="#0b1424"/><circle cx="15" cy="12" r="2" fill="#0b1424"/><circle cx="8" cy="18" r="2" fill="#0b1424"/></svg></span>
            <span class="admin-menu-text">تنظیمات سایت</span>
        </a>
    </nav>
    <div class="admin-sidebar-footer"><span class="admin-sidebar-status" aria-hidden="true"></span><span>پنل مدیریت سرویس</span></div>
</aside>
