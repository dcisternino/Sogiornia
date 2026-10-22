@extends('layouts.app')
@section('title', 'Pubblica guida · '.$property->name)

{{-- Revisione esplicita della bozza e accesso allo storico immutabile. --}}
@section('content')
<div class="container py-5 workspace">
    <a class="back-link" href="{{ $setupNavigation->back('host.properties.index') }}">@if($setupNavigation->active())← Torna alla configurazione guidata @else ← Le mie strutture @endif</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">{{ $property->name }}</span><h1 class="mt-2">Rivedi e pubblica</h1><p class="text-muted-custom">{{ $published ? 'Gli ospiti vedono la versione '.$published->number.'. Le modifiche restano in bozza fino alla tua approvazione.' : 'La guida non è ancora pubblicata. Controlla i contenuti prima di condividerla.' }}</p></div></div>
    <div class="sg-card mb-4">
        <h2 class="h4">{{ count($changes) ? 'Modifiche da approvare' : 'La guida è aggiornata' }}</h2>
        @if(count($changes))
            <ul>@foreach($changes as $change)<li>{{ $change }}</li>@endforeach</ul>
        @else<p class="mb-0">Non ci sono differenze rispetto alla versione pubblicata.</p>@endif
        <p class="small text-muted-custom mt-3 mb-0">La pubblicazione riguarda l’intera struttura: dati, stanze, sezioni, contenuti, foto e traduzioni. Sospensione, archiviazione, eliminazione di stanze o struttura e revoca dei soggiorni hanno effetto immediato.</p>
    </div>
    {{-- Anteprime private per verificare la visibilità delle singole stanze. --}}
    <div class="sg-card mb-4"><h2 class="h4">Controlla la bozza per stanza</h2><div class="d-flex flex-wrap gap-2">
        @forelse($draft['rooms'] as $room)<a class="btn btn-outline-secondary" href="{{ $setupNavigation->url('host.preview.index', [$property, $room['id']]) }}">Anteprima · {{ $room['name'] }}</a>@empty<p class="mb-0">Puoi pubblicare i contenuti anche prima di aggiungere le stanze.</p>@endforelse
    </div></div>
    <details class="sg-card mb-4"><summary class="h5 mb-0">Tutti i contenuti della bozza</summary><div class="mt-4"><x-guide-content :payload="$draft" /></div></details>
    @if($published)<details class="sg-card mb-4"><summary class="h5 mb-0">Confronta con la versione pubblicata {{ $published->number }}</summary><div class="mt-4"><x-guide-content :payload="$published->payload" /></div></details>@endif
    {{-- Approvazione vincolata alla revisione letta, per evitare sovrascritture concorrenti. --}}
    @if(auth()->user()->canWrite() && count($changes))
    <form method="post" action="{{ $setupNavigation->url('host.guide.publish', $property) }}" class="sg-card mb-4">
        @csrf <x-onboarding-context />
        <input type="hidden" name="revision" value="{{ $workspace->revision }}">
        <input type="hidden" name="base_version" value="{{ $published?->id }}">
        <div class="form-check mb-3"><input class="form-check-input" id="approve-guide" name="approval" type="checkbox" value="1" required><label class="form-check-label" for="approve-guide">Ho controllato e approvo tutti i contenuti di questa bozza.</label></div>
        <button class="btn btn-primary">Pubblica la guida</button>
    </form>
    @endif
    <div class="sg-card"><h2 class="h4">Storico pubblicazioni</h2>
        @forelse($versions as $version)<div class="d-flex flex-wrap justify-content-between gap-2 border-bottom py-3"><div>Versione {{ $version->number }} · {{ $version->published_at->timezone('Europe/Rome')->format('d/m/Y H:i') }}<br><span class="small text-muted-custom">{{ $version->publisher?->name ?? 'Versione iniziale conservata' }}</span></div><a href="{{ $setupNavigation->url('host.guide.version', [$property, $version]) }}">Consulta versione</a></div>@empty<p>Nessuna pubblicazione.</p>@endforelse
        <div class="mt-3">{{ $versions->links() }}</div>
    </div>
</div>
@endsection
