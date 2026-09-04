<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#080B12]/90 backdrop-blur-sm">
    <div class="fixed inset-0" wire:click="$set('showMatchModal', false)"></div>
    <div class="relative w-full max-w-md bg-[#111722] border border-[#202938] rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between p-4 border-b border-[#202938]">
            <h3 class="text-sm font-bold text-[#F8FAFC]">تطبیق دستی درخواست</h3>
            <button wire:click="$set('showMatchModal', false)" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 space-y-4">
            <p class="text-xs text-[#94A3B8]">یک پیام بانکی برای تطبیق با این درخواست انتخاب کنید.</p>
            <div>
                <label class="block text-[11px] text-[#94A3B8] font-bold mb-1.5">پیام بانکی</label>
                <select wire:model="matchMessageId" class="w-full bg-[#080B12] border border-[#202938] text-[#F8FAFC] text-sm rounded-xl px-4 py-3 focus:ring-1 focus:ring-[#F59E0B] focus:outline-none transition">
                    <option value="">انتخاب کنید...</option>
                    @foreach($matchableMessages as $msg)
                        <option value="{{ $msg->id }}">#{{ $msg->id }} - {{ number_format($msg->deposit_amount) }} تومان - {{ $msg->transaction_datetime?->format('Y/m/d H:i') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-3 border-t border-[#202938]">
                <button wire:click="confirmMatch" class="flex-1 py-2.5 rounded-xl bg-[#10B981] hover:bg-[#059669] text-white text-xs font-bold transition">
                    تأیید تطبیق
                </button>
                <button wire:click="$set('showMatchModal', false)" class="flex-1 py-2.5 rounded-xl bg-[#202938] hover:bg-[#333] text-[#94A3B8] text-xs font-bold transition">
                    انصراف
                </button>
            </div>
        </div>
    </div>
</div>
