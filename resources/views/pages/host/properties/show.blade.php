@extends('layouts.app')
@section('title', $property->name)

{{-- Struttura, stato delle guide e contenuti dell'host proprietario. --}}
@section('content')
<div class="container py-5">
    <a href="{{ route('host-home') }}">← Le mie strutture</a>
    <div class="page-heading mt-4">
        <div><span class="eyebrow">{{ $property->city }}</span><h1>{{ $property->name }}</h1><p class="text-muted-custom">{{ $property->address }}</p>
            <span class="badge badge-soft">{{ $property->archived_at ? 'Archiviata' : 'Attiva' }} · {{ $property->guide_enabled ? 'Guide abilitate' : 'Guide sospese' }}</span>
        </div>
        @if (auth()->user()->canWrite())<a class="btn btn-outline-secondary" href="{{ route('host.properties.edit', $property) }}">Modifica struttura</a>@endif
    </div>
    <x-lifecycle :record="$property" :update-url="route('host.properties.lifecycle', $property)" :destroy-url="route('host.properties.destroy', $property)" />
    <div class="onboarding-note my-4">Archiviare o sospendere struttura o stanza blocca temporaneamente le guide senza cancellare i contenuti. Disattivare l'account host, invece, lascia validi i soggiorni esistenti.</div>

    {{-- Catalogo delle sezioni informative riutilizzabili nelle stanze. --}}
    <section class="my-5" aria-labelledby="sections-title">
        <div class="page-heading compact"><h2 id="sections-title">Sezioni informative</h2>@if (auth()->user()->canWrite())<a class="btn btn-outline-secondary" href="{{ route('host.sections.create', $property) }}">+ Nuova sezione</a>@endif</div>
        <div class="sg-card">
            @forelse ($sections as $section)
                <div class="section-row">
                    <div><h3 class="h6 mb-1">{{ $section->title }}</h3><span class="small text-muted-custom">{{ $section->slug }}</span></div>
                    @if (auth()->user()->canWrite())
                        <div class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('host.sections.edit', [$property, $section]) }}">Modifica<span class="visually-hidden"> {{ $section->title }}</span></a>
                            <form method="POST" action="{{ route('host.sections.destroy', [$property, $section]) }}" data-confirm="Eliminare questa sezione da tutte le guide?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Elimina<span class="visually-hidden"> {{ $section->title }}</span></button></form>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted-custom mb-0">Nessuna sezione. Puoi iniziare dai modelli Wi-Fi, check-in, regole, contatti e territorio.</p>
            @endforelse
            {{ $sections->withQueryString()->links() }}
        </div>
    </section>

    {{-- Stanze, visibilità dei contenuti e accesso ai soggiorni. --}}
    <section aria-labelledby="rooms-title">
        <div class="page-heading compact"><h2 id="rooms-title">Le stanze</h2>@if (auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.rooms.create', $property) }}">+ Nuova stanza</a>@endif</div>
        <div class="row g-4">
            @forelse ($rooms as $room)
                <div class="col-lg-6"><article class="sg-card h-100">
                    <h3 class="h4">{{ $room->name }}</h3>
                    <p class="preserve-lines text-muted-custom">{{ $room->description }}</p>
                    <p class="small">{{ $room->sections_count }} {{ $room->sections_count === 1 ? 'sezione' : 'sezioni' }} · {{ $room->archived_at ? 'Archiviata' : 'Attiva' }} · {{ $room->guide_enabled ? 'Guida abilitata' : 'Guida sospesa' }}</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('host.stays.index', [$property, $room]) }}">Soggiorni e link</a>
                        @if (auth()->user()->canWrite())<a class="btn btn-outline-secondary btn-sm" href="{{ route('host.rooms.edit', [$property, $room]) }}">Modifica stanza e sezioni</a>@endif
                    </div>
                    <x-lifecycle :record="$room" :update-url="route('host.rooms.lifecycle', [$property, $room])" :destroy-url="route('host.rooms.destroy', [$property, $room])" />
                </article></div>
            @empty
                <div class="col-12"><div class="sg-card">Nessuna stanza presente.</div></div>
            @endforelse
        </div>
        <div class="mt-4">{{ $rooms->withQueryString()->links() }}</div>
    </section>
</div>
@endsection
