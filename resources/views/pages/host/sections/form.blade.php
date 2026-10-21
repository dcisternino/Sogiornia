@extends('layouts.app')
@section('title', $section->exists ? 'Modifica sezione' : 'Nuova sezione')

{{-- Sezioni personalizzabili con testo, immagine privata, collegamento e telefono. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <a href="{{ route('host.properties.show', $property) }}">← {{ $property->name }}</a>
    <h1 class="mt-4">{{ $section->exists ? 'Modifica sezione' : 'Crea una sezione' }}</h1>
    <p class="text-muted-custom">Una sezione raccoglie un'informazione utile: per esempio Wi-Fi, check-in o consigli sul territorio.</p>
    <x-setup-steps :property="$property" current="sections" class="my-4" />
    @if (! $section->exists)
        <div class="sg-card my-4">
            <h2 class="h5">Parti da un modello</h2><p class="small text-muted-custom">Scegli prima un modello, poi personalizza il testo e salva. I segnaposto vanno sostituiti con le tue informazioni.</p>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($templates as $key => $template)
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('host.sections.create', ['property' => $property, 'template' => $key]) }}">{{ $template['title'] }}</a>
                @endforeach
            </div>
        </div>
    @endif
    <form class="sg-card editor-form mt-4" method="POST" enctype="multipart/form-data" action="{{ $section->exists ? route('host.sections.update', [$property, $section]) : route('host.sections.store', $property) }}">
        @csrf
        @if ($section->exists) @method('PUT') @endif
        <x-input name="title" label="Titolo della sezione" :value="$section->title" maxlength="255" :required="true" />
        <x-textarea name="content" label="Informazioni per gli ospiti" :value="$section->content" maxlength="20000" :rows="9" :required="true" help="Testo semplice, anche su più righe. HTML non supportato." />

        {{-- Immagini non pubbliche, disponibili solo attraverso una guida valida. --}}
        <fieldset class="my-4">
            <legend class="h5">Un'immagine per la sezione</legend>
            @if ($section->exists && $section->image_path)
                <img class="section-image mb-3" src="{{ route('host.sections.image', [$property, $section]) }}" alt="{{ $section->image_alt ?: $section->title }}">
                <div class="form-check mb-3"><input class="form-check-input" id="remove_image" name="remove_image" value="1" type="checkbox" @checked(old('remove_image'))><label class="form-check-label" for="remove_image">Rimuovi l'immagine attuale</label></div>
            @endif
            <label for="image" class="form-label">Carica immagine</label>
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="form-control mb-2 @error('image') is-invalid @enderror" @error('image') aria-invalid="true" aria-describedby="image-error" @enderror>
            @error('image')<div class="invalid-feedback" id="image-error">{{ $message }}</div>@enderror
            <p class="small text-muted-custom">JPEG, PNG o WebP. Massimo 5 MB e 4000 × 4000 pixel. Le immagini non selezionate rimangono invariate.</p>
            <x-input name="image_alt" label="Descrizione dell'immagine" :value="$section->image_alt" maxlength="255" help="Descrivi ciò che mostra per chi utilizza un lettore di schermo." />
        </fieldset>

        {{-- Destinazioni ammesse: pagine web HTTP/HTTPS e numeri telefonici. --}}
        <fieldset class="my-4">
            <legend class="h5">Link e contatti (facoltativi)</legend>
            <x-input name="link_url" label="Indirizzo web" type="url" :value="$section->link_url" maxlength="2048" placeholder="https://..." />
            <x-input name="link_label" label="Testo del collegamento" :value="$section->link_label" maxlength="255" placeholder="Es. Apri la mappa" />
            <x-input name="phone" label="Numero telefonico" type="tel" :value="$section->phone" maxlength="40" placeholder="+39 ..." />
        </fieldset>
        <p class="small text-muted-custom">Le modifiche valgono per tutte le stanze in cui questa sezione è abilitata.</p>
        {{-- Identificativo facoltativo, separato dai contenuti destinati agli ospiti. --}}
        <details class="advanced-settings my-4" @if($errors->has('slug')) open @endif><summary>Impostazioni avanzate del collegamento</summary><div class="mt-3"><x-input name="slug" label="Identificativo nel link" :value="$section->slug" maxlength="100" pattern="[a-z0-9]+(-[a-z0-9]+)*" help="Puoi lasciarlo vuoto: lo generiamo dal titolo. Modificarlo cambia il link di questa sezione." /></div></details>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Salva sezione</button><a class="btn btn-outline-secondary" href="{{ route('host.properties.show', $property) }}">Annulla</a></div>
    </form>
</div>
@endsection
