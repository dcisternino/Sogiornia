@extends('layouts.app')

@section('title', $property->exists ? 'Modifica struttura' : 'Nuova struttura')

{{-- Modulo condiviso per creazione e modifica della struttura. --}}
@section('content')
<div class="container py-5 form-container">
    <a class="back-link" href="{{ $property->exists ? route('host.properties.show', $property) : route('host-home') }}">← Torna alle strutture</a>
    <span class="eyebrow d-block mt-4">IL LUOGO DEL TUO BENVENUTO</span>
    <h1 class="mt-2 mb-4">{{ $property->exists ? 'Modifica struttura' : 'Crea la tua struttura' }}</h1>
    <form class="sg-card p-4 p-md-5" method="POST" action="{{ $property->exists ? route('host.properties.update', $property) : route('host.properties.store') }}">
        @csrf
        @if ($property->exists) @method('PUT') @endif
        <x-input name="name" label="Nome della struttura" :value="$property->name" maxlength="255" :required="true" placeholder="Es. Casa Marina" />
        <x-input name="city" label="Città" :value="$property->city" maxlength="255" :required="true" autocomplete="address-level2" />
        <x-input name="address" label="Indirizzo" :value="$property->address" maxlength="255" autocomplete="street-address" />
        <x-textarea name="welcome_message" label="Messaggio di benvenuto" :value="$property->welcome_message" maxlength="3000" help="Comparirà nella home della guida di ogni stanza." />
        <div class="form-actions"><button class="btn btn-primary" type="submit">{{ $property->exists ? 'Salva modifiche' : 'Crea struttura' }}</button><a class="btn btn-outline-secondary" href="{{ $property->exists ? route('host.properties.show', $property) : route('host-home') }}">Annulla</a></div>
    </form>
</div>
@endsection
