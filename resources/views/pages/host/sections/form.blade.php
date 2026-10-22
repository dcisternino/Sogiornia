@extends('layouts.app')
@section('title', $section->exists ? 'Modifica sezione' : 'Nuova sezione')

{{-- Dati del contenitore; i singoli contenuti si gestiscono nella pagina dedicata. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <x-guide-draft-notice :property="$property" />
    <a href="{{ route('host.sections.index', $property) }}">← {{ $property->name }}</a>
    <h1 class="mt-4">{{ $section->exists ? 'Modifica sezione' : 'Crea una sezione' }}</h1>
    <p class="text-muted-custom">Una sezione è un contenitore: dopo il salvataggio puoi aggiungere testi, contatti, collegamenti e luoghi.</p>
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
        <x-input name="title" label="Titolo della sezione in italiano" :value="$section->title" maxlength="255" :required="true" />
        <x-textarea name="content" label="Introduzione della sezione (facoltativa)" :value="$section->content" maxlength="20000" :rows="4" help="Testo semplice, anche su più righe. HTML non supportato." />

        <x-guest-content-translations :record="$section" type="section" />
        <p class="small text-muted-custom">Le modifiche valgono per tutte le stanze in cui questa sezione è abilitata.</p>
        {{-- Identificativo facoltativo, separato dai contenuti destinati agli ospiti. --}}
        <details class="advanced-settings my-4" @if($errors->has('slug')) open @endif><summary>Impostazioni avanzate del collegamento</summary><div class="mt-3"><x-input name="slug" label="Identificativo nel link" :value="$section->slug" maxlength="100" pattern="[a-z0-9]+(-[a-z0-9]+)*" help="Puoi lasciarlo vuoto: lo generiamo dal titolo. Modificarlo cambia il link di questa sezione." /></div></details>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Salva bozza</button><a class="btn btn-outline-secondary" href="{{ route('host.sections.index', $property) }}">Annulla</a></div>
    </form>
</div>
@endsection
