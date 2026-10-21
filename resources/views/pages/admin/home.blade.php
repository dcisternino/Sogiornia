@extends('layouts.app')

@section('title', 'Dashboard amministratore')

{{-- Riepilogo degli account host e accesso alla loro creazione. --}}
@section('content')
<div class="container py-5">
    <div class="page-heading">
        <div><span class="eyebrow">AREA AMMINISTRATORE</span><h1 class="mt-2">Chi accoglie, parte da qui.</h1><p class="text-muted-custom">Gestisci gli account host che utilizzano Sogiornia.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.hosts.create') }}">+ Crea un host</a>
    </div>
    <div class="stat-card mb-4"><span class="text-muted-custom">Host registrati</span><strong>{{ $hostCount }}</strong></div>

    {{-- Elenco paginato degli host senza esposizione delle credenziali. --}}
    <div class="sg-card">
        <h2 class="h4 mb-4">I tuoi host</h2>
        @if ($hosts->isEmpty())
            <div class="empty-state"><span aria-hidden="true">✦</span><h3 class="h5 mt-3">Il primo benvenuto lo dai tu.</h3><p class="text-muted-custom">Crea il primo account host e condividi le credenziali in modo riservato.</p><a class="btn btn-primary" href="{{ route('admin.hosts.create') }}">Crea il primo host</a></div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <caption class="visually-hidden">Account host registrati su Sogiornia</caption>
                    <thead><tr><th scope="col">Host</th><th scope="col">Email di accesso</th><th scope="col">Strutture</th><th scope="col">Creato il</th></tr></thead>
                    <tbody>
                        @foreach ($hosts as $host)
                            <tr><th scope="row">{{ $host->name }}</th><td class="text-break">{{ $host->email }}</td><td>{{ $host->properties_count }}</td><td>{{ $host->created_at->format('d/m/Y') }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $hosts->links() }}
        @endif
    </div>
</div>
@endsection
