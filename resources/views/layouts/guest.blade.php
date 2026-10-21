@extends('layouts.base')
@section('referrer', 'no-referrer')

{{-- Scheda della guida ospite, ottimizzata per la lettura da telefono. --}}
@section('body')
    <main id="main-content" class="guest-guide guest-experience" tabindex="-1">
        <div class="guest-shell">
            {{-- Identità Sogiornia e indicazione della guida corrente. --}}
            <header class="guest-topbar">
                <a class="guest-brand" href="{{ $token ? route('guide.index', ['token' => $token]) : route('guest-home') }}" aria-label="Sogiornia — vai alla tua guida">
                    <img src="{{ asset('image/sogiornia-logo.png') }}" alt="Sogiornia" width="2172" height="724" decoding="async">
                </a>
                <x-guest-language-menu />
            </header>

            @yield('content')

            {{-- Firma discreta in fondo alla guida. --}}
            <footer class="guest-footer">
                Un piccolo aiuto per sentirti a casa.<br>
                <a href="{{ route('welcome') }}">Con cura, Sogiornia.</a>
            </footer>
        </div>
    </main>
@endsection
