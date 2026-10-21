@extends('layouts.app')

@section('title', $room->exists ? 'Modifica stanza' : 'Nuova stanza')

{{-- Dati della stanza e selezione esplicita dei contenuti visibili. --}}
@section('content')
<div class="container py-5 form-container">
    <a class="back-link" href="{{ route('host.properties.show', $property) }}">← {{ $property->name }}</a>
    <span class="eyebrow d-block mt-4">UNA GUIDA SU MISURA</span>
    <h1 class="mt-2 mb-4">{{ $room->exists ? 'Modifica stanza' : 'Crea una stanza' }}</h1>
    <form class="sg-card p-4 p-md-5" method="POST" action="{{ $room->exists ? route('host.rooms.update', [$property, $room]) : route('host.rooms.store', $property) }}">
        @csrf
        @if ($room->exists) @method('PUT') @endif
        <x-input name="name" label="Nome della stanza" :value="$room->name" maxlength="255" :required="true" placeholder="Es. Stanza Levante" />
        <x-textarea name="description" label="Nota interna sulla stanza" :value="$room->description" maxlength="3000" :rows="3" help="Questa nota è visibile solo nella tua area di gestione." />

        {{-- Nessuna sezione viene pubblicata implicitamente nelle nuove stanze. --}}
        <fieldset class="mb-4">
            <legend class="h5">Quali sezioni può vedere questa stanza?</legend>
            <p class="small text-muted-custom">Seleziona solo i contenuti da pubblicare nella guida. Senza selezioni, la guida resterà vuota.</p>
            <input type="hidden" name="sections_present" value="1">
            @forelse ($sections as $section)
                <div class="form-check section-choice">
                    <input class="form-check-input" type="checkbox" name="sections[]" id="section-{{ $section->id }}" value="{{ $section->id }}" @checked(in_array($section->id, (array) (old('sections_present') ? old('sections', []) : $selectedSections)))>
                    <label class="form-check-label" for="section-{{ $section->id }}">{{ $section->title }}</label>
                </div>
            @empty
                <p class="onboarding-note">Non hai ancora creato sezioni. Puoi salvare la stanza e configurare i contenuti in seguito.</p>
            @endforelse
        </fieldset>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Salva stanza e visibilità</button><a class="btn btn-outline-secondary" href="{{ route('host.properties.show', $property) }}">Annulla</a></div>
    </form>
</div>
@endsection
