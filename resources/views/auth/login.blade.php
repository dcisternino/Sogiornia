@extends('layouts.app')

@section('title', 'Accedi')

{{-- Accesso unico con destinazione determinata dal ruolo dell'account. --}}
@section('content')
<div class="container py-5">
    <div class="row align-items-center g-5 py-lg-4">
        <div class="col-lg-6 pe-lg-5">
            <span class="eyebrow">BENTORNATO A CASA</span>
            <h1 class="display-5 mt-3">La tua accoglienza.<br>Un unico posto.</h1>
            <p class="lead text-muted-custom mt-4">Entra in Sogiornia e prepara un soggiorno più semplice, stanza dopo stanza.</p>
            <div class="login-note mt-4"><span aria-hidden="true">✦</span> Le informazioni giuste, per l'ospite giusto.</div>
        </div>
        <div class="col-lg-5 offset-lg-1">
            <div class="sg-card p-4 p-md-5">
                <h2 class="h3">Accedi al tuo spazio</h2>
                <p class="text-muted-custom mb-4">Usa le credenziali del tuo account admin o host.</p>
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <x-input name="email" label="Email" type="email" autocomplete="username" maxlength="255" :required="true" autofocus />
                    <x-input name="password" label="Password" type="password" autocomplete="current-password" maxlength="255" :required="true" />
                    <div class="form-check mb-4">
                        <input id="remember" name="remember" type="checkbox" class="form-check-input" value="1" @checked(old('remember'))>
                        <label for="remember" class="form-check-label">Ricordami su questo dispositivo</label>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Entra in Sogiornia <span aria-hidden="true">→</span></button>
                </form>
                <p class="small text-muted-custom mt-4 mb-0">Non hai ancora un account o hai dimenticato la password? Contatta l'amministratore del servizio.</p>
            </div>
        </div>
    </div>
</div>
@endsection
