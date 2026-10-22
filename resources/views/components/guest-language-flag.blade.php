@props(['locale'])
{{-- Bandiere locali in SVG, indipendenti dalle emoji e senza richieste a servizi esterni. --}}
<svg {{ $attributes->merge(['class' => 'guest-language-flag']) }} viewBox="0 0 30 20" aria-hidden="true" focusable="false">
    @switch($locale)
        @case('en')
            <path fill="#253b73" d="M0 0h30v20H0z"/><path stroke="#fff" stroke-width="5" d="m0 0 30 20M30 0 0 20"/><path stroke="#cf3544" stroke-width="2" d="m0 0 30 20M30 0 0 20"/><path stroke="#fff" stroke-width="7" d="M15 0v20M0 10h30"/><path stroke="#cf3544" stroke-width="4" d="M15 0v20M0 10h30"/>
            @break
        @case('es')
            <path fill="#ba2637" d="M0 0h30v20H0z"/><path fill="#f3c748" d="M0 5h30v10H0z"/>
            @break
        @case('de')
            <path fill="#252525" d="M0 0h30v7H0z"/><path fill="#c63a3d" d="M0 7h30v6H0z"/><path fill="#edc14d" d="M0 13h30v7H0z"/>
            @break
        @default
            <path fill="#16945b" d="M0 0h10v20H0z"/><path fill="#fff" d="M10 0h10v20H10z"/><path fill="#d84141" d="M20 0h10v20H20z"/>
    @endswitch
</svg>
