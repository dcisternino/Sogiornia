@extends('layouts.app')
@section('title', 'Sezioni · '.$property->name)

{{-- Sezioni e visibilità: disattivazione globale e condivisione con tutte le stanze. --}}
@section('content')
<div class="container py-5 workspace">
    <x-setup-shortcut :property="$property" />
    <a class="back-link" href="{{ route('host.properties.index') }}">← Le mie strutture</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">{{ $property->name }}</span><h1 class="mt-2">Le sezioni</h1><p class="text-muted-custom mb-0">I contenuti della guida e le stanze che possono vederli.</p></div><div class="d-flex flex-wrap gap-2"><a class="btn btn-outline-secondary" href="{{ route('host.rooms.index', $property) }}">Gestisci stanze</a>@if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.sections.create', $property) }}">+ Aggiungi sezione</a>@endif</div></div>
    <p class="small text-muted-custom mb-4"><strong>Attiva</strong> pubblica la sezione nelle stanze abilitate. <strong>Condivisa</strong> la abilita in tutte le stanze di questa struttura, anche quelle nuove. Una sezione disattivata resta nascosta in ogni caso.</p>
    <div class="d-grid gap-3">
        @forelse($sections as $section)
            <article class="sg-card section-management-row">
                <div class="d-flex align-items-start gap-3 section-management-info"><span class="section-symbol"><x-guest-section-icon :slug="$section->slug" /></span><div><h2 class="h5 mb-2">{{ $section->title }}</h2><p class="small text-muted-custom mb-2">{{ $section->blocks_count }} contenuti · {{ Str::limit($section->content, 100) }}</p><span class="badge badge-soft">{{ !$section->isActive() ? 'Nascosta da tutte le guide' : ($section->isShared() ? 'Tutte le stanze' : 'Stanze selezionate') }}</span></div></div>
                <div class="section-toggles"><x-section-switch :property="$property" :section="$section" setting="is_active" label="Attiva" :checked="$section->isActive()" /><x-section-switch :property="$property" :section="$section" setting="is_shared" label="Condivisa" :checked="$section->isShared()" /></div>
                <div class="d-flex flex-wrap gap-2"><a class="btn btn-primary btn-sm" href="{{ route('host.blocks.index', [$property, $section]) }}">Contenuti</a>@if(auth()->user()->canWrite())<a class="btn btn-outline-secondary btn-sm" href="{{ route('host.sections.edit', [$property, $section]) }}">Modifica<span class="visually-hidden"> {{ $section->title }}</span></a><form method="POST" action="{{ route('host.sections.destroy', [$property, $section]) }}" data-confirm="Eliminare questa sezione da tutte le guide?">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" type="submit">Elimina<span class="visually-hidden"> {{ $section->title }}</span></button></form>@endif</div>
            </article>
        @empty
            <div class="sg-card empty-state"><h2 class="h4">Crea la prima sezione</h2><p class="text-muted-custom mb-0">Puoi partire dai modelli Wi-Fi, check-in, contatti e territorio.</p></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $sections->links() }}</div>
</div>
@endsection
