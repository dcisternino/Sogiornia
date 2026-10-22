@extends('layouts.app')
@section('title', 'Sezioni')

{{-- Elenco generale delle sezioni: i contenuti restano associati alla loro struttura. --}}
@section('content')
<div class="container py-5 workspace">
    <p class="small text-muted-custom">Le modifiche restano in bozza. Rivedi e pubblica la guida dalla relativa struttura.</p>
    <div class="page-heading"><div><span class="eyebrow">CONTENUTI DELLE GUIDE</span><h1 class="mt-2">Le tue sezioni</h1><p class="text-muted-custom">Gestisci contenuti e visibilità per ogni struttura.</p></div></div>
    <x-property-directory-filter :properties="$properties" :selected-property="$selectedProperty" index-route="host.section-directory.index" create-route="host.sections.create" create-label="Aggiungi sezione" />
    <p class="small text-muted-custom"><strong>Attiva</strong> include la sezione nella bozza nelle stanze abilitate. <strong>Condivisa</strong> la abilita in tutte le stanze della stessa struttura, anche quelle nuove.</p>
    <div class="d-grid gap-3">
        @forelse($sections as $section)<x-section-management-card :property="$section->property" :section="$section" :show-property="true" />
        @empty<div class="sg-card empty-state"><h2 class="h4">Nessuna sezione da mostrare</h2><p class="text-muted-custom">Scegli un'altra struttura oppure crea una sezione e aggiungi i suoi contenuti.</p>@if($properties->isEmpty())<a class="btn btn-outline-secondary" href="{{ $setupNavigation->url('host.properties.index') }}">Vai alle strutture</a>@endif</div>@endforelse
    </div>
    <div class="mt-4">{{ $sections->links() }}</div>
</div>
@endsection
