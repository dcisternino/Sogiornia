@extends('layouts.app')

@section('title', $section->exists ? 'Modifica sezione' : 'Nuova sezione')

{{-- Contenuti della guida con slug stabile e testo semplice escapato. --}}
@section('content')
<div class="container py-5 form-container">
    <a class="back-link" href="{{ route('host.properties.show', $property) }}">← {{ $property->name }}</a>
    <span class="eyebrow d-block mt-4">LE RISPOSTE CHE FANNO ACCOGLIENZA</span>
    <h1 class="mt-2 mb-4">{{ $section->exists ? 'Modifica sezione' : 'Crea una sezione' }}</h1>
    <form class="sg-card p-4 p-md-5" method="POST" action="{{ $section->exists ? route('host.sections.update', [$property, $section]) : route('host.sections.store', $property) }}">
        @csrf
        @if ($section->exists) @method('PUT') @endif
        <x-input name="title" label="Titolo della sezione" :value="$section->title" maxlength="255" :required="true" placeholder="Es. Wi-Fi, Check-in, Dove mangiare" />
        <x-input name="slug" label="Identificativo nel link" :value="$section->slug" maxlength="100" pattern="[a-z0-9]+(-[a-z0-9]+)*" help="Lettere minuscole, numeri e trattini. Se vuoto viene generato dal titolo. Modificarlo cambia il link del dettaglio." />
        <x-textarea name="content" label="Informazioni per gli ospiti" :value="$section->content" maxlength="20000" :rows="10" :required="true" help="Scrivi testo semplice, anche su più righe. Non sono supportati HTML o allegati." />
        <p class="small text-muted-custom">Le modifiche saranno visibili in tutte le stanze che hanno questa sezione abilitata.</p>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Salva sezione</button><a class="btn btn-outline-secondary" href="{{ route('host.properties.show', $property) }}">Annulla</a></div>
    </form>
</div>
@endsection
