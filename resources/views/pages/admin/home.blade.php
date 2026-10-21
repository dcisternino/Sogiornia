@extends('layouts.app')
@section('title', 'Dashboard amministratore')

{{-- Gestione degli inviti e dello stato operativo degli host. --}}
@section('content')
<div class="container py-5">
    <div class="page-heading"><div><span class="eyebrow">AREA AMMINISTRATORE</span><h1 class="mt-2">Gli host di Sogiornia</h1><p class="text-muted-custom">{{ $hostCount }} account registrati.</p></div><a class="btn btn-primary" href="{{ route('admin.hosts.create') }}">+ Invita un host</a></div>
    @if (in_array(config('mail.default'), ['log', 'array'], true))
        <div class="alert alert-warning">L'invio email reale non è ancora configurato. Gli inviti restano disponibili per la verifica in sviluppo: configura il mittente e il servizio SMTP prima di invitare utenti reali.</div>
    @endif
    <div class="sg-card table-responsive">
        <table class="table align-middle">
            <caption>Gli host disattivati possono accedere in sola lettura; i loro soggiorni esistenti restano validi.</caption>
            <thead><tr><th>Host</th><th>Email</th><th>Stato</th><th>Strutture</th><th>Azioni</th></tr></thead>
            <tbody>
                @forelse ($hosts as $host)
                    <tr>
                        <th scope="row">{{ $host->name }}</th><td class="text-break">{{ $host->email }}</td>
                        <td>{{ $host->is_active ? 'Attivo' : 'Sola lettura' }}<br><small class="text-muted-custom">{{ $host->hasPendingInvitation() ? 'Invito da accettare' : 'Account pronto' }}</small></td>
                        <td>{{ $host->properties_count }}</td>
                        <td>
                            @if ($host->hasPendingInvitation())
                                <form class="mb-2" method="POST" action="{{ route('admin.hosts.invitation', $host) }}">@csrf<button class="btn btn-sm btn-outline-secondary" type="submit">Reinvia invito</button></form>
                            @endif
                            <form method="POST" action="{{ route('admin.hosts.status', $host) }}" data-confirm="{{ $host->is_active ? 'Impostare questo host in sola lettura? I soggiorni in corso resteranno accessibili.' : 'Riattivare questo host?' }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="is_active" value="{{ $host->is_active ? '0' : '1' }}">
                                <button class="btn btn-sm {{ $host->is_active ? 'btn-outline-danger' : 'btn-outline-secondary' }}" type="submit">{{ $host->is_active ? 'Disattiva' : 'Riattiva' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Nessun host registrato. Inizia con un invito.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $hosts->links() }}
    </div>
</div>
@endsection
