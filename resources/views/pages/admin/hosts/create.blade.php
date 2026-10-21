@extends('layouts.app')

@section('title', 'Crea un host')

{{-- Creazione riservata di un host senza possibilità di scegliere un ruolo superiore. --}}
@section('content')
<div class="container py-5 form-container">
    <a class="back-link" href="{{ route('admin-home') }}">← Torna agli host</a>
    <div class="mt-4 mb-4"><span class="eyebrow">UN NUOVO INIZIO</span><h1 class="mt-2">Crea un account host</h1><p class="text-muted-custom">L'host potrà gestire esclusivamente le proprie strutture, stanze e guide.</p></div>
    <form class="sg-card p-4 p-md-5" method="POST" action="{{ route('admin.hosts.store') }}">
        @csrf
        <x-input name="name" label="Nome e cognome" autocomplete="name" maxlength="255" :required="true" />
        <x-input name="email" label="Email di accesso" type="email" autocomplete="off" maxlength="255" :required="true" />
        <x-input name="password" label="Password" type="password" autocomplete="new-password" minlength="12" maxlength="255" :required="true" help="Almeno 12 caratteri, con maiuscole, minuscole e numeri. Comunicala tramite un canale riservato." />
        <x-input name="password_confirmation" label="Conferma password" type="password" autocomplete="new-password" minlength="12" maxlength="255" :required="true" />
        <div class="form-actions"><button class="btn btn-primary" type="submit">Crea host</button><a href="{{ route('admin-home') }}" class="btn btn-outline-secondary">Annulla</a></div>
    </form>
</div>
@endsection
