<div class="bg-[#111722] border border-[#202938] rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right text-xs">
            <thead class="bg-[#080B12] border-b border-[#202938]">
            <tr>
                <th class="p-3 rounded-r-xl cursor-pointer hover:text-[#F59E0B] transition" wire:click="sortBy('id')">
                    شناسه
                    @if($sortField === 'id')
                        <span class="text-[#F59E0B]">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
                <th class="p-3 cursor-pointer hover:text-[#F59E0B] transition" wire:click="sortBy('user_id')">نماینده</th>
                <th class="p-3 cursor-pointer hover:text-[#F59E0B] transition" wire:click="sortBy('requested_amount')">مبلغ درخواستی</th>
                <th class="p-3 cursor-pointer hover:text-[#F59E0B] transition" wire:click="sortBy('payable_amount')">مبلغ یونیک</th>
                <th class="p-3">وضعیت</th>
                <th class="p-3">تطبیق</th>
                <th class="p-3 cursor-pointer hover:text-[#F59E0B] transition" wire:click="sortBy('created_at')">تاریخ ثبت</th>
                <th class="p-3 rounded-l-xl">عملیات</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-[#202938]">
            @forelse($requests as $request)
                <tr class="hover:bg-[#171E2B]/40 transition">
                    <td class="p-3 font-mono text-[#F8FAFC]">#{{ $request->id }}</td>
                    <td class="p-3">
                        <div class="font-bold text-[#F8FAFC]">{{ $request->user?->name ?? 'نامشخص' }}</div>
                        <div class="text-[10px] text-[#94A3B8]">@ {{ $request->user?->username ?? '' }}</div>
                    </td>
                    <td class="p-3 font-mono font-bold text-[#F8FAFC]">{{ number_format($request->requested_amount) }}</td>
                    <td class="p-3 font-mono font-bold text-[#F59E0B]">{{ number_format($request->payable_amount) }}</td>
                    <td class="p-3">
                        @php
                            $colors = ['pending'=>'bg-yellow-500/10 text-yellow-500','paid'=>'bg-blue-500/10 text-blue-500','verifying'=>'bg-purple-500/10 text-purple-500','approved'=>'bg-green-500/10 text-green-500','rejected'=>'bg-red-500/10 text-red-500','expired'=>'bg-gray-500/10 text-gray-500'];
                            $labels = ['pending'=>'در انتظار','paid'=>'پرداخت شده','verifying'=>'در حال بررسی','approved'=>'تأیید شده','rejected'=>'رد شده','expired'=>'منقضی شده'];
                        @endphp
                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold border {{ $colors[$request->status] ?? 'bg-gray-500/10 text-gray-500' }}">
                                {{ $labels[$request->status] ?? $request->status }}
                            </span>
                    </td>
                    <td class="p-3">
                        @if($request->matched_bank_message_id)
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold border bg-green-500/10 text-green-500 border-green-500/20">
                                    {{ $request->match_status === 'manual' ? 'دستی' : 'تطبیق شده' }}
                                </span>
                        @else
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold border bg-yellow-500/10 text-yellow-500 border-yellow-500/20">
                                    در انتظار
                                </span>
                        @endif
                    </td>
                    <td class="p-3 text-[10px] text-[#94A3B8] font-mono">{{ $request->created_at->format('Y/m/d H:i') }}</td>
                    <td class="p-3">
                        <div class="flex items-center gap-1.5">
                            <button wire:click="showRequestDetail({{ $request->id }})" class="p-1.5 rounded-lg bg-[#202938] hover:bg-[#3B82F6] text-[#94A3B8] hover:text-white transition" title="جزئیات">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>

                            @if(in_array($request->status, ['pending', 'paid']))
                                <button wire:click="approve({{ $request->id }})"
                                        wire:confirm="آیا از تأیید این درخواست اطمینان دارید؟"
                                        class="p-1.5 rounded-lg bg-[#202938] hover:bg-[#10B981] text-[#94A3B8] hover:text-white transition" title="تأیید">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button wire:click="openRejectModal({{ $request->id }})" class="p-1.5 rounded-lg bg-[#202938] hover:bg-[#EF4444] text-[#94A3B8] hover:text-white transition" title="رد">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                @if(!$request->matched_bank_message_id)
                                    <button wire:click="openMatchModal({{ $request->id }})" class="p-1.5 rounded-lg bg-[#202938] hover:bg-[#F59E0B] text-[#94A3B8] hover:text-white transition" title="تطبیق دستی">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-8 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-[#94A3B8] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-sm text-[#94A3B8]">هیچ درخواستی با این فیلترها یافت نشد.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-t border-[#202938]">
        {{ $requests->links() }}
    </div>
</div>
