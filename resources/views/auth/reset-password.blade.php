@extends('layouts.app')
@section('title', 'Scegli la tua password')
@section('referrer', 'no-referrer')

{{-- Scelta autonoma della password per invito o recupero. --}}
@section('content')
<div class="container py-5 form-container">
    <span class="eyebrow">IL TUO ACCOUNT, LA TUA PASSWORD</span>
    <h1 class="mt-3">Scegli la tua password</h1>
    <form class="sg-card mt-4" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-input name="email" label="Email dell'account" type="email" :value="is_string($email) ? $email : ''" maxlength="255" autocomplete="username" :required="true" />
        <x-input name="password" label="Nuova password" type="password" minlength="12" maxlength="255" autocomplete="new-password" :required="true" help="Almeno 12 caratteri con maiuscole, minuscole e numeri." />
        <x-input name="password_confirmation" label="Conferma password" type="password" maxlength="255" autocomplete="new-password" :required="true" />
        <button class="btn btn-primary" type="submit">Salva password</button>
        <a class="btn btn-link" href="{{ route('password.request') }}">Richiedi un nuovo link</a>
    </form>
</div>
@endsection
