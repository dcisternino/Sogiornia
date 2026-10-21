@extends('layouts.app')
@section('title', 'Il mio profilo')

{{-- Modifica personale protetta dalla password corrente e dallo stato dell'account. --}}
@section('content')
<div class="container py-5 form-container">
    <h1>Il mio profilo</h1>
    @if ($user->canWrite())
        <form class="sg-card mt-4" method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PATCH')
            <x-input name="name" label="Nome" :value="$user->name" maxlength="255" :required="true" />
            <x-input name="email" label="Email" type="email" :value="$user->email" maxlength="255" :required="true" help="Usa un indirizzo che controlli. Sarà utilizzato per accesso e recupero password." />
            <x-input name="current_password" label="Password corrente" type="password" autocomplete="current-password" :required="true" />
            <x-input name="password" label="Nuova password (facoltativa)" type="password" minlength="12" maxlength="255" autocomplete="new-password" help="Lascia vuoto per mantenere la password attuale. Almeno 12 caratteri, maiuscole, minuscole e numeri." />
            <x-input name="password_confirmation" label="Conferma nuova password" type="password" autocomplete="new-password" />
            <button class="btn btn-primary" type="submit">Aggiorna profilo</button>
        </form>
    @else
        <div class="sg-card mt-4"><p>{{ $user->name }}</p><p>{{ $user->email }}</p><p class="mb-0 text-muted-custom">Account in sola lettura. Contatta l'amministratore per modificarlo.</p></div>
    @endif
</div>
@endsection
