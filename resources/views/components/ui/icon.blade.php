@props(['name' => 'sparkles'])
<svg {{ $attributes->merge(['class'=>'commerce-icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('wallet')<path d="M20 8V5a2 2 0 0 0-2-2H6a3 3 0 0 0 0 6h14v11H6a3 3 0 0 1-3-3V6"/><path d="M20 12h-5v4h5M16 14h.01"/>@break
        @case('calendar')<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M16 3v4M8 3v4M3 11h18M8 15h2M14 15h2"/>@break
        @case('ticket')<path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V7Z"/><path d="M14 5v3m0 3v2m0 3v3M7 9h3M7 13h3"/>@break
        @case('gift')<path d="M3 9h18v4H3zM5 13v8h14v-8M12 9v12"/><path d="M12 9H8a3 3 0 1 1 3-3l1 3Zm0 0h4a3 3 0 1 0-3-3l-1 3Z"/>@break
        @case('check')<path d="m5 12 4 4L19 6"/>@break
        @case('shield')<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/>@break
        @case('arrow')<path d="M19 12H5m6-6-6 6 6 6"/>@break
        @case('copy')<rect x="8" y="8" width="12" height="13" rx="2"/><path d="M16 8V3H4v13h4"/>@break
        @case('clock')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@break
        @case('users')<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m20 0v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/><circle cx="9" cy="7" r="4"/>@break
        @case('plus')<path d="M12 5v14M5 12h14"/>@break
        @case('alert')<circle cx="12" cy="12" r="9"/><path d="M12 7v6m0 4h.01"/>@break
        @default<path d="m12 3 2.4 6.6L21 12l-6.6 2.4L12 21l-2.4-6.6L3 12l6.6-2.4L12 3Z"/>
    @endswitch
</svg>
