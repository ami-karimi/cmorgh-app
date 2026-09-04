<div class="space-y-6" x-data="{ drawerOpen: @entangle('showDrawer') }">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#F8FAFC] tracking-tight">مدیریت شارژ حساب</h1>
            <p class="text-xs text-[#94A3B8] mt-1">مدیریت درخواست‌های شارژ، پیام‌های بانکی و تطبیق خودکار تراکنش‌ها</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="$refresh" class="px-4 py-2 rounded-xl bg-[#202938] text-[#94A3B8] text-xs hover:text-[#F8FAFC] transition">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                بروزرسانی
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <p class="text-[10px] text-[#94A3B8]">در انتظار</p>
            <p class="text-lg font-bold text-yellow-500">{{ $stats['pending_count'] }}</p>
        </div>
        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <p class="text-[10px] text-[#94A3B8]">تأیید امروز</p>
            <p class="text-lg font-bold text-green-500">{{ $stats['approved_today'] }}</p>
        </div>
        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <p class="text-[10px] text-[#94A3B8]">رد امروز</p>
            <p class="text-lg font-bold text-red-500">{{ $stats['rejected_today'] }}</p>
        </div>
        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <p class="text-[10px] text-[#94A3B8]">پیام‌های جدید</p>
            <p class="text-lg font-bold text-blue-500">{{ $stats['unprocessed_messages'] }}</p>
        </div>
        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <p class="text-[10px] text-[#94A3B8]">بدون تطبیق</p>
            <p class="text-lg font-bold text-yellow-500">{{ $stats['unmatched_messages'] }}</p>
        </div>
        <div class="bg-[#111722] border border-[#202938] rounded-xl p-4">
            <p class="text-[10px] text-[#94A3B8]">مجموع شارژ امروز</p>
            <p class="text-lg font-bold text-emerald-500">{{ number_format($stats['total_today_amount']) }}</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-[#202938] pb-3">
        <button wire:click="$set('activeTab', 'requests')"
                class="px-5 py-2.5 text-xs font-bold rounded-xl transition flex items-center gap-2 {{ $activeTab === 'requests' ? 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938]/50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            درخواست‌های شارژ
        </button>
        <button wire:click="$set('activeTab', 'messages')"
                class="px-5 py-2.5 text-xs font-bold rounded-xl transition flex items-center gap-2 {{ $activeTab === 'messages' ? 'bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20' : 'text-[#94A3B8] hover:text-[#F8FAFC] hover:bg-[#202938]/50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            پیام‌های بانکی
        </button>
    </div>

    {{-- ========== TAB: REQUESTS ========== --}}
    @if($activeTab === 'requests')
        @include('livewire.admin.partials.charge-filters')
        @include('livewire.admin.partials.charge-table')
    @endif

    {{-- ========== TAB: MESSAGES ========== --}}
    @if($activeTab === 'messages')
        @include('livewire.admin.partials.message-table')
    @endif

    {{-- ========== DRAWER ========== --}}
    @if($showDrawer)
        @include('livewire.admin.partials.charge-drawer')
    @endif

    {{-- ========== MATCH MODAL ========== --}}
    @if($showMatchModal)
        @include('livewire.admin.partials.match-modal')
    @endif

    {{-- ========== REJECT MODAL ========== --}}
    @if($showRejectModal)
        @include('livewire.admin.partials.reject-modal')
    @endif

    {{-- Toast --}}
    @include('livewire.admin.partials.toast')
</div>
