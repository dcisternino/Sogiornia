{{-- Selettore delle lingue della guida; conserva pagina e token del soggiorno. --}}
@php
    $locales = config('guest.locales');
    $current = app()->getLocale();
@endphp
<div {{ $attributes->class(['dropdown guest-language']) }}>
    <button class="guest-guide-badge dropdown-toggle border-0" type="button" id="guest-language-button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('guest.language_label', ['language' => $locales[$current]['name']]) }}">
        <x-guest-language-flag :locale="$current" /> {{ $locales[$current]['code'] }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="guest-language-button">
        @foreach($locales as $locale => $language)
            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['lang' => $locale]) }}" lang="{{ $locale }}" hreflang="{{ $locale }}" @if($locale === $current) aria-current="true" @endif><span class="d-inline-flex align-items-center gap-2"><x-guest-language-flag :locale="$locale" />{{ $language['name'] }} · {{ $language['code'] }}</span>@if($locale === $current)<span aria-hidden="true">✓</span>@endif</a></li>
        @endforeach
    </ul>
</div>
