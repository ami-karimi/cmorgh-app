<div class="fixed inset-0 z-40 flex justify-end" x-show="drawerOpen" x-cloak>
    <div class="fixed inset-0 bg-[#080B12]/60" @click="drawerOpen = false"></div>

    <div class="relative w-full max-w-2xl bg-[#111722] border-r border-[#202938] shadow-2xl overflow-y-auto"
         x-show="drawerOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0">

        {{-- Header --}}
        <div class="sticky top-0 z-10 bg-[#111722] border-b border-[#202938] p-4 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#F8FAFC]">
                {{ $selectedRequest ? 'جزئیات درخواست #' . $selectedRequest->id : 'جزئیات پیام بانکی #' . $selectedMessage->id }}
            </h3>
            <button wire:click="closeDrawer" class="p-1.5 rounded-lg bg-[#202938] text-[#94A3B8] hover:text-[#F8FAFC]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Content --}}
        <div class="p-6 space-y-6">
            @if($selectedRequest)
                {{-- اطلاعات درخواست --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-[#94A3B8] text-xs">شناسه</span>
                        <div class="font-bold text-[#F8FAFC]">#{{ $selectedRequest->id }}</div>
                    </div>
                    <div>
                        <span class="text-[#94A3B8] text-xs">نماینده</span>
                        <div class="font-bold text-[#F8FAFC]">{{ $selectedRequest->user?->name ?? 'نامشخص' }}</div>
                        <div class="text-[10px] text-[#94A3B8]">@ {{ $selectedRequest->user?->username ?? '' }}</div>
                    </div>
                    <div>
                        <span class="text-[#94A3B8] text-xs">مبلغ درخواستی</span>
                        <div class="font-bold text-[#F8FAFC]">{{ number_format($selectedRequest->requested_amount) }} تومان</div>
                    </div>
                    <div>
                        <span class="text-[#94A3B8] text-xs">مبلغ یونیک (قابل پرداخت)</span>
                        <div class="font-bold text-[#F59E0B]">{{ number_format($selectedRequest->payable_amount) }} تومان</div>
                    </div>
                    <div>
                        <span class="text-[#94A3B8] text-xs">وضعیت</span>
                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold border {{ $colors[$selectedRequest->status] ?? 'bg-gray-500/10 text-gray-500' }}">
                            {{ $labels[$selectedRequest->status] ?? $selectedRequest->status }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[#94A3B8] text-xs">تطبیق</span>
                        @if($selectedRequest->matched_bank_message_id)
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold border bg-green-500/10 text-green-500 border-green-500/20">
                                {{ $selectedRequest->match_status === 'manual' ? 'دستی' : 'خودکار' }}
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold border bg-yellow-500/10 text-yellow-500 border-yellow-500/20">
                                در انتظار
                            </span>
                        @endif
                    </div>
                    <div>
                        <span class="text-[#94A3B8] text-xs">تاریخ ثبت</span>
                        <div class="font-mono text-[#F8FAFC]">{{ $selectedRequest->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                    @if($selectedRequest->expires_at)
                        <div>
                            <span class="text-[#94A3B8] text-xs">زمان انقضا</span>
                            <div class="font-mono text-[#F8FAFC]">{{ $selectedRequest->expires_at->format('Y/m/d H:i') }}</div>
                        </div>
                    @endif
                </div>

                {{-- پیام بانکی مرتبط --}}
                @if($selectedRequest->matchedBankMessage)
                    <div class="border-t border-[#202938] pt-4">
                        <h4 class="text-xs font-bold text-[#94A3B8] mb-2">پیام بانکی مرتبط</h4>
                        <div class="bg-[#080B12] border border-[#202938] rounded-xl p-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">شناسه</span>
                                <span class="font-bold text-[#F8FAFC]">#{{ $selectedRequest->matchedBankMessage->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">شماره حساب</span>
                                <span class="font-mono text-[#F8FAFC]">{{ $selectedRequest->matchedBankMessage->account_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">مبلغ</span>
                                <span class="font-bold text-[#F59E0B]">{{ number_format($selectedRequest->matchedBankMessage->deposit_amount) }} تومان</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">تاریخ تراکنش</span>
                                <span class="font-mono text-[#F8FAFC]">{{ $selectedRequest->matchedBankMessage->transaction_datetime?->format('Y/m/d H:i') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Timeline --}}
                <div class="border-t border-[#202938] pt-4">
                    <h4 class="text-xs font-bold text-[#94A3B8] mb-3">تاریخچه</h4>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-[#10B981] mt-1.5"></div>
                            <div>
                                <p class="text-xs font-bold text-[#F8FAFC]">ایجاد درخواست</p>
                                <p class="text-[10px] text-[#94A3B8]">{{ $selectedRequest->created_at->format('Y/m/d H:i') }}</p>
                            </div>
                        </div>
                        @if($selectedRequest->matched_bank_message_id)
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-[#3B82F6] mt-1.5"></div>
                                <div>
                                    <p class="text-xs font-bold text-[#F8FAFC]">تطبیق با پیام بانکی</p>
                                    <p class="text-[10px] text-[#94A3B8]">{{ $selectedRequest->matched_at?->format('Y/m/d H:i') ?? 'نامشخص' }}</p>
                                </div>
                            </div>
                        @endif
                        @if($selectedRequest->status === 'approved')
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-[#F59E0B] mt-1.5"></div>
                                <div>
                                    <p class="text-xs font-bold text-[#F8FAFC]">تأیید نهایی</p>
                                    <p class="text-[10px] text-[#94A3B8]">{{ $selectedRequest->updated_at->format('Y/m/d H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($selectedRequest->status === 'rejected')
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-[#EF4444] mt-1.5"></div>
                                <div>
                                    <p class="text-xs font-bold text-[#EF4444]">رد شده</p>
                                    <p class="text-[10px] text-[#94A3B8]">{{ $selectedRequest->updated_at->format('Y/m/d H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($selectedMessage)
                {{-- اطلاعات پیام بانکی --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-[#94A3B8] text-xs">شناسه</span>
                        <div class="font-bold text-[#F8FAFC]">#{{ $selectedMessage->id }}</div>
                    </div>
                    <div>
                        <span class="text-[#94A3B8] text-xs">شماره حساب</span>
                        <div class="font-mono text-[#F8FAFC]">{{ $selectedMessage->account_number }}</div>
                    </div>
                    <div>
                        <span class="text-[#94A3B8] text-xs">مبلغ</span>
                        <div class="font-bold text-[#F59E0B]">{{ number_format($selectedMessage->deposit_amount) }} تومان</div>
                    </div>
                    <div>
                        <span class="text-[#94A3B8] text-xs">مانده</span>
                        <div class="font-mono text-[#F8FAFC]">{{ number_format($selectedMessage->balance) }} تومان</div>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[#94A3B8] text-xs">تاریخ و ساعت تراکنش</span>
                        <div class="font-mono text-[#F8FAFC]">{{ $selectedMessage->transaction_datetime?->format('Y/m/d H:i') ?? '-' }}</div>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[#94A3B8] text-xs">وضعیت</span>
                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold border {{ $selectedMessage->processed ? 'bg-green-500/10 text-green-500 border-green-500/20' : 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20' }}">
                            {{ $selectedMessage->processed ? 'پردازش شده' : 'در انتظار' }}
                        </span>
                    </div>
                    @if($selectedMessage->processed_at)
                        <div class="col-span-2">
                            <span class="text-[#94A3B8] text-xs">زمان پردازش</span>
                            <div class="font-mono text-[#F8FAFC]">{{ $selectedMessage->processed_at->format('Y/m/d H:i') }}</div>
                        </div>
                    @endif
                </div>

                {{-- درخواست مرتبط --}}
                @if($selectedMessage->matchedRequest)
                    <div class="border-t border-[#202938] pt-4">
                        <h4 class="text-xs font-bold text-[#94A3B8] mb-2">درخواست مرتبط</h4>
                        <div class="bg-[#080B12] border border-[#202938] rounded-xl p-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">شناسه</span>
                                <span class="font-bold text-[#F8FAFC]">#{{ $selectedMessage->matchedRequest->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">نماینده</span>
                                <span class="font-bold text-[#F8FAFC]">{{ $selectedMessage->matchedRequest->user?->name ?? 'نامشخص' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">مبلغ درخواستی</span>
                                <span class="font-bold text-[#F8FAFC]">{{ number_format($selectedMessage->matchedRequest->requested_amount) }} تومان</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#94A3B8]">وضعیت</span>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold border {{ $colors[$selectedMessage->matchedRequest->status] ?? 'bg-gray-500/10 text-gray-500' }}">
                                    {{ $labels[$selectedMessage->matchedRequest->status] ?? $selectedMessage->matchedRequest->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- متن خام پیام --}}
                @if($selectedMessage->raw_message)
                    <div class="border-t border-[#202938] pt-4">
                        <h4 class="text-xs font-bold text-[#94A3B8] mb-2">متن خام پیام</h4>
                        <div class="bg-[#080B12] border border-[#202938] rounded-xl p-4 text-xs font-mono text-[#94A3B8] break-words">
                            {{ $selectedMessage->raw_message }}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
