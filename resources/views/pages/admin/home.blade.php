@extends('layouts.app')
@section('title', 'Dashboard amministratore')

{{-- Stato generale degli account e accesso rapido alle attività amministrative. --}}
@section('content')
<div class="container py-5 workspace">
    <div class="page-heading"><div><span class="eyebrow">AREA AMMINISTRATORE</span><h1 class="mt-2">La tua rete di ospitalità.</h1><p class="text-muted-custom">Gestisci gli host e tieni sotto controllo le prossime scadenze.</p></div><a class="btn btn-primary" href="{{ route('admin.hosts.create') }}">+ Crea un host</a></div>
    <div class="row g-3 mb-4">
        <div class="col-md-4"><x-metric-card label="Host attivi" :value="$activeCount" :href="route('admin.hosts.index', ['status' => 'active'])" hint="Possono gestire strutture e soggiorni" /></div>
        <div class="col-md-4"><x-metric-card label="Host disattivi" :value="$inactiveCount" :href="route('admin.hosts.index', ['status' => 'inactive'])" hint="Account disattivati o con accesso scaduto" /></div>
        <div class="col-md-4"><x-metric-card label="In scadenza" :value="$expiringCount" :href="route('admin.hosts.index', ['status' => 'expiring'])" hint="Accesso in scadenza nei prossimi 10 giorni" /></div>
    </div>
    {{-- Elenco delle scadenze con collegamento al profilo amministrativo dell'host. --}}
    <section class="sg-card"><div class="d-flex flex-wrap justify-content-between gap-3 mb-3"><h2 class="h4 mb-0">Scadenze da seguire</h2><a href="{{ route('admin.hosts.index') }}">Tutti gli host ↗</a></div><x-host-table :hosts="$hosts" :show-properties="false" /></section>
</div>
@endsection
