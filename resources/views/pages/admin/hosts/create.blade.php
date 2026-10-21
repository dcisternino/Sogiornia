@extends('layouts.app')
@section('title', 'Invita un host')

{{-- L'amministratore indica solo nome ed email: la password viene scelta dall'host. --}}
@section('content')
<div class="container py-5 form-container">
    <a class="back-link" href="{{ route('admin-home') }}">← Torna agli host</a>
    <h1 class="mt-4">Invita un nuovo host</h1>
    <p class="text-muted-custom">L'host riceverà un link personale valido per 60 minuti e sceglierà autonomamente la password. Puoi reinviare l'invito se scade.</p>
    <form class="sg-card p-4 p-md-5" method="POST" action="{{ route('admin.hosts.store') }}">
        @csrf
        <x-input name="name" label="Nome e cognome" maxlength="255" :required="true" />
        <x-input name="email" label="Email dell'host" type="email" maxlength="255" :required="true" />
        <button class="btn btn-primary" type="submit">Crea account e invia invito</button>
    </form>
</div>
@endsection
