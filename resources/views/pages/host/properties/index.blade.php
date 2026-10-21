@extends('layouts.app')

@section('title', 'Le mie strutture')

{{-- Catalogo delle strutture con accesso alla gestione di stanze e guide. --}}
@section('content')
<div class="container py-5 workspace">
    <div class="page-heading">
        <div><span class="eyebrow">STRUTTURE E STANZE</span><h1 class="mt-2">Le tue strutture</h1><p class="text-muted-custom">Scegli una struttura per gestire stanze, sezioni della guida e impostazioni.</p></div>
        @if (auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.properties.create') }}">+ Nuova struttura</a>@endif
    </div>
    <div class="row g-4">
        @forelse ($properties as $property)
            <div class="col-md-6 col-xl-4">
                <article class="sg-card h-100 d-flex flex-column">
                    <span class="eyebrow">{{ $property->city }}</span><h2 class="h3 mt-3">{{ $property->name }}</h2>
                    @if ($property->archived_at)<span class="badge badge-soft mb-3">Archiviata</span>@endif
                    <p class="text-muted-custom">{{ $property->address ?: 'Indirizzo non inserito' }}</p>
                    <div class="d-flex gap-2 mb-4"><span class="badge badge-soft">{{ $property->rooms_count }} {{ $property->rooms_count === 1 ? 'stanza' : 'stanze' }}</span><span class="badge badge-soft">{{ $property->sections_count }} {{ $property->sections_count === 1 ? 'sezione' : 'sezioni' }}</span></div>
                    <a class="btn btn-outline-secondary mt-auto" href="{{ route('host.properties.show', $property) }}">Gestisci stanze e guida <span aria-hidden="true">↗</span></a>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="sg-card empty-state"><span aria-hidden="true">⌂</span><h2 class="h3 mt-3">Diamo una casa alla tua guida.</h2><p class="text-muted-custom">Inizia dalla struttura, aggiungi le informazioni e prepara le stanze per i tuoi ospiti.</p>@if (auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.properties.create') }}">Crea la prima struttura</a>@endif</div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $properties->links() }}</div>
</div>
@endsection
