@extends('layouts.app')
@section('title', 'Stanze')

{{-- Elenco generale delle stanze con le stesse azioni della pagina della struttura. --}}
@section('content')
<div class="container py-5 workspace">
    <div class="page-heading"><div><span class="eyebrow">LE TUE STRUTTURE</span><h1 class="mt-2">Le tue stanze</h1><p class="text-muted-custom">Trova una stanza e configura foto, sezioni e soggiorni.</p></div></div>
    <x-property-directory-filter :properties="$properties" :selected-property="$selectedProperty" index-route="host.room-directory.index" create-route="host.rooms.create" create-label="Aggiungi stanza" />
    <div class="row g-4">
        @forelse($rooms as $room)<div class="col-md-6 col-xl-4"><x-room-card :property="$room->property" :room="$room" :show-property="true" /></div>
        @empty<div class="col-12"><div class="sg-card empty-state"><h2 class="h4">Nessuna stanza da mostrare</h2><p class="text-muted-custom">Scegli un'altra struttura oppure aggiungi la prima stanza.</p>@if($properties->isEmpty())<a class="btn btn-outline-secondary" href="{{ route('host.properties.index') }}">Vai alle strutture</a>@endif</div></div>@endforelse
    </div>
    <div class="mt-4">{{ $rooms->links() }}</div>
</div>
@endsection
