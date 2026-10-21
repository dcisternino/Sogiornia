@extends('layouts.app')
@section('title', 'Nuovo soggiorno')

{{-- Scelta della stanza e creazione del relativo link riservato. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <a class="back-link" href="{{ route('host-home') }}">← Dashboard</a>
    <h1 class="mt-4">Prepara un nuovo soggiorno</h1>
    <p class="text-muted-custom">Scegli dove accogliere i tuoi ospiti. Creeremo un link distinto per questo soggiorno.</p>
    @if($properties->isEmpty())
        <div class="sg-card empty-state"><h2 class="h4">Prima serve una stanza</h2><p>Crea una struttura e aggiungi una stanza, oppure ripristina quelle archiviate.</p><a class="btn btn-primary" href="{{ route('host.properties.index') }}">Vai alle strutture</a></div>
    @else
        <form class="sg-card mt-4" method="POST" action="{{ route('host.bookings.store') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label" for="room_id">Struttura e stanza *</label>
                <select id="room_id" name="room_id" class="form-select @error('room_id') is-invalid @enderror" required @error('room_id') aria-invalid="true" aria-describedby="room_id-feedback" @enderror>
                    <option value="">Scegli la stanza</option>
                    @foreach($properties as $property)
                        <optgroup label="{{ $property->name }}">
                            @foreach($property->rooms as $room)<option value="{{ $room->id }}" @selected((string) old('room_id') === (string) $room->id)>{{ $room->name }}{{ !$property->guide_enabled || !$room->guide_enabled ? ' — guida sospesa' : '' }}</option>@endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('room_id')<div class="invalid-feedback" id="room_id-feedback">{{ $message }}</div>@enderror
            </div>
            <x-stay-fields />
            <div class="form-actions"><button class="btn btn-primary" type="submit">Crea soggiorno e link</button><a href="{{ route('host-home') }}" class="btn btn-outline-secondary">Annulla</a></div>
        </form>
    @endif
</div>
@endsection
