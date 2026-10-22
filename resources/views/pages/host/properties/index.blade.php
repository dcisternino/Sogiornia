@extends('layouts.app')
@section('title', 'Le mie strutture')

{{-- Elenco delle strutture e accesso distinto a dati, stanze e sezioni. --}}
@section('content')
<div class="container py-5 workspace">
    <x-setup-shortcut />
    <div class="page-heading"><div><span class="eyebrow">IL TUO SPAZIO HOST</span><h1 class="mt-2">Le tue strutture</h1></div>@if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.properties.create') }}">+ Aggiungi struttura</a>@endif</div>
    <x-property-summary :active-properties="$activeProperties" :rooms="$roomCount" :sections="$sectionCount" class="mb-4" />
    <div class="d-grid gap-4">
        @forelse($properties as $property)
            <article class="sg-card property-row">
                <img class="property-row-photo" src="{{ $property->profile?->image_path ? route('host.properties.image', $property) : asset('image/guest-welcome.webp') }}" alt="{{ $property->profile?->image_path ? 'Foto di '.$property->name : '' }}" loading="lazy">
                <div class="property-row-info">
                    <span class="eyebrow">{{ $property->city }}</span><h2 class="h3 mt-2">{{ $property->name }}</h2>
                    <p class="text-muted-custom mb-3">{{ $property->profile?->description ?: 'Aggiungi una breve descrizione dalle impostazioni della struttura.' }}</p>
                    <div class="d-flex flex-wrap gap-2"><span class="badge badge-soft">{{ $property->archived_at ? 'Archiviata' : 'Attiva' }}</span><span class="small text-muted-custom">{{ $property->rooms_count }} {{ $property->rooms_count === 1 ? 'stanza' : 'stanze' }} · {{ $property->sections_count }} {{ $property->sections_count === 1 ? 'sezione' : 'sezioni' }}</span></div>
                </div>
                <div class="property-row-actions">
                    @if(auth()->user()->canWrite())<a class="btn btn-outline-secondary" href="{{ route('host.properties.edit', $property) }}">Modifica<span class="visually-hidden"> {{ $property->name }}</span></a>@endif
                    <a class="btn btn-primary" href="{{ route('host.rooms.index', $property) }}">Gestisci stanze<span class="visually-hidden"> di {{ $property->name }}</span></a>
                    <a class="btn btn-outline-secondary" href="{{ route('host.sections.index', $property) }}">Sezioni<span class="visually-hidden"> di {{ $property->name }}</span></a>
                </div>
            </article>
        @empty
            <div class="sg-card empty-state"><h2 class="h4">Non hai ancora strutture</h2><p class="text-muted-custom mb-0">Aggiungi la prima struttura per iniziare a creare stanze e guide.</p></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $properties->links() }}</div>
</div>
@endsection
