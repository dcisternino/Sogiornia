@extends('layouts.app')
@section('title', 'Nuovo soggiorno')

{{-- Date espresse nel fuso italiano e riferimento privo di dati personali obbligatori. --}}
@section('content')
<div class="container py-5 form-container">
    <a href="{{ $setupNavigation->back('host.stays.index', [$property, $room]) }}">@if($setupNavigation->active())← Torna alla configurazione guidata @else ← Soggiorni di {{ $room->name }} @endif</a>
    <h1 class="mt-4">Prepara un nuovo soggiorno</h1>
    <p class="text-muted-custom">Ogni soggiorno avrà un link nuovo, utilizzabile dall'arrivo alla partenza alle 10:00. Gli orari sono riferiti all'Italia (Europe/Rome).</p>
    <form class="sg-card mt-4" method="POST" action="{{ $setupNavigation->url('host.stays.store', [$property, $room]) }}">
        @csrf <x-onboarding-context />
        <x-stay-fields />
        <button class="btn btn-primary" type="submit">Crea soggiorno e link</button>
    </form>
</div>
@endsection
