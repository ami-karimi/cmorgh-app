@if(session()->has('toast_message'))
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-6 left-6 z-[200] max-w-sm bg-[#111722] border border-[#202938] rounded-2xl p-4 shadow-2xl">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-full {{ session('toast_type') === 'success' ? 'bg-[#10B981]/20 text-[#10B981]' : 'bg-[#EF4444]/20 text-[#EF4444]' }} flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="{{ session('toast_type') === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-[#F8FAFC]">{{ session('toast_title') ?? session('toast_message') }}</p>
                <p class="text-xs text-[#94A3B8] mt-0.5">{{ session('toast_message') }}</p>
            </div>
            <button @click="show = false" class="mr-auto text-[#94A3B8] hover:text-[#F8FAFC]">✕</button>
        </div>
    </div>
@endif
