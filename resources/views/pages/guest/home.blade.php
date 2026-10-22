@extends('layouts.guest')

@section('title', $propertyName)

{{-- Immagine di benvenuto ispirata al simbolo Sogiornia. --}}
@section('content')
    <div class="guest-hero">
        <img src="{{ $roomImageUrl ?: asset('image/guest-welcome.webp') }}" alt="{{ $roomImageUrl ? __('guest.room_photo', ['room' => $roomName]) : '' }}" width="1774" height="887" fetchpriority="high" decoding="async">
    </div>

    <div class="guest-content">
        {{-- Benvenuto e dati reali della struttura e della stanza. --}}
        <header class="guest-intro">
            <p class="guest-kicker">{{ __('guest.welcome', ['property' => $propertyName]) }}</p>
            <h1>{{ __('guest.headline_first') }}<br>{{ __('guest.headline_second') }}</h1>
            @if ($roomName || $propertyCity)
                <p class="guest-room">
                    @if ($roomName)<span>{{ $roomName }}</span>@endif
                    @if ($roomName && $propertyCity)<span aria-hidden="true"> · </span>@endif
                    @if ($propertyCity)<span>{{ $propertyCity }}</span>@endif
                </p>
            @endif
            @if (filled($welcomeMessage))
                <p class="guest-welcome-message preserve-lines">{{ $welcomeMessage }}</p>
            @endif
            @unless ($token)<p class="guest-demo-note">{{ __('guest.demo_note') }}</p>@endunless
            @if($token && app()->getLocale() !== 'it')<p class="guest-original-note" role="note">{{ __('guest.original_content') }}</p>@endif
        </header>

        {{-- Schede delle sole sezioni consentite, in due colonne anche su telefono. --}}
        <section class="guest-sections" aria-label="{{ __('guest.stay_information') }}">
            @forelse ($sections as $slug => $section)
                <a href="{{ $token ? route('guide.show', ['token' => $token, 'section' => $slug, 'lang' => app()->getLocale()]) : route('guest.section', ['section' => $slug, 'lang' => app()->getLocale()]) }}" class="guest-section-card">
                    <x-guest-section-icon :slug="$slug" />
                    <h2>{{ $section['title'] }}</h2>
                    <span class="guest-section-arrow" aria-hidden="true">↗</span>
                </a>
            @empty
                <div class="guest-empty">
                    <h2>{{ __('guest.empty_title') }}</h2>
                    <p>{{ __('guest.empty_body') }}</p>
                </div>
            @endforelse
        </section>
    </div>
@endsection
