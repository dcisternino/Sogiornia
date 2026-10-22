@extends('layouts.app')

@section('title', $property->exists ? 'Modifica struttura' : 'Nuova struttura')

{{-- Modulo condiviso per creazione e modifica della struttura. --}}
@section('content')
<div class="container py-5 form-container workspace">
    @if($property->exists)<x-guide-draft-notice :property="$property" />@endif
    <a class="back-link" href="{{ $setupNavigation->back('host.properties.index') }}">@if($setupNavigation->active())← Torna alla configurazione guidata @else ← Torna alle strutture @endif</a>
    <span class="eyebrow d-block mt-4">IL LUOGO DEL TUO BENVENUTO</span>
    <h1 class="mt-2 mb-4">{{ $property->exists ? 'Modifica struttura' : 'Crea la tua struttura' }}</h1>
    <p class="text-muted-custom">Parti dalle informazioni principali. Potrai poi aggiungere sezioni informative e stanze.</p>
    <form class="sg-card editor-form" method="POST" enctype="multipart/form-data" action="{{ $property->exists ? $setupNavigation->url('host.properties.update', $property) : $setupNavigation->url('host.properties.store') }}">
        @csrf <x-onboarding-context />
        @if ($property->exists) @method('PUT') @endif
        <h2 class="h5 mb-4">Come si chiama la tua struttura?</h2>
        <x-input name="name" label="Nome della struttura" :value="$property->name" maxlength="255" :required="true" placeholder="Es. Casa Marina" />
        <x-textarea name="description" label="Breve descrizione" :value="$property->exists ? $property->profile?->description : ''" maxlength="500" :rows="3" help="Una breve presentazione mostrata nell'elenco delle strutture." />
        {{-- Copertina della struttura distinta dalla foto delle singole stanze. --}}
        <fieldset class="editor-section">
            <legend>Foto della struttura</legend>
            <img class="room-photo-preview mb-3" id="property-photo-preview" src="{{ $property->exists && $property->profile?->image_path ? $setupNavigation->url('host.properties.image', $property) : asset('image/guest-welcome.webp') }}" alt="Anteprima della foto della struttura">
            <label class="form-label" for="image">Carica o sostituisci la foto</label>
            <input class="form-control @error('image') is-invalid @enderror" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="property-photo-preview" aria-describedby="image-feedback">
            <div id="image-feedback" class="{{ $errors->has('image') ? 'invalid-feedback' : 'form-text' }}">@error('image'){{ $message }}@else JPEG, PNG o WebP. Massimo 5 MB e 4000 × 4000 pixel.@enderror</div>
            @if($property->exists && $property->profile?->image_path)<div class="form-check mt-3"><input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1" @checked(old('remove_image'))><label class="form-check-label" for="remove_image">Rimuovi la foto attuale</label></div>@endif
            @error('remove_image')<p class="small text-danger">{{ $message }}</p>@enderror
        </fieldset>
        <x-input name="city" label="Città" :value="$property->city" maxlength="255" :required="true" autocomplete="address-level2" />
        <x-input name="address" label="Indirizzo" :value="$property->address" maxlength="255" autocomplete="street-address" />
        <x-textarea name="welcome_message" label="Messaggio di benvenuto in italiano" :value="$property->welcome_message" maxlength="3000" help="Comparirà nella home della guida di ogni stanza e sarà usato quando manca una traduzione." />
        <x-guest-content-translations :record="$property" type="property" />
        <div class="form-actions"><button class="btn btn-primary" type="submit">{{ $property->exists ? 'Salva bozza' : 'Crea struttura' }}</button><a class="btn btn-outline-secondary" href="{{ $setupNavigation->back('host.properties.index') }}">@if($setupNavigation->active())← Torna alla configurazione guidata @else Annulla @endif</a></div>
    </form>
    {{-- Azioni occasionali della struttura separate dai dati principali. --}}
    @if($property->exists && auth()->user()->canWrite())<details class="sg-card advanced-settings mt-4"><summary>Stato della struttura e opzioni avanzate</summary><p class="small text-muted-custom mt-3">Archiviazione e sospensione interrompono le guide senza cancellarne i contenuti.</p><x-lifecycle :record="$property" :update-url="$setupNavigation->url('host.properties.lifecycle', $property)" :destroy-url="$setupNavigation->url('host.properties.destroy', $property)" /></details>@endif
</div>
@endsection
