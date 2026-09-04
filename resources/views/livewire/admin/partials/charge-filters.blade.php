<div class="bg-[#111722] border border-[#202938] rounded-xl p-4 space-y-3">
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <svg class="w-4 h-4 absolute right-3 top-2.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="filters.search"
                       type="text"
                       placeholder="جستجوی نام، شناسه، مبلغ..."
                       class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl pr-9 pl-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">
            </div>
        </div>

        <select wire:model.live="filters.status" class="bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">
            <option value="">همه وضعیت‌ها</option>
            <option value="pending">در انتظار</option>
            <option value="paid">پرداخت شده</option>
            <option value="verifying">در حال بررسی</option>
            <option value="approved">تأیید شده</option>
            <option value="rejected">رد شده</option>
            <option value="expired">منقضی شده</option>
        </select>

        <select wire:model.live="filters.match_status" class="bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">
            <option value="">همه تطبیق‌ها</option>
            <option value="pending">در انتظار</option>
            <option value="matched">تطبیق شده</option>
            <option value="unmatched">بدون تطبیق</option>
            <option value="manual">تطبیق دستی</option>
        </select>

        <input wire:model.live.debounce.300ms="filters.amount_min"
               type="number"
               placeholder="حداقل مبلغ"
               class="w-28 bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">
        <input wire:model.live.debounce.300ms="filters.amount_max"
               type="number"
               placeholder="حداکثر مبلغ"
               class="w-28 bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">

        <input wire:model.live="filters.date_from" type="date" class="bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">
        <span class="text-[#94A3B8] text-xs">تا</span>
        <input wire:model.live="filters.date_to" type="date" class="bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#F59E0B] transition">

        <label class="flex items-center gap-1.5 text-xs text-[#94A3B8] cursor-pointer">
            <input wire:model.live="filters.today" type="checkbox" class="w-4 h-4 rounded border-[#202938] bg-[#080B12] text-[#F59E0B] focus:ring-[#F59E0B]">
            امروز
        </label>
        <label class="flex items-center gap-1.5 text-xs text-[#94A3B8] cursor-pointer">
            <input wire:model.live="filters.unmatched_only" type="checkbox" class="w-4 h-4 rounded border-[#202938] bg-[#080B12] text-[#F59E0B] focus:ring-[#F59E0B]">
            فقط بدون تطبیق
        </label>

        <button wire:click="resetFilters" class="px-4 py-2.5 rounded-xl bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC] text-xs transition">
            بازنشانی
        </button>
    </div>
</div>
