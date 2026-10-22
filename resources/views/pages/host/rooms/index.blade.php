@extends('layouts.app')
@section('title', 'Stanze · '.$property->name)

{{-- Gestione delle sole stanze della struttura corrente. --}}
@section('content')
<div class="container py-5 workspace">
    <x-guide-draft-notice :property="$property" />
    <a class="back-link" href="{{ route('host.properties.index') }}">← Le mie strutture</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">{{ $property->name }}</span><h1 class="mt-2">Le stanze</h1><p class="text-muted-custom mb-0">Configura foto, informazioni visibili e link per gli ospiti.</p></div><div class="d-flex flex-wrap gap-2"><a class="btn btn-outline-secondary" href="{{ route('host.sections.index', $property) }}">Sezioni della struttura</a>@if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.rooms.create', $property) }}">+ Aggiungi stanza</a>@endif</div></div>
    <div class="row g-4">
        @forelse($rooms as $room)
            <div class="col-md-6 col-xl-4"><x-room-card :property="$property" :room="$room" /></div>
        @empty
            <div class="col-12"><div class="sg-card empty-state"><h2 class="h4">Aggiungi la prima stanza</h2><p class="text-muted-custom mb-0">Scegli un nome, carica una foto e configura le sezioni della sua guida.</p></div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $rooms->links() }}</div>
</div>
@endsection
