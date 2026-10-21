@props(['hosts', 'showProperties' => true])
{{-- Elenco riutilizzabile con accesso diretto alla scheda del singolo host. --}}
<div {{ $attributes->class(['table-responsive workspace-table']) }}>
    <table class="table align-middle mb-0">
        <caption>Apri il nome di un host per visualizzare il profilo e gestire l'accesso.</caption>
        <thead><tr><th>Host</th><th>Stato</th><th>Scadenza accesso</th>@if($showProperties)<th>Strutture</th>@endif</tr></thead>
        <tbody>
        @forelse($hosts as $host)
            <tr>
                <th scope="row"><a href="{{ route('admin.hosts.show', $host) }}">{{ $host->name }} <span aria-hidden="true">↗</span></a><br><span class="small fw-normal text-muted-custom text-break">{{ $host->email }}</span></th>
                <td><span class="badge {{ $host->hasActiveAccess() ? 'status-active' : 'status-inactive' }}">{{ $host->hasActiveAccess() ? 'Attivo' : 'Disattivo' }}</span><br><small class="text-muted-custom">{{ $host->hasPendingInvitation() ? 'Invito da accettare' : 'Invito accettato' }}</small></td>
                <td>{{ $host->hostAccess?->expires_at?->timezone('Europe/Rome')->subSecond()->format('d/m/Y') ?? 'Senza scadenza' }}</td>
                @if($showProperties)<td>{{ $host->properties_count }}</td>@endif
            </tr>
        @empty
            <tr><td colspan="{{ $showProperties ? 4 : 3 }}" class="py-5 text-center text-muted-custom">Nessun host da mostrare.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $hosts->links() }}</div>
