@extends('layouts.app')
@section('title', 'Modifica soggiorno')
{{-- Modifica delle date con conservazione del token e dello stato di revoca. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <a class="back-link" href="{{ $setupNavigation->back('host.stays.index', [$property, $room]) }}">@if($setupNavigation->active())← Torna alla configurazione guidata @else ← Soggiorni di {{ $room->name }} @endif</a>
    <h1 class="mt-4">Modifica il soggiorno</h1><p class="text-muted-custom">{{ $property->name }} · {{ $room->name }}. Il link resta lo stesso e seguirà subito le nuove date.</p>
    @if($stay->revoked_at)<div class="alert alert-warning">Questo link è revocato. Modificare le date non riattiva l'accesso: crea un nuovo soggiorno se serve un nuovo link.</div>@endif
    <form class="sg-card editor-form" method="POST" action="{{ $setupNavigation->url('host.stays.update', [$property, $room, $stay]) }}">
        @csrf <x-onboarding-context /> @method('PUT')
        <x-stay-fields :stay="$stay" />
        <div class="form-actions"><button class="btn btn-primary" type="submit">Salva le modifiche</button><a class="btn btn-outline-secondary" href="{{ $setupNavigation->back('host.stays.index', [$property, $room]) }}">@if($setupNavigation->active())← Torna alla configurazione guidata @else Annulla @endif</a></div>
    </form>
</div>
@endsection
