<div class="space-y-6 animate-fade-in">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">مدیریت اطلاعیه‌ها</h1>
            <p class="text-sm text-zinc-500 mt-1">ارسال پیام‌های هدفمند به نمایندگان و مشتریان</p>
        </div>
        <button wire:click="$set('isModalOpen', true)" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg shadow-orange-500/20 transition">
            + اطلاعیه جدید
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($announcements as $ann)
            <div class="bg-[#111827] border {{ $ann->is_active ? 'border-orange-500/30' : 'border-zinc-800' }} rounded-3xl p-6 shadow-xl relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $ann->title }}</h3>
                        <span class="text-[10px] text-zinc-500">{{ \Morilog\Jalali\Jalalian::forge($ann->created_at)->format('Y/m/d H:i') }}</span>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                        {{ $ann->target === 'all' ? 'bg-blue-500/10 text-blue-500' : ($ann->target === 'agents' ? 'bg-purple-500/10 text-purple-500' : 'bg-emerald-500/10 text-emerald-500') }}">
                        نمایش به: {{ $ann->target === 'all' ? 'همه' : ($ann->target === 'agents' ? 'نمایندگان' : 'مشتریان') }}
                    </span>
                </div>
                <p class="text-sm text-zinc-400 mb-6 leading-relaxed">{{ $ann->content }}</p>
                <div class="flex justify-between items-center pt-4 border-t border-zinc-800/50">
                    <button wire:click="toggleActive({{ $ann->id }})" class="text-xs font-bold {{ $ann->is_active ? 'text-emerald-500' : 'text-zinc-500' }}">
                        {{ $ann->is_active ? '✅ فعال (در حال نمایش)' : '❌ غیرفعال' }}
                    </button>
                    <button wire:click="delete({{ $ann->id }})" class="text-xs text-rose-500 hover:text-rose-400 font-bold">حذف</button>
                </div>
            </div>
        @endforeach
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-lg bg-[#111827] border border-zinc-800 rounded-3xl p-6 shadow-2xl">
                <h3 class="text-lg font-black text-white mb-6">ثبت اطلاعیه جدید</h3>
                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">عنوان اطلاعیه</label>
                        <input wire:model="title" type="text" class="w-full bg-[#09090b] border border-zinc-800 text-white rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">مخاطب هدف</label>
                        <select wire:model="target" class="w-full bg-[#09090b] border border-zinc-800 text-white rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-500">
                            <option value="all">همه کاربران (نمایندگان + مشتریان)</option>
                            <option value="agents">فقط نمایندگان</option>
                            <option value="customers">فقط مشتریان</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 mb-1.5">متن پیام</label>
                        <textarea wire:model="content" rows="4" class="w-full bg-[#09090b] border border-zinc-800 text-white rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-500"></textarea>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-5 py-3 rounded-xl bg-zinc-800 text-white text-sm font-bold flex-1">انصراف</button>
                        <button type="submit" class="px-5 py-3 rounded-xl bg-orange-500 text-white text-sm font-black flex-1">انتشار پیام</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
