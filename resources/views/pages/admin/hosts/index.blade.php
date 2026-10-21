@extends('layouts.app')
@section('title', 'Host')

{{-- Filtro degli account per stato effettivo, inclusa la scadenza automatica. --}}
@section('content')
<div class="container py-5 workspace">
    <div class="page-heading"><div><span class="eyebrow">GESTIONE ACCOUNT</span><h1 class="mt-2">Gli host di Sogiornia</h1><p class="text-muted-custom">Apri un host per consultare dati, strutture e soggiorni.</p></div><a class="btn btn-primary" href="{{ route('admin.hosts.create') }}">+ Crea un host</a></div>
    <section class="sg-card">
        <form method="GET" action="{{ route('admin.hosts.index') }}" class="d-flex flex-wrap align-items-end gap-3 mb-4">
            <div><label class="form-label" for="status">Stato account</label><select class="form-select" name="status" id="status"><option value="">Tutti gli host</option>@foreach(['active' => 'Attivi', 'inactive' => 'Disattivi', 'expiring' => 'In scadenza entro 10 giorni'] as $key => $label)<option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>@endforeach</select></div>
            <button class="btn btn-primary" type="submit">Filtra</button>@if($status)<a class="btn btn-outline-secondary" href="{{ route('admin.hosts.index') }}">Azzera filtro</a>@endif
        </form>
        <x-host-table :hosts="$hosts" />
    </section>
</div>
@endsection
