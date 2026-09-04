<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-sm">
    <div class="fixed inset-0" wire:click="$set('showRejectModal', false)"></div>
    <div class="relative w-full max-w-md bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between p-4 border-b border-[#202938]">
            <h3 class="text-sm font-bold text-[#F8FAFC]">رد درخواست</h3>
            <button wire:click="$set('showRejectModal', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 space-y-4">
            <p class="text-xs text-[#94A3B8]">لطفاً دلیل رد کردن را وارد کنید.</p>
            <div>
                <label class="block text-[11px] text-[#94A3B8] font-bold mb-1.5">دلیل رد <span class="text-[#EF4444]">*</span></label>
                <textarea wire:model="rejectReason" rows="3" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl px-4 py-3 focus:ring-1 focus:ring-[#F59E0B] focus:outline-none transition" placeholder="دلیل را وارد کنید..."></textarea>
                @error('rejectReason') <span class="text-[#EF4444] text-[10px]">{{ $message }}</span> @enderror
            </div>
            <div class="flex gap-3 pt-3 border-t border-[#202938]">
                <button wire:click="confirmReject" class="flex-1 py-2.5 rounded-xl bg-[#EF4444] hover:bg-[#DC2626] text-white text-xs font-bold transition">
                    تأیید رد
                </button>
                <button wire:click="$set('showRejectModal', false)" class="flex-1 py-2.5 rounded-xl bg-[#202938] hover:bg-[#333] text-[#94A3B8] text-xs font-bold transition">
                    انصراف
                </button>
            </div>
        </div>
    </div>
</div>
