@extends('layouts.base')
@section('referrer', 'no-referrer')
@section('language', app()->getLocale())
@section('skip-label', __('guest.skip_content'))
@section('description', __('guest.meta_description'))

{{-- Scheda della guida ospite, ottimizzata per la lettura da telefono. --}}
@section('body')
    <main id="main-content" class="guest-guide guest-experience" tabindex="-1">
        <div class="guest-shell">
            {{-- Identità Sogiornia e indicazione della guida corrente. --}}
            <header class="guest-topbar">
                <a class="guest-brand" href="{{ $homeUrl }}" aria-label="{{ __('guest.guide_home') }}">
                    <img src="{{ asset('image/sogiornia-logo.png') }}" alt="Sogiornia" width="2172" height="724" decoding="async">
                </a>
                <x-guest-language-menu />
            </header>
            {{-- Anteprima privata: non genera né pubblica un link di soggiorno. --}}
            @if($isPreview)<div class="guest-preview-note" role="status"><strong>{{ __('guest.preview_banner') }}</strong><p>{{ __('guest.preview_help') }}</p><a href="{{ $previewExitUrl }}">{{ __('guest.preview_exit') }} ↗</a></div>@endif

            @yield('content')

            {{-- Firma discreta in fondo alla guida. --}}
            <footer class="guest-footer">
                {{ __('guest.footer_help') }}<br>
                <a>{{ __('guest.footer_signature') }}</a>
            </footer>
        </div>
    </main>
@endsection
