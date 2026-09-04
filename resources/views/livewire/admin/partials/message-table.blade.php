<div class="bg-[#111722] border border-[#202938] rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right text-xs">
            <thead class="bg-[#080B12] border-b border-[#202938]">
            <tr>
                <th class="p-3 rounded-r-xl">شناسه</th>
                <th class="p-3">شماره حساب</th>
                <th class="p-3">مبلغ</th>
                <th class="p-3">تاریخ تراکنش</th>
                <th class="p-3">وضعیت</th>
                <th class="p-3">درخواست مرتبط</th>
                <th class="p-3 rounded-l-xl">عملیات</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-[#202938]">
            @forelse($messages as $message)
                <tr class="hover:bg-[#171E2B]/40 transition">
                    <td class="p-3 font-mono text-[#F8FAFC]">#{{ $message->id }}</td>
                    <td class="p-3 font-mono text-[#F8FAFC]">{{ $message->account_number }}</td>
                    <td class="p-3 font-mono font-bold text-[#F59E0B]">{{ number_format($message->deposit_amount) }}</td>
                    <td class="p-3 text-[10px] text-[#94A3B8] font-mono">{{ $message->transaction_datetime?->format('Y/m/d H:i') ?? '-' }}</td>
                    <td class="p-3">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold border {{ $message->processed ? 'bg-green-500/10 text-green-500 border-green-500/20' : 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20' }}">
                                {{ $message->processed ? 'پردازش شده' : 'در انتظار' }}
                            </span>
                    </td>
                    <td class="p-3">
                        @if($message->matchedRequest)
                            <a href="#" wire:click="showRequestDetail({{ $message->matchedRequest->id }})" class="text-[#3B82F6] hover:underline">
                                #{{ $message->matchedRequest->id }}
                            </a>
                            <div class="text-[10px] text-[#94A3B8]">{{ $message->matchedRequest->user?->name ?? '' }}</div>
                        @else
                            <span class="text-[#94A3B8]">—</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <div class="flex items-center gap-1.5">
                            <button wire:click="showMessageDetail({{ $message->id }})" class="p-1.5 rounded-lg bg-[#202938] hover:bg-[#3B82F6] text-[#94A3B8] hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            @if(!$message->processed)
                                <button wire:click="reprocessMessage({{ $message->id }})" class="p-1.5 rounded-lg bg-[#202938] hover:bg-[#F59E0B] text-[#94A3B8] hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-[#94A3B8]">هیچ پیام بانکی یافت نشد.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-t border-[#202938]">
        {{ $messages->links() }}
    </div>
</div>
