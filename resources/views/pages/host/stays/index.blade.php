@extends('layouts.app')
@section('title', 'Soggiorni · '.$room->name)

{{-- Storico dei soggiorni con link distinti e revoca anticipata. --}}
@section('content')
<div class="container py-5">
    <a href="{{ route('host.properties.show', $property) }}">← {{ $property->name }}</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">LINK PERSONALI PER SOGGIORNO</span><h1>{{ $room->name }}</h1><p class="text-muted-custom">Scadenza automatica alla partenza. Orari italiani.</p></div>
        @if (auth()->user()->canWrite() && ! $property->archived_at && ! $room->archived_at)
            <a class="btn btn-primary" href="{{ route('host.stays.create', [$property, $room]) }}">+ Nuovo soggiorno</a>
        @endif
    </div>
    @foreach ($stays as $stay)
        <article class="sg-card mb-4">
            <div class="d-flex justify-content-between flex-wrap gap-2"><h2 class="h4">{{ $stay->reference }}</h2><span class="badge badge-soft">{{ $stay->statusLabel() }}</span></div>
            <p class="text-muted-custom">{{ $stay->starts_at->timezone('Europe/Rome')->format('d/m/Y H:i') }} → {{ $stay->ends_at->timezone('Europe/Rome')->format('d/m/Y H:i') }}</p>
            @if (! $stay->revoked_at && $stay->ends_at->isFuture())
                <label class="form-label" for="stay-link-{{ $stay->id }}">Link del soggiorno</label>
                <div class="input-group">
                    <input class="form-control" id="stay-link-{{ $stay->id }}" readonly value="{{ route('guide.index', $stay->guide_token) }}">
                    <button class="btn btn-outline-secondary" type="button" data-copy-target="stay-link-{{ $stay->id }}">Copia</button>
                </div>
                <p class="small text-muted-custom mt-2">Il link funziona solo durante il periodo indicato. Chi lo possiede può consultare la guida.</p>
                @if ($stay->isAccessible())<a href="{{ route('guide.index', $stay->guide_token) }}" target="_blank" rel="noopener noreferrer">Apri guida ↗</a>@endif
                @if (auth()->user()->canWrite())
                    <form class="mt-3" method="POST" action="{{ route('host.stays.revoke', [$property, $room, $stay]) }}" data-confirm="Revocare definitivamente questo link? Sarà necessario un nuovo soggiorno per generare un altro accesso.">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-outline-danger" type="submit">Revoca accesso</button>
                    </form>
                @endif
            @endif
        </article>
    @endforeach
    @if ($stays->isEmpty())<div class="sg-card">Nessun soggiorno presente. Crea il primo per ottenere un link ospite.</div>@endif
    {{ $stays->links() }}
</div>
@endsection
