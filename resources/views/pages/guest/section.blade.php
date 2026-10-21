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
    </article>
</div>
@endsection
