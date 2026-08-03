<div class="space-y-6 pb-12 font-sans" wire:key="tutorial-manager-view">

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <style>
        .ql-toolbar.ql-snow { border: 1px solid #3f3f46; border-top-left-radius: 1rem; border-top-right-radius: 1rem; background-color: #18181b; }
        .ql-container.ql-snow { border: 1px solid #3f3f46; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem; background-color: #09090b; color: #e4e4e7; font-family: inherit; font-size: 0.875rem; min-height: 250px; }
        .ql-snow .ql-stroke { stroke: #a1a1aa; }
        .ql-snow .ql-fill, .ql-snow .ql-stroke.ql-fill { fill: #a1a1aa; }
        .ql-snow.ql-toolbar button:hover .ql-stroke { stroke: #3b82f6; }
        .ql-snow.ql-toolbar button:hover .ql-fill { fill: #3b82f6; }
        .ql-editor { direction: rtl; text-align: right; }
        .ql-editor img { max-width: 100%; border-radius: 0.75rem; margin: 15px 0; border: 1px solid #27272a; }
        .ql-editor iframe { width: 100%; height: 400px; border-radius: 0.75rem; margin: 15px 0; border: 1px solid #27272a; }
    </style>

    <div class="relative overflow-hidden bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/60 rounded-[2rem] p-6 shadow-2xl">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 font-black text-xl shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-white tracking-tight">مدیریت آموزش‌ها و اتصالات</h1>
                    <p class="text-xs text-zinc-400 mt-1">ساخت پایگاه دانش (متن، فایل پیوست و ویدیو) برای نمایندگان و مشتریان</p>
                </div>
            </div>
            <button wire:click="openModal" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-blue-500/25 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                افزودن آموزش جدید
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($tutorials as $tutorial)
            <div class="bg-zinc-900/50 border {{ $tutorial->is_published ? 'border-zinc-800/60' : 'border-rose-900/50 bg-rose-950/10' }} rounded-[2rem] p-6 hover:-translate-y-1 transition-all duration-300 shadow-xl flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="px-3 py-1 rounded-lg text-[10px] font-bold bg-zinc-800 text-zinc-300 border border-zinc-700 uppercase tracking-wider mb-2 inline-block">{{ $tutorial->platform }}</span>
                        <h3 class="text-base font-bold text-white leading-tight">{{ $tutorial->title }}</h3>
                        <span class="text-xs text-zinc-500 mt-2 block font-mono" dir="ltr">Protocol: {{ $tutorial->protocol ?? 'General' }}</span>
                    </div>
                    <button wire:click="togglePublish({{ $tutorial->id }})" class="relative h-5 w-9 rounded-full transition-colors duration-200 {{ $tutorial->is_published ? 'bg-emerald-500' : 'bg-zinc-700' }}">
                        <span class="absolute top-[2px] bg-white w-4 h-4 rounded-full transition-transform duration-200 {{ $tutorial->is_published ? 'left-[2px]' : 'translate-x-[16px] left-[2px]' }}"></span>
                    </button>
                </div>

                @php
                    $fileList = [];
                    if (is_array($tutorial->attachments)) {
                        $fileList = $tutorial->attachments;
                    } elseif (is_string($tutorial->attachments) && !empty($tutorial->attachments)) {
                        $decoded = json_decode($tutorial->attachments, true);
                        $fileList = is_array($decoded) ? $decoded : [$tutorial->attachments];
                    }
                @endphp

                @if(!empty($fileList))
                    <div class="my-3 space-y-2">
                        @foreach($fileList as $index => $file)
                            @php
                                $fileName = basename($file);
                            @endphp
                            <a href="{{ Storage::url($file) }}" target="_blank" download class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20 transition-colors group">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span class="text-purple-400 text-[11px] font-mono font-bold truncate">{{ $fileName }}</span>
                                </div>
                                <svg class="w-4 h-4 text-purple-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="mt-auto pt-5 flex items-center gap-3 border-t border-zinc-800/80">
                    <button wire:click="edit({{ $tutorial->id }})" class="flex-1 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-xs rounded-xl transition-all">ویرایش آموزش</button>
                    <button wire:click="delete({{ $tutorial->id }})" onclick="confirm('آیا مطمئن هستید؟') || event.stopImmediatePropagation()" class="px-4 py-2.5 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white font-bold text-xs rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center border-2 border-dashed border-zinc-800/60 rounded-[2rem] bg-zinc-900/20">
                <p class="text-zinc-400 font-bold text-sm">هیچ آموزشی یافت نشد. اولین آموزش را ایجاد کنید.</p>
            </div>
        @endforelse
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-md transition-all animate-fade-in" wire:key="tutorial-modal">
            <div class="relative w-full max-w-5xl bg-zinc-900 border border-zinc-700/60 rounded-[2.5rem] shadow-2xl flex flex-col max-h-[95vh]">

                <div class="flex items-center justify-between px-8 py-5 border-b border-zinc-800/80 bg-zinc-900">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        {{ $tutorial_id ? 'ویرایش آموزش' : 'ثبت آموزش جدید' }}
                    </h2>
                    <button wire:click="closeModal" class="p-2 text-zinc-400 hover:text-white bg-zinc-800/60 hover:bg-zinc-700 rounded-full transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div class="p-8 overflow-y-auto space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-[11px] font-bold text-zinc-400 mb-2">عنوان آموزش</label>
                            <input wire:model="title" type="text" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/30">
                            @error('title') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[11px] font-bold text-zinc-400 mb-2">پلتفرم هدف</label>
                            <select wire:model="platform" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/30">
                                <option value="Android">Android</option>
                                <option value="iOS">iOS (iPhone/iPad)</option>
                                <option value="Windows">Windows</option>
                                <option value="Mac">Mac OS</option>
                                <option value="Router">Modem / Router</option>
                                <option value="General">عمومی</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[11px] font-bold text-zinc-400 mb-2">پروتکل ارتباطی</label>
                            <select wire:model="protocol" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl p-3.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/30">
                                <option value="WireGuard">WireGuard</option>
                                <option value="L2TP">L2TP/IPsec</option>
                                <option value="Cisco">Cisco AnyConnect</option>
                                <option value="OpenVPN">OpenVPN</option>
                                <option value="V2ray">V2ray / Xray</option>
                                <option value="">بدون پروتکل خاص</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-zinc-950/50 border border-zinc-800 rounded-[1.5rem] p-5">
                        <label class="block text-[11px] font-bold text-zinc-400 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            پیوست فایل‌ها (انتخاب همزمان چندین فایل مجاز است)
                        </label>

                        <div class="flex flex-col xl:flex-row gap-5 items-start">

                            <div class="relative w-full xl:w-1/3">
                                <input wire:model="attachments" type="file" id="file-upload" class="hidden" multiple />
                                <label for="file-upload" class="cursor-pointer w-full flex flex-col items-center justify-center py-6 border-2 border-dashed border-zinc-700 hover:border-purple-500 hover:bg-purple-500/5 rounded-2xl transition-all">
                                    <svg class="w-7 h-7 text-zinc-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <span class="text-[11px] font-bold text-zinc-400">برای انتخاب فایل‌ها کلیک کنید</span>
                                    <span class="text-[9px] font-bold text-zinc-600 mt-1">امکان انتخاب چند فایل با هم</span>
                                </label>
                                <div wire:loading wire:target="attachments" class="text-[10px] font-bold text-emerald-400 mt-2 text-center w-full">در حال بارگذاری فایل‌ها...</div>
                                @error('attachments.*') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="w-full xl:w-2/3 flex flex-col gap-3">

                                @if(!empty($attachments))
                                    <div class="p-3 bg-emerald-500/5 border border-emerald-500/20 rounded-xl">
                                        <span class="text-[10px] font-bold text-emerald-500 block mb-2">فایل‌های آماده ذخیره:</span>
                                        <div class="space-y-2">
                                            @foreach($attachments as $index => $file)
                                                <div class="flex items-center justify-between px-3 py-2 bg-zinc-900 rounded-lg">
                                                    <span class="text-[10px] text-zinc-300 font-mono truncate max-w-[200px]">{{ $file->getClientOriginalName() }}</span>
                                                    <button type="button" wire:click="removeNewAttachment({{ $index }})" class="text-[10px] text-rose-400 hover:text-rose-300 font-bold">حذف</button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($existing_attachments))
                                    <div class="p-3 bg-zinc-900 border border-zinc-800 rounded-xl">
                                        <span class="text-[10px] font-bold text-zinc-500 block mb-2">فایل‌های از قبل ذخیره شده:</span>
                                        <div class="space-y-2">
                                            @foreach($existing_attachments as $index => $file)
                                                <div class="flex items-center justify-between px-3 py-2 bg-zinc-950 border border-zinc-800 rounded-lg group">
                                                    <div class="flex items-center gap-2 overflow-hidden">
                                                        <svg class="w-3.5 h-3.5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                        <span class="text-[10px] text-zinc-400 font-mono truncate max-w-[150px]">{{ basename($file) }}</span>
                                                    </div>
                                                    <button type="button" wire:click="deleteExistingAttachment({{ $index }})" class="px-2.5 py-1 bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white rounded text-[9px] font-bold transition-colors">حذف فایل</button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    <div wire:ignore x-data="{
                            content: @entangle('content'),
                            init() {
                                let quill = new Quill(this.$refs.quillEditor, {
                                    theme: 'snow',
                                    placeholder: 'متن آموزش، لینک ویدیو یا عکس‌ها را اینجا قرار دهید...',
                                    modules: {
                                        toolbar: [
                                            [{ 'header': [1, 2, 3, false] }],
                                            ['bold', 'italic', 'underline', 'strike'],
                                            ['blockquote', 'code-block'],
                                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                            [{ 'color': [] }, { 'background': [] }],
                                            [{ 'align': [] }, { 'direction': 'rtl' }],
                                            ['link', 'image', 'video'],
                                            ['clean']
                                        ]
                                    }
                                });
                                quill.root.innerHTML = this.content;
                                quill.on('text-change', () => { this.content = quill.root.innerHTML; });
                            }
                        }">
                        <label class="block text-[11px] font-bold text-zinc-400 mb-2">محتوای کامل آموزش (پشتیبانی از عکس، ویدیو، لیست)</label>
                        <div x-ref="quillEditor"></div>
                    </div>
                    @error('content') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="px-8 py-5 border-t border-zinc-800/80 bg-zinc-900 flex justify-end gap-3">
                    <button wire:click="closeModal" class="px-6 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-xs rounded-xl transition-all">انصراف</button>
                    <button wire:click="save" wire:loading.attr="disabled" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                        <svg wire:loading wire:target="save, attachment" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4-4H4z"></path></svg>
                        ذخیره و انتشار
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
