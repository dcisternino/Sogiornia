@extends('layouts.base')

{{-- Navigazione e contenuti delle pagine pubbliche e riservate. --}}
@section('body')
<x-navbar />
<main id="main-content" tabindex="-1">
    <x-flash />
    @yield('content')
</main>
<footer class="site-footer">
    <div class="container d-flex flex-wrap justify-content-between gap-3">
        <a class="navbar-brand">
            <img
                src="{{ asset('image/sogiornia-logo.png') }}"
                alt="Sogiornia"
                style="width: 180px; max-width: 100%; height: auto;">
        </a>
        <span>Più tempo per accogliere. Più libertà per i tuoi ospiti.</span>
    </div>
</footer>
@endsection