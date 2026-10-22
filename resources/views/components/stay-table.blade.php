@props(['stays', 'hostLinks' => false])
{{-- Storico e calendario comuni alle schede admin e host. --}}
<div {{ $attributes->class(['table-responsive workspace-table']) }}>
    <table class="table align-middle mb-0">
        <caption>Soggiorni ordinati per arrivo. Gli orari sono riferiti all'Italia.</caption>
        <thead><tr><th>Riferimento</th><th>Struttura / stanza</th><th>Arrivo</th><th>Partenza</th><th>Stato guida</th>@if($hostLinks)<th><span class="visually-hidden">Azioni</span></th>@endif</tr></thead>
        <tbody>
        @forelse($stays as $stay)
            <tr>
                <th scope="row">{{ $stay->reference }}</th>
                <td>{{ $stay->room->property->name }}<br><small class="text-muted-custom">{{ $stay->room->name }}</small></td>
                <td class="text-nowrap">{{ $stay->starts_at->timezone('Europe/Rome')->format('d/m/Y H:i') }}</td>
                <td class="text-nowrap">{{ $stay->ends_at->timezone('Europe/Rome')->format('d/m/Y H:i') }}</td>
                <td><span class="badge badge-soft">{{ $stay->statusLabel() }}</span></td>
                @if($hostLinks)<td><div class="d-flex flex-wrap gap-2"><a class="btn btn-sm btn-outline-secondary text-nowrap" href="{{ route('host.stays.index', [$stay->room->property_id, $stay->room_id]) }}">Gestisci link<span class="visually-hidden"> di {{ $stay->reference }}</span></a>@if(auth()->user()->canWrite() && !$stay->room->archived_at && !$stay->room->property->archived_at)<a class="btn btn-sm btn-outline-secondary" href="{{ route('host.stays.edit', [$stay->room->property_id, $stay->room_id, $stay]) }}">Modifica<span class="visually-hidden"> {{ $stay->reference }}</span></a>@endif</div></td>@endif
            </tr>
        @empty
            <tr><td colspan="{{ $hostLinks ? 6 : 5 }}" class="py-5 text-center text-muted-custom">Nessun soggiorno da mostrare.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $stays->links() }}</div>
