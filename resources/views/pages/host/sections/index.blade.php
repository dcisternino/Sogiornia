@extends('layouts.app')
@section('title', 'Sezioni · '.$property->name)

{{-- Sezioni e visibilità: disattivazione globale e condivisione con tutte le stanze. --}}
@section('content')
<div class="container py-5 workspace">
    <x-guide-draft-notice :property="$property" />
    <a class="back-link" href="{{ route('host.properties.index') }}">← Le mie strutture</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">{{ $property->name }}</span><h1 class="mt-2">Le sezioni</h1><p class="text-muted-custom mb-0">I contenuti della guida e le stanze che possono vederli.</p></div><div class="d-flex flex-wrap gap-2"><a class="btn btn-outline-secondary" href="{{ route('host.rooms.index', $property) }}">Gestisci stanze</a>@if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.sections.create', $property) }}">+ Aggiungi sezione</a>@endif</div></div>
    <p class="small text-muted-custom mb-4"><strong>Attiva</strong> include la sezione nella bozza nelle stanze abilitate. <strong>Condivisa</strong> la abilita in tutte le stanze di questa struttura, anche quelle nuove. Una sezione disattivata resta nascosta in ogni caso.</p>
    <div class="d-grid gap-3">
        @forelse($sections as $section)
            <x-section-management-card :property="$property" :section="$section" />
        @empty
            <div class="sg-card empty-state"><h2 class="h4">Crea la prima sezione</h2><p class="text-muted-custom mb-0">Puoi partire dai modelli Wi-Fi, check-in, contatti e territorio.</p></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $sections->links() }}</div>
</div>
@endsection
