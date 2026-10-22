@extends('layouts.app')
@section('title', 'Soggiorni')

{{-- Calendario mensile e riepiloghi, separati dalla dashboard. --}}
@section('content')
<div class="container py-5 workspace">
    <div class="page-heading"><div><span class="eyebrow">PRENOTAZIONI E LINK OSPITE</span><h1 class="mt-2">I tuoi soggiorni</h1><p class="text-muted-custom">Controlla le notti prenotate e aggiungi un soggiorno direttamente dal giorno di arrivo.</p></div>@if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ $setupNavigation->url('host.bookings.create', $canCreate ? array_filter(['property_id' => $filters['property_id'] ?? null, 'room_id' => $filters['room_id'] ?? null]) : []) }}">+ Nuovo soggiorno</a>@endif</div>
    {{-- Riepiloghi e filtri della tabella mantengono il mese visualizzato. --}}
    <div class="row g-3 mb-4">
        @foreach(['active' => 'Soggiorni attivi', 'scheduled' => 'Programmati', 'past' => 'Passati'] as $key => $label)
            <div class="col-md-4"><x-metric-card :label="$label" :value="$counts[$key]" :href="$setupNavigation->url('host.calendar.index', [...$filters, 'month' => $month, 'period' => $key])" :class="$period === $key ? 'metric-selected' : ''" /></div>
        @endforeach
    </div>
    <x-stay-filters :properties="$properties" :filters="$filters" :period="$period" :month="$month" />
    <x-stay-calendar :calendar="$calendar" :month="$month" :filters="$filters" :period="$period" :can-create="$canCreate" />
    <section class="sg-card mt-4">
        <div class="d-flex flex-wrap justify-content-between gap-3 mb-3"><h2 class="h4 mb-0">Elenco dei soggiorni</h2><a href="{{ $setupNavigation->url('host.properties.index') }}">Gestisci strutture ↗</a></div>
        <nav class="nav nav-pills workspace-tabs mb-3" aria-label="Periodo dei soggiorni">
            @foreach(['active' => 'Attivi', 'scheduled' => 'Programmati', 'past' => 'Passati', 'all' => 'Tutti'] as $key => $label)
                <a class="nav-link {{ $period === $key ? 'active' : '' }}" @if($period === $key) aria-current="page" @endif href="{{ $setupNavigation->url('host.calendar.index', [...$filters, 'month' => $month, 'period' => $key]) }}">{{ $label }}</a>
            @endforeach
        </nav>
        <p class="small text-muted-custom">Le schede e le date filtrano questo elenco e i tre riepiloghi. Il calendario mensile mostra tutte le prenotazioni della struttura e della stanza selezionate.</p>
        <x-stay-table :stays="$stays" :host-links="true" />
    </section>
</div>
@endsection
