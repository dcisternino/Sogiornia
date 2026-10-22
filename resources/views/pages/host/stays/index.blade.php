@extends('layouts.app')
@section('title', 'Soggiorni · '.$room->name)

{{-- Storico dei soggiorni con link distinti e revoca anticipata. --}}
@section('content')
<div class="container py-5 workspace">
    <a href="{{ $setupNavigation->back('host.rooms.index', $property) }}">@if($setupNavigation->active())← Torna alla configurazione guidata @else ← {{ $property->name }} @endif</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">LINK PERSONALI PER SOGGIORNO</span><h1>{{ $room->name }}</h1><p class="text-muted-custom">Scadenza automatica alla partenza. Orari italiani.</p></div>
        @if (auth()->user()->canWrite() && ! $property->archived_at && ! $room->archived_at)
            <a class="btn btn-primary" href="{{ $setupNavigation->url('host.stays.create', [$property, $room]) }}">+ Nuovo soggiorno</a>
        @endif
    </div>
    @if($selectedStay)<p><a href="{{ $setupNavigation->back('host.stays.index', [$property, $room]) }}">@if($setupNavigation->active())← Torna alla configurazione guidata @else ← Mostra tutti i soggiorni della stanza @endif</a></p>@endif
    {{-- Istruzioni pratiche per condividere il link senza confonderlo con la configurazione della stanza. --}}
    <div class="setup-tip mb-4"><strong>Un link diverso per ogni soggiorno</strong><p class="mb-0">Il link si apre dall'arrivo e scade automaticamente alla partenza. Puoi copiarlo e condividerlo con gli ospiti. @if(auth()->user()->canWrite())Per cambiare foto o sezioni usa <a href="{{ $setupNavigation->url('host.rooms.edit', [$property, $room]) }}">la configurazione della stanza</a>.@endif</p></div>
    @foreach ($stays as $stay)
        <article class="sg-card mb-4">
            <div class="d-flex justify-content-between flex-wrap gap-2"><h2 class="h4">{{ $stay->reference }}</h2><span class="badge badge-soft">{{ $stay->statusLabel() }}</span></div>
            <p class="text-muted-custom">{{ $stay->starts_at->timezone('Europe/Rome')->format('d/m/Y H:i') }} → {{ $stay->ends_at->timezone('Europe/Rome')->format('d/m/Y H:i') }}</p>
            @if(auth()->user()->canWrite() && !$property->archived_at && !$room->archived_at)<a class="btn btn-sm btn-outline-secondary mb-3" href="{{ $setupNavigation->url('host.stays.edit', [$property, $room, $stay]) }}">Modifica soggiorno<span class="visually-hidden"> {{ $stay->reference }}</span></a>@endif
            @if (! $stay->revoked_at && $stay->ends_at->isFuture())
                <label class="form-label" for="stay-link-{{ $stay->id }}">Link del soggiorno</label>
                <div class="input-group">
                    <input class="form-control" id="stay-link-{{ $stay->id }}" readonly value="{{ $setupNavigation->url('guide.index', $stay->guide_token) }}">
                    <button class="btn btn-outline-secondary" type="button" data-copy-target="stay-link-{{ $stay->id }}">Copia</button>
                </div>
                <p class="small text-muted-custom mt-2">Il link funziona solo durante il periodo indicato. Chi lo possiede può consultare la guida.</p>
                @if(!$property->archived_at && !$room->archived_at && $property->guide_enabled && $room->guide_enabled)<a class="btn btn-outline-secondary mb-3" href="{{ $setupNavigation->url('host.stays.share', [$property, $room, $stay]) }}">Condividi via WhatsApp o email</a>@endif
                @if ($stay->isAccessible())<a href="{{ $setupNavigation->url('guide.index', $stay->guide_token) }}" target="_blank" rel="noopener noreferrer">Apri guida ↗</a>@endif
                @if (auth()->user()->canWrite())
                    <form class="mt-3" method="POST" action="{{ $setupNavigation->url('host.stays.revoke', [$property, $room, $stay]) }}" data-confirm="Revocare definitivamente questo link? Sarà necessario un nuovo soggiorno per generare un altro accesso.">
                        @csrf <x-onboarding-context /> @method('PATCH')
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
