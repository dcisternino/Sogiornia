@extends('layouts.guest')

@section('title', $propertyName)

{{-- Immagine di benvenuto ispirata al simbolo Sogiornia. --}}
@section('content')
    <div class="guest-hero">
        <img src="{{ asset('image/guest-welcome.webp') }}" alt="" width="1774" height="887" fetchpriority="high" decoding="async">
    </div>

    <div class="guest-content">
        {{-- Benvenuto e dati reali della struttura e della stanza. --}}
        <header class="guest-intro">
            <p class="guest-kicker">Benvenuto a {{ $propertyName }}</p>
            <h1>Il tuo soggiorno,<br>con un tocco in più.</h1>
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
            @unless ($token)<p class="guest-demo-note">Guida dimostrativa · dati di esempio</p>@endunless
        </header>

        {{-- Schede delle sole sezioni consentite, in due colonne anche su telefono. --}}
        <section class="guest-sections" aria-label="Informazioni per il soggiorno">
            @forelse ($sections as $slug => $section)
                <a href="{{ $token ? route('guide.show', ['token' => $token, 'section' => $slug]) : route('guest.section', ['section' => $slug]) }}" class="guest-section-card">
                    <x-guest-section-icon :slug="$slug" />
                    <h2>{{ $section['title'] }}</h2>
                    <span class="guest-section-arrow" aria-hidden="true">↗</span>
                </a>
            @empty
                <div class="guest-empty">
                    <h2>La tua guida si sta preparando</h2>
                    <p>L'host non ha ancora abilitato informazioni per questa stanza. Contattalo per assistenza.</p>
                </div>
            @endforelse
        </section>
    </div>
@endsection
