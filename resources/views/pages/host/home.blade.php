@extends('layouts.app')
@section('title', 'Dashboard host')

{{-- Riepilogo operativo dei soggiorni nelle proprie strutture. --}}
@section('content')
<div class="container py-5 workspace">
    <div class="page-heading">
        <div><span class="eyebrow">IL TUO SPAZIO HOST</span><h1 class="mt-2">{{ $properties->isEmpty() ? 'Prepariamo la tua prima guida.' : 'Il tuo spazio per accogliere.' }}</h1><p class="text-muted-custom">Ciao {{ auth()->user()->name }}, qui trovi i soggiorni e i loro link di accesso.</p></div>
        @if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.bookings.create') }}">+ Nuovo soggiorno</a>@endif
    </div>
    {{-- Accesso sempre disponibile al percorso, anche per configurare altre strutture. --}}
    <div class="setup-tip mb-4"><strong>{{ $properties->isEmpty() ? 'Benvenuto! Prepariamo la tua prima guida.' : 'Vuoi preparare una guida o controllare cosa manca?' }}</strong><p class="mb-0">Struttura, stanza, contenuti e soggiorno: <a href="{{ route('host.setup.index') }}">apri la configurazione guidata</a> e riprendi dai dati già salvati.</p></div>
    {{-- Indicatori navigabili per periodo del soggiorno. --}}
    <div class="row g-3 mb-4">
        @foreach(['active' => 'Soggiorni attivi', 'scheduled' => 'Programmati', 'past' => 'Passati'] as $key => $label)
            <div class="col-md-4"><x-metric-card :label="$label" :value="$counts[$key]" :href="route('host-home', [...$filters, 'period' => $key])" :class="$period === $key ? 'metric-selected' : ''" /></div>
        @endforeach
    </div>
    <section class="sg-card">
        <div class="d-flex flex-wrap justify-content-between gap-3 mb-3"><h2 class="h4 mb-0">Il calendario dei soggiorni</h2><a href="{{ route('host.properties.index') }}">Gestisci strutture e stanze ↗</a></div>
        <nav class="nav nav-pills workspace-tabs mb-3" aria-label="Periodo dei soggiorni">
            @foreach(['active' => 'Attivi', 'scheduled' => 'Programmati', 'past' => 'Passati', 'all' => 'Tutti'] as $key => $label)
                <a class="nav-link {{ $period === $key ? 'active' : '' }}" @if($period === $key) aria-current="page" @endif href="{{ route('host-home', [...$filters, 'period' => $key]) }}">{{ $label }}</a>
            @endforeach
        </nav>
        <x-stay-filters :properties="$properties" :filters="$filters" :period="$period" />
        <p class="small text-muted-custom">I periodi dipendono dalle date di arrivo e partenza. La colonna “Stato guida” segnala anche link revocati o guide sospese.</p>
        <x-stay-table :stays="$stays" :host-links="true" />
        @if($stays->total() === 0 && auth()->user()->canWrite())<a class="btn btn-outline-secondary" href="{{ route('host.bookings.create') }}">Inserisci un soggiorno</a>@endif
    </section>
</div>
@endsection
