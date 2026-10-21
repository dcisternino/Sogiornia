@extends('layouts.guest')

@section('title', $section['title'].' · '.$propertyName)

{{-- Dettaglio coerente con la scheda iniziale della guida. --}}
@section('content')
    <div class="guest-content guest-detail">
        {{-- Ritorno alla guida con lo stesso link riservato del soggiorno. --}}
        <a class="guest-back-link" href="{{ $token ? route('guide.index', ['token' => $token]) : route('guest-home') }}">← Torna alla guida</a>

        <article>
            {{-- Titolo e contesto della struttura. --}}
            <p class="guest-kicker">{{ $propertyName }}</p>
            <h1>{{ $section['title'] }}</h1>

            {{-- Testo escapato e immagine protetta dalla validità del soggiorno. --}}
            <div class="preserve-lines section-content">{{ $section['content'] }}</div>
            @if ($section['image_url'] ?? null)
                <img class="section-image mt-4" src="{{ $section['image_url'] }}" alt="{{ $section['image_alt'] ?: $section['title'] }}" loading="lazy">
            @endif

            {{-- Azioni costruite esclusivamente con collegamenti e numeri validati. --}}
            <div class="guest-detail-actions">
                @if ($section['link_url'] ?? null)
                    <a class="btn btn-primary" href="{{ $section['link_url'] }}" target="_blank" rel="noopener noreferrer">{{ $section['link_label'] ?: 'Apri il collegamento' }} ↗</a>
                @endif
                @if ($section['phone'] ?? null)
                    <a class="btn btn-outline-secondary" href="tel:{{ preg_replace('/[^+0-9]/', '', $section['phone']) }}">Chiama {{ $section['phone'] }}</a>
                @endif
            </div>
        </article>
    </div>
@endsection
