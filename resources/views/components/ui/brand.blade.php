@props(['name' => 'همراه سیمرغ', 'logo' => null, 'subtitle' => null])
<span class="ui-brand">
    <span class="ui-brand-mark" aria-hidden="true">
        @if($logo)
            <img src="{{ $logo }}" alt="">
        @else
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3 3 8l9 5 9-5-9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>
            </svg>
        @endif
    </span>
    <span class="min-w-0">
        <span class="ui-brand-name block truncate max-w-[200px]">{{ $name }}</span>
        @if($subtitle)<span class="ui-eyebrow">{{ $subtitle }}</span>@endif
    </span>
</span>
