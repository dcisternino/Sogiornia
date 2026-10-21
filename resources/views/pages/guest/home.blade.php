@extends('layouts.guest')

@section('title', $propertyName)

{{-- Intestazione della struttura e della stanza selezionata dal link. --}}
@section('content')
<div class="container py-5 guide-container">
    <header class="mb-5">
        <span class="eyebrow">LA TUA GUIDA AL SOGGIORNO</span>
        <h1 class="display-5 mt-3">{{ $propertyName }}</h1>
        @if ($roomName)<p class="room-label">{{ $roomName }}</p>@endif
        <p class="lead text-muted-custom preserve-lines">{{ $welcomeMessage }}</p>
        @unless ($token)<span class="badge badge-soft">Guida dimostrativa · dati di esempio</span>@endunless
    </header>

    {{-- Elenco delle sole sezioni consentite, con collegamenti alla guida corrente. --}}
    <section aria-label="Informazioni per il soggiorno" class="row g-3">
        @forelse ($sections as $slug => $section)
            <div class="col-12 col-md-6">
                <a href="{{ $token ? route('guide.show', ['token' => $token, 'section' => $slug]) : route('guest.section', ['section' => $slug]) }}" class="sg-card guide-card d-flex align-items-center justify-content-between h-100">
                    <h2 class="h5 mb-0">{{ $section['title'] }}</h2>
                    <span aria-hidden="true">↗</span>
                </a>
            </div>
        @empty
            <div class="col-12"><div class="sg-card"><h2 class="h5">La tua guida si sta preparando</h2><p class="mb-0 text-muted-custom">L'host non ha ancora abilitato informazioni per questa stanza. Contattalo per assistenza.</p></div></div>
        @endforelse
    </section>
</div>
@endsection
