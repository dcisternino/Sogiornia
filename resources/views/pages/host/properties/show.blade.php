@extends('layouts.app')
@section('title', $property->name)

{{-- Punto di partenza per configurare struttura, contenuti e stanze. --}}
@section('content')
<div class="container py-5 workspace">
    <a class="back-link" href="{{ route('host.properties.index') }}">← Le mie strutture</a>
    <div class="page-heading mt-4">
        <div><span class="eyebrow">{{ $property->city }}</span><h1 class="mt-2">{{ $property->name }}</h1><p class="text-muted-custom mb-2">{{ $property->address ?: 'Completa l’indirizzo dalle impostazioni della struttura.' }}</p>
            <span class="badge badge-soft">{{ $property->archived_at ? 'Archiviata' : 'Attiva' }} · {{ $property->guide_enabled ? 'Guide abilitate' : 'Guide sospese' }}</span>
        </div>
        @if (auth()->user()->canWrite())<a class="btn btn-outline-secondary" href="{{ route('host.properties.edit', $property) }}">Modifica i dati della struttura</a>@endif
    </div>
    <x-setup-steps :property="$property" current="sections" class="mb-4" />
    <div class="setup-tip mb-5"><strong>Come preparare la tua guida</strong><p class="mb-0">Crea le sezioni con Wi-Fi, check-in e consigli. Poi aggiungi le stanze, una foto e scegli quali sezioni mostrare in ciascuna. Infine crea un soggiorno dalla dashboard per ottenere il link ospite.</p></div>

    {{-- Sezioni condivise, da scrivere una sola volta e abilitare nelle singole stanze. --}}
    <section id="sezioni" class="mb-5 anchor-section" aria-labelledby="sections-title">
        <div class="page-heading compact"><div><span class="eyebrow">CONTENUTI DELLA GUIDA</span><h2 id="sections-title" class="h3 mt-2">Le sezioni informative <span class="item-count">{{ $sections->total() }}</span></h2><p class="small text-muted-custom mb-0">Scrivile una volta. Scegli poi in quali stanze renderle visibili.</p></div>@if (auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.sections.create', $property) }}">+ Crea una sezione</a>@endif</div>
        <div class="sg-card">
            @forelse ($sections as $section)
                <div class="section-row">
                    <div class="d-flex gap-3 align-items-start"><span class="section-symbol"><x-guest-section-icon :slug="$section->slug" /></span><div><h3 class="h6 mb-1">{{ $section->title }}</h3><p class="small text-muted-custom mb-0">{{ Str::limit($section->content, 110) }}</p></div></div>
                    @if (auth()->user()->canWrite())
                        <div class="d-flex gap-2 flex-shrink-0">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('host.sections.edit', [$property, $section]) }}">Modifica<span class="visually-hidden"> {{ $section->title }}</span></a>
                            <details class="section-menu"><summary class="btn btn-sm btn-outline-secondary" aria-label="Altre azioni per {{ $section->title }}">•••</summary><form class="mt-2" method="POST" action="{{ route('host.sections.destroy', [$property, $section]) }}" data-confirm="Eliminare questa sezione da tutte le guide?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Elimina<span class="visually-hidden"> {{ $section->title }}</span></button></form></details>
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-state py-4"><h3 class="h5">Quali informazioni chiedono più spesso i tuoi ospiti?</h3><p class="text-muted-custom mb-0">Inizia da Wi-Fi o check-in: trovi modelli già pronti nel modulo di creazione.</p></div>
            @endforelse
            {{ $sections->withQueryString()->links() }}
        </div>
    </section>

    {{-- Stanze con copertina, configurazione della guida e accesso ai soggiorni. --}}
    <section id="stanze" class="mb-5 anchor-section" aria-labelledby="rooms-title">
        <div class="page-heading compact"><div><span class="eyebrow">UN'ACCOGLIENZA SU MISURA</span><h2 id="rooms-title" class="h3 mt-2">Le stanze <span class="item-count">{{ $rooms->total() }}</span></h2><p class="small text-muted-custom mb-0">Aggiungi la foto e seleziona le sezioni da mostrare per ogni stanza.</p></div>@if (auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.rooms.create', $property) }}">+ Aggiungi una stanza</a>@endif</div>
        <div class="row g-4">
            @forelse ($rooms as $room)
                <div class="col-md-6 col-xl-4"><article class="sg-card room-card h-100">
                    <img class="room-card-photo" src="{{ $room->photo ? route('host.rooms.image', [$property, $room]) : asset('image/guest-welcome.webp') }}" alt="{{ $room->photo ? 'Foto di '.$room->name : '' }}" loading="lazy">
                    <div class="room-card-content">
                        <div class="d-flex flex-wrap gap-2 mb-3"><span class="badge badge-soft">{{ $room->archived_at ? 'Archiviata' : ($property->archived_at ? 'Struttura archiviata' : ($room->guide_enabled && $property->guide_enabled ? 'Guida abilitata' : 'Guida sospesa')) }}</span><span class="badge badge-soft">{{ $room->sections_count }} {{ $room->sections_count === 1 ? 'sezione visibile' : 'sezioni visibili' }}</span></div>
                        <h3 class="h4">{{ $room->name }}</h3>
                        <p class="small text-muted-custom">{{ Str::limit($room->description ?: 'Configura una guida su misura per gli ospiti di questa stanza.', 120) }}</p>
                        @if($room->sections_count === 0)<p class="small text-warning-emphasis">La guida non ha ancora sezioni: selezionale da “Configura stanza”.</p>@endif
                        <div class="d-grid gap-2">
                            @if (auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.rooms.edit', [$property, $room]) }}">Configura stanza</a>@endif
                            <a class="btn btn-outline-secondary" href="{{ route('host.stays.index', [$property, $room]) }}">Soggiorni e link ospite</a>
                        </div>
                        @if(auth()->user()->canWrite())<details class="advanced-settings mt-3"><summary>Stato e altre opzioni</summary><x-lifecycle :record="$room" :update-url="route('host.rooms.lifecycle', [$property, $room])" :destroy-url="route('host.rooms.destroy', [$property, $room])" /></details>@endif
                    </div>
                </article></div>
            @empty
                <div class="col-12"><div class="sg-card empty-state"><h3 class="h5">Ora prepara la prima stanza</h3><p class="text-muted-custom mb-0">Scegli un nome, aggiungi una foto e abilita le sezioni utili ai suoi ospiti.</p></div></div>
            @endforelse
        </div>
        <div class="mt-4">{{ $rooms->withQueryString()->links() }}</div>
    </section>
    {{-- Impostazioni meno frequenti separate dal percorso di configurazione. --}}
    @if(auth()->user()->canWrite())
        <details class="sg-card advanced-settings"><summary>Stato della struttura e opzioni avanzate</summary><p class="small text-muted-custom mt-3">Archiviare o sospendere la struttura interrompe le guide senza cancellare i contenuti. L'eliminazione definitiva rimuove anche stanze e soggiorni.</p><x-lifecycle :record="$property" :update-url="route('host.properties.lifecycle', $property)" :destroy-url="route('host.properties.destroy', $property)" /></details>
    @endif
</div>
@endsection
