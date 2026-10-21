@extends('layouts.app')
@section('title', 'Nuovo soggiorno')

{{-- Date espresse nel fuso italiano e riferimento privo di dati personali obbligatori. --}}
@section('content')
<div class="container py-5 form-container">
    <a href="{{ route('host.stays.index', [$property, $room]) }}">← Soggiorni di {{ $room->name }}</a>
    <h1 class="mt-4">Prepara un nuovo soggiorno</h1>
    <p class="text-muted-custom">Ogni soggiorno avrà un link nuovo, utilizzabile dall'arrivo alla partenza alle 10:00. Gli orari sono riferiti all'Italia (Europe/Rome).</p>
    <form class="sg-card mt-4" method="POST" action="{{ route('host.stays.store', [$property, $room]) }}">
        @csrf
        <x-input name="reference" label="Riferimento soggiorno" maxlength="120" :required="true" help="Usa un codice o un riferimento interno; non servono documenti o nomi degli ospiti." />
        <x-input name="arrival_date" label="Data di arrivo" type="date" :required="true" :value="now('Europe/Rome')->format('Y-m-d')" />
        <x-input name="arrival_time" label="Orario di arrivo" type="time" :required="true" value="15:00" />
        <x-input name="departure_date" label="Data di partenza" type="date" :required="true" :value="now('Europe/Rome')->addDay()->format('Y-m-d')" />
        <button class="btn btn-primary" type="submit">Crea soggiorno e link</button>
    </form>
</div>
@endsection
