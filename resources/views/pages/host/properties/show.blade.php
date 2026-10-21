@extends('layouts.app')

@section('title', $property->name)

{{-- Intestazione e indicazioni per preparare la guida della struttura. --}}
@section('content')
<div class="container py-5">
    <a class="back-link" href="{{ route('host-home') }}">← Le mie strutture</a>
    <div class="page-heading mt-4">
        <div><span class="eyebrow">{{ $property->city }}</span><h1 class="mt-2">{{ $property->name }}</h1><p class="text-muted-custom mb-0">{{ $property->address }}</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('host.properties.edit', $property) }}">Modifica struttura</a>
    </div>
    <div class="onboarding-note mb-5"><strong>La tua guida, in ordine.</strong> Crea le sezioni informative, aggiungi le stanze e scegli cosa mostrare in ognuna. Poi condividi il link con gli ospiti.</div>

    {{-- Catalogo dei contenuti riutilizzabili nelle stanze della struttura. --}}
    <section class="mb-5" aria-labelledby="sections-title">
        <div class="page-heading compact"><div><span class="eyebrow">01 · PREPARA I CONTENUTI</span><h2 class="mt-2" id="sections-title">Sezioni informative</h2></div><a class="btn btn-outline-secondary" href="{{ route('host.sections.create', $property) }}">+ Nuova sezione</a></div>
        <div class="sg-card">
            @forelse ($sections as $section)
                <div class="section-row">
                    <div><h3 class="h6 mb-1">{{ $section->title }}</h3><span class="small text-muted-custom">{{ $section->slug }}</span></div>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('host.sections.edit', [$property, $section]) }}">Modifica<span class="visually-hidden"> {{ $section->title }}</span></a>
                        <form method="POST" action="{{ route('host.sections.destroy', [$property, $section]) }}" data-confirm="Eliminare questa sezione da tutte le guide?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Elimina<span class="visually-hidden"> {{ $section->title }}</span></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state py-4"><h3 class="h5">Cosa devono sapere i tuoi ospiti?</h3><p class="text-muted-custom mb-0">Inizia con Wi-Fi, check-in, regole della casa o i tuoi consigli sul territorio.</p></div>
            @endforelse
            {{ $sections->withQueryString()->links() }}
        </div>
    </section>

    {{-- Stanze, visibilità delle sezioni e gestione dei link riservati. --}}
    <section aria-labelledby="rooms-title">
        <div class="page-heading compact"><div><span class="eyebrow">02 · PERSONALIZZA E CONDIVIDI</span><h2 class="mt-2" id="rooms-title">Le stanze</h2></div><a class="btn btn-primary" href="{{ route('host.rooms.create', $property) }}">+ Nuova stanza</a></div>
        <div class="row g-4">
            @forelse ($rooms as $room)
                <div class="col-lg-6">
                    <article class="sg-card h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3"><h3 class="h4">{{ $room->name }}</h3><span class="badge badge-soft">{{ $room->sections_count }} {{ $room->sections_count === 1 ? 'sezione' : 'sezioni' }}</span></div>
                        @if ($room->description)<p class="text-muted-custom preserve-lines">{{ $room->description }}</p>@endif
                        <a class="small fw-semibold" href="{{ route('host.rooms.edit', [$property, $room]) }}">Modifica stanza e sezioni →</a>
                        <div class="guide-link-box mt-4">
                            <label class="form-label small" for="guide-{{ $room->id }}">Link riservato per gli ospiti</label>
                            <div class="input-group"><input class="form-control form-control-sm" id="guide-{{ $room->id }}" readonly value="{{ route('guide.index', $room->guide_token) }}"><button type="button" class="btn btn-outline-secondary btn-sm" data-copy-target="guide-{{ $room->id }}">Copia</button></div>
                            <p class="small text-muted-custom mt-2 mb-3">Chi possiede il link può leggere le sezioni abilitate. Condividilo in modo riservato.</p>
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <a class="small fw-semibold" href="{{ route('guide.index', $room->guide_token) }}" target="_blank" rel="noopener noreferrer">Apri guida ↗</a>
                                <form method="POST" action="{{ route('host.rooms.guide-link.update', [$property, $room]) }}" data-confirm="Rigenerare il link? Quello precedente smetterà di funzionare.">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0">Revoca e rigenera link</button>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="sg-card empty-state"><h3 class="h5">Ogni stanza merita il suo benvenuto.</h3><p class="text-muted-custom mb-0">Crea una stanza per scegliere le sezioni visibili e ottenere il suo link ospite.</p></div></div>
            @endforelse
        </div>
        <div class="mt-4">{{ $rooms->withQueryString()->links() }}</div>
    </section>
</div>
@endsection
