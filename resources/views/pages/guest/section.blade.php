@extends('layouts.guest')

@section('title', $section['title'].' · '.$propertyName)

{{-- Dettaglio di una sezione appartenente alla whitelist della guida corrente. --}}
@section('content')
<div class="container py-5 guide-container">
    {{-- Collegamento di ritorno che conserva il contesto della stanza. --}}
    <a class="back-link" href="{{ $token ? route('guide.index', ['token' => $token]) : route('guest-home') }}">← Torna alla guida</a>

    {{-- Titolo e contesto della struttura, senza dati di gestione. --}}
    <article class="sg-card mt-4 p-4 p-md-5">
        <span class="eyebrow">{{ $propertyName }}</span>
        <h1 class="mt-3 mb-4">{{ $section['title'] }}</h1>

        {{-- Testo escapato con conservazione degli a capo inseriti dall'host. --}}
        <div class="preserve-lines section-content">{{ $section['content'] }}</div>

        {{-- Immagine protetta dalla validità del soggiorno e dalla visibilità della sezione. --}}
        @if ($section['image_url'] ?? null)
            <img class="section-image mt-4" src="{{ $section['image_url'] }}" alt="{{ $section['image_alt'] ?: $section['title'] }}" loading="lazy">
        @endif

        {{-- Azioni cliccabili costruite soltanto da URL e numeri validati. --}}
        <div class="d-flex flex-wrap gap-3 mt-4">
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
