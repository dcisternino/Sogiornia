@extends('layouts.app')

@section('title', 'Le mie strutture')

{{-- Dashboard limitata alle strutture appartenenti all'host autenticato. --}}
@section('content')
<div class="container py-5">
    <div class="page-heading">
        <div><span class="eyebrow">IL TUO SPAZIO HOST</span><h1 class="mt-2">L'ospitalità comincia qui.</h1><p class="text-muted-custom">Ciao {{ auth()->user()->name }}, prenditi cura delle tue strutture e delle loro guide.</p></div>
        <a class="btn btn-primary" href="{{ route('host.properties.create') }}">+ Nuova struttura</a>
    </div>
    <div class="row g-4">
        @forelse ($properties as $property)
            <div class="col-md-6 col-xl-4">
                <article class="sg-card h-100 d-flex flex-column">
                    <span class="eyebrow">{{ $property->city }}</span><h2 class="h3 mt-3">{{ $property->name }}</h2>
                    <p class="text-muted-custom">{{ $property->address ?: 'Indirizzo non inserito' }}</p>
                    <div class="d-flex gap-2 mb-4"><span class="badge badge-soft">{{ $property->rooms_count }} stanze</span><span class="badge badge-soft">{{ $property->sections_count }} sezioni</span></div>
                    <a class="btn btn-outline-secondary mt-auto" href="{{ route('host.properties.show', $property) }}">Gestisci struttura <span aria-hidden="true">↗</span></a>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="sg-card empty-state"><span aria-hidden="true">⌂</span><h2 class="h3 mt-3">Diamo una casa alla tua guida.</h2><p class="text-muted-custom">Inizia dalla struttura, aggiungi le informazioni e prepara le stanze per i tuoi ospiti.</p><a class="btn btn-primary" href="{{ route('host.properties.create') }}">Crea la prima struttura</a></div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $properties->links() }}</div>
</div>
@endsection
