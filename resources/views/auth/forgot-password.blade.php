@extends('layouts.app')
@section('title', 'Recupera password')

{{-- Richiesta del link senza indicazioni sull'esistenza dell'account. --}}
@section('content')
<div class="container py-5 form-container">
    <span class="eyebrow">TORNA NEL TUO SPAZIO</span>
    <h1 class="mt-3">Hai dimenticato la password?</h1>
    <p class="text-muted-custom">Inserisci l'email del tuo account. Il link per scegliere la password scade dopo 60 minuti.</p>
    <form class="sg-card mt-4" method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-input name="email" label="Email" type="email" maxlength="255" autocomplete="email" :required="true" />
        <button class="btn btn-primary" type="submit">Richiedi il link</button>
        <a class="btn btn-link" href="{{ route('login') }}">Torna al login</a>
    </form>
</div>
@endsection
