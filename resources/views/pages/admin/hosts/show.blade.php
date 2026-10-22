@extends('layouts.app')
@section('title', 'Host · '.$host->name)

{{-- Identità e riepilogo dell'attività dell'host selezionato. --}}
@section('content')
<div class="container py-5 workspace">
    <a class="back-link" href="{{ route('admin.hosts.index') }}">← Tutti gli host</a>
    <a class="d-inline-block ms-3" href="{{ route('admin.monitoring.index', ['host_id' => $host->id]) }}">Consumi e limiti di traduzione ↗</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">PROFILO HOST #{{ $host->id }}</span><h1 class="mt-2">{{ $host->name }}</h1><p class="text-muted-custom text-break">{{ $host->email }}</p></div><span class="badge {{ $host->hasActiveAccess() ? 'status-active' : 'status-inactive' }}">{{ $host->hasActiveAccess() ? 'Attivo' : 'Disattivo · sola lettura' }}</span></div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><x-metric-card label="Strutture" :value="$host->properties_count" /></div>
        <div class="col-6 col-lg-3"><x-metric-card label="Stanze" :value="$roomCount" /></div>
        <div class="col-6 col-lg-3"><x-metric-card label="Soggiorni totali" :value="$stayCount" /></div>
        <div class="col-6 col-lg-3"><x-metric-card label="Soggiorni programmati" :value="$counts['scheduled']" /></div>
    </div>
    {{-- Dati account e controlli amministrativi separati dal profilo personale. --}}
    <div class="row g-4 mb-4">
        <section class="col-lg-6"><div class="sg-card h-100"><h2 class="h4">Dati dell'account</h2><dl class="account-details">
            <dt>Nome</dt><dd>{{ $host->name }}</dd><dt>Email</dt><dd class="text-break">{{ $host->email }}</dd><dt>Registrato il</dt><dd>{{ $host->created_at->timezone('Europe/Rome')->format('d/m/Y H:i') }}</dd>
            <dt>Ultimo aggiornamento</dt><dd>{{ $host->updated_at->timezone('Europe/Rome')->format('d/m/Y H:i') }}</dd>
            <dt>Invito inviato</dt><dd>{{ $host->invited_at?->timezone('Europe/Rome')->format('d/m/Y H:i') ?? 'Non registrato' }}</dd>
            <dt>Invito accettato</dt><dd>{{ $host->invitation_accepted_at?->timezone('Europe/Rome')->format('d/m/Y H:i') ?? 'Non ancora registrato' }}</dd>
            <dt>Email verificata</dt><dd>{{ $host->email_verified_at?->timezone('Europe/Rome')->format('d/m/Y H:i') ?? 'Non verificata' }}</dd>
            <dt>Soggiorni attivi / passati</dt><dd>{{ $counts['active'] }} / {{ $counts['past'] }}</dd>
        </dl></div></section>
        <section class="col-lg-6"><div class="sg-card h-100"><h2 class="h4">Accesso e scadenza</h2>
            <p class="small text-muted-custom">Alla scadenza o disattivazione l'host resta in sola lettura. I soggiorni esistenti mantengono il proprio link.</p>
            <form method="POST" action="{{ route('admin.hosts.access', $host) }}" class="mb-4">@csrf @method('PATCH')
                <x-input name="expires_on" label="Ultimo giorno di accesso operativo" type="date" :value="$host->hostAccess?->expires_at?->timezone('Europe/Rome')->subSecond()->format('Y-m-d') ?? ''" help="Valido fino a fine giornata italiana. Lascia vuoto per nessuna scadenza." />
                <button class="btn btn-primary" type="submit">Salva scadenza</button>
            </form>
            <form method="POST" action="{{ route('admin.hosts.status', $host) }}" data-confirm="{{ $host->is_active ? 'Disattivare questo host? I soggiorni esistenti restano validi.' : 'Riabilitare questo host? Una scadenza già passata va aggiornata separatamente.' }}">@csrf @method('PATCH')<input type="hidden" name="is_active" value="{{ $host->is_active ? '0' : '1' }}"><button class="btn btn-outline-secondary" type="submit">{{ $host->is_active ? 'Disattiva account' : 'Riabilita account' }}</button></form>
            @if($host->is_active && !$host->hasActiveAccess())<p class="small mt-3">La scadenza è trascorsa: aggiorna la data per ripristinare le modifiche.</p>@endif
            @if($host->hasPendingInvitation())<form class="mt-3" method="POST" action="{{ route('admin.hosts.invitation', $host) }}">@csrf<button class="btn btn-outline-secondary" type="submit">Reinvia invito</button></form>@endif
            @if(in_array(config('mail.default'), ['log', 'array'], true))<p class="small mt-3 text-muted-custom">Invio email di sviluppo: configura il servizio email prima di invitare host reali.</p>@endif
        </div></section>
    </div>
    {{-- Inventario paginato per evitare pagine eccessive anche per host con molte stanze. --}}
    <section class="sg-card mb-4"><h2 class="h4 mb-3">Strutture</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Nome</th><th>Città / indirizzo</th><th>Stanze</th><th>Stato</th></tr></thead><tbody>
        @forelse($properties as $property)<tr><th scope="row">{{ $property->name }}</th><td>{{ $property->city }}<br><small>{{ $property->address }}</small></td><td>{{ $property->rooms_count }}</td><td>{{ $property->archived_at ? 'Archiviata' : ($property->guide_enabled ? 'Guida attiva' : 'Guida sospesa') }}</td></tr>@empty<tr><td colspan="4">Nessuna struttura.</td></tr>@endforelse
    </tbody></table></div>{{ $properties->links() }}</section>
    <section class="sg-card mb-4"><h2 class="h4 mb-3">Stanze</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Stanza</th><th>Struttura</th><th>Soggiorni</th><th>Stato</th></tr></thead><tbody>
        @forelse($rooms as $room)<tr><th scope="row">{{ $room->name }}</th><td>{{ $room->property->name }}</td><td>{{ $room->stays_count }}</td><td>{{ $room->archived_at ? 'Archiviata' : ($room->guide_enabled ? 'Guida attiva' : 'Guida sospesa') }}</td></tr>@empty<tr><td colspan="4">Nessuna stanza.</td></tr>@endforelse
    </tbody></table></div>{{ $rooms->links() }}</section>
    {{-- Storico consultabile senza mostrare all'amministratore i link riservati degli ospiti. --}}
    <section class="sg-card"><h2 class="h4 mb-3">Soggiorni prenotati</h2><x-stay-table :stays="$stays" /></section>
</div>
@endsection
