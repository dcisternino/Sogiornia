@props(['property' => null, 'room' => null])
{{-- Riprende il percorso sulla struttura e sulla stanza appena configurate. --}}
@php
    $setupProperty = $property && !$property->archived_at ? $property : null;
    $setupRoom = $setupProperty && $room && !$room->archived_at ? $room : null;
@endphp
<div {{ $attributes->class(['d-flex justify-content-end mb-3']) }}>
    <a class="small" href="{{ route('host.setup.index', array_filter(['property_id' => $setupProperty?->id, 'room_id' => $setupRoom?->id])) }}">Riprendi configurazione guidata ↗</a>
</div>
