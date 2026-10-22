@extends('layouts.app')
@section('title', 'Stanze · '.$property->name)

{{-- Gestione delle sole stanze della struttura corrente. --}}
@section('content')
<div class="container py-5 workspace">
    <x-setup-shortcut :property="$property" />
    <a class="back-link" href="{{ route('host.properties.index') }}">← Le mie strutture</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">{{ $property->name }}</span><h1 class="mt-2">Le stanze</h1><p class="text-muted-custom mb-0">Configura foto, informazioni visibili e link per gli ospiti.</p></div><div class="d-flex flex-wrap gap-2"><a class="btn btn-outline-secondary" href="{{ route('host.sections.index', $property) }}">Sezioni della struttura</a>@if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.rooms.create', $property) }}">+ Aggiungi stanza</a>@endif</div></div>
    <div class="row g-4">
        @forelse($rooms as $room)
            <div class="col-md-6 col-xl-4"><article class="sg-card room-card h-100">
                <img class="room-card-photo" src="{{ $room->photo ? route('host.rooms.image', [$property, $room]) : asset('image/guest-welcome.webp') }}" alt="{{ $room->photo ? 'Foto di '.$room->name : '' }}" loading="lazy">
                <div class="room-card-content">
                    <span class="badge badge-soft mb-3">{{ $room->archived_at ? 'Archiviata' : ($property->archived_at ? 'Struttura archiviata' : ($room->guide_enabled && $property->guide_enabled ? 'Guida abilitata' : 'Guida sospesa')) }}</span>
                    <h2 class="h4">{{ $room->name }}</h2><p class="text-muted-custom small">{{ Str::limit($room->description ?: 'Foto e sezioni personalizzabili per questa stanza.', 130) }}</p>
                    <div class="d-grid gap-2">@if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.rooms.edit', [$property, $room]) }}">Configura stanza<span class="visually-hidden"> {{ $room->name }}</span></a>@endif<a class="btn btn-outline-secondary" href="{{ route('host.stays.index', [$property, $room]) }}">Soggiorni e link ospite</a><a class="btn btn-outline-secondary" href="{{ route('host.preview.index', [$property, $room]) }}">Anteprima guida<span class="visually-hidden"> di {{ $room->name }}</span></a></div>
                    @if(auth()->user()->canWrite())<details class="advanced-settings mt-3"><summary>Stato e altre opzioni</summary><x-lifecycle :record="$room" :update-url="route('host.rooms.lifecycle', [$property, $room])" :destroy-url="route('host.rooms.destroy', [$property, $room])" /></details>@endif
                </div>
            </article></div>
        @empty
            <div class="col-12"><div class="sg-card empty-state"><h2 class="h4">Aggiungi la prima stanza</h2><p class="text-muted-custom mb-0">Scegli un nome, carica una foto e configura le sezioni della sua guida.</p></div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $rooms->links() }}</div>
</div>
@endsection
