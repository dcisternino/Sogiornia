@extends('layouts.base')

{{-- Guida essenziale senza navigazione verso le aree di gestione. --}}
@section('body')
    <main id="main-content" class="guest-guide" tabindex="-1">
        @yield('content')
    </main>
    <footer class="text-center pb-4 small text-muted-custom">
        Accoglienza curata con <a href="{{ route('welcome') }}">Sogiornia</a>
    </footer>
@endsection
