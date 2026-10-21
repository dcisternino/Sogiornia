@props(['slug'])

{{-- Icone leggere per le sezioni della guida, senza dipendenze esterne. --}}
@php
    $icon = match (true) {
        str_contains($slug, 'wifi'), str_contains($slug, 'wi-fi') => 'wifi',
        str_contains($slug, 'check'), str_contains($slug, 'arrivo') => 'home',
        str_contains($slug, 'regol'), str_contains($slug, 'casa') => 'rules',
        str_contains($slug, 'numer'), str_contains($slug, 'contatt'), str_contains($slug, 'telefon') => 'phone',
        default => 'spark',
    };
@endphp
<svg {{ $attributes->merge(['class' => 'guest-section-icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($icon)
        @case('wifi')
            <path d="M3 8.8a14 14 0 0 1 18 0M6 12a9.4 9.4 0 0 1 12 0M9.2 15.2a4.5 4.5 0 0 1 5.6 0"/>
            <circle cx="12" cy="18.3" r=".8" fill="currentColor" stroke="none"/>
            @break
        @case('home')
            <path d="m4.5 10 7.5-6.5 7.5 6.5v10h-15zM9.5 20v-7h5v7"/>
            @break
        @case('rules')
            <path d="M9 5h11M9 12h11M9 19h11M3.5 5h1M3.5 12h1M3.5 19h1"/>
            @break
        @case('phone')
            <path d="m7.3 3.5 3 4.5-2.2 2.2a13 13 0 0 0 5.7 5.7l2.2-2.2 4.5 3c-.6 3-2 4-4.5 3.3A19.8 19.8 0 0 1 4 8c-.7-2.5.3-3.9 3.3-4.5Z"/>
            @break
        @default
            <path d="M12 2.5c1.3 5.5 4 8.2 9.5 9.5-5.5 1.3-8.2 4-9.5 9.5C10.7 16 8 13.3 2.5 12 8 10.7 10.7 8 12 2.5Z" fill="currentColor" stroke="none"/>
    @endswitch
</svg>
