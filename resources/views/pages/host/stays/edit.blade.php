@extends('layouts.app')
@section('title', 'Modifica soggiorno')
{{-- Modifica delle date con conservazione del token e dello stato di revoca. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <a class="back-link" href="{{ route('host.stays.index', [$property, $room]) }}">← Soggiorni di {{ $room->name }}</a>
    <h1 class="mt-4">Modifica il soggiorno</h1><p class="text-muted-custom">{{ $property->name }} · {{ $room->name }}. Il link resta lo stesso e seguirà subito le nuove date.</p>
    @if($stay->revoked_at)<div class="alert alert-warning">Questo link è revocato. Modificare le date non riattiva l'accesso: crea un nuovo soggiorno se serve un nuovo link.</div>@endif
    <form class="sg-card editor-form" method="POST" action="{{ route('host.stays.update', [$property, $room, $stay]) }}">
        @csrf @method('PUT')
        <x-stay-fields :stay="$stay" />
        <div class="form-actions"><button class="btn btn-primary" type="submit">Salva le modifiche</button><a class="btn btn-outline-secondary" href="{{ route('host.stays.index', [$property, $room]) }}">Annulla</a></div>
    </form>
</div>
@endsection
