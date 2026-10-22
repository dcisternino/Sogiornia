@extends('layouts.app')
@section('title', 'Configura la tua guida')

{{-- Percorso guidato che riutilizza i moduli esistenti e verifica i contenuti realmente visibili. --}}
@section('content')
<div class="container py-5 workspace">
    <a class="back-link" href="{{ route('host-home') }}">← Dashboard</a>
    <div class="page-heading mt-4"><div><span class="eyebrow">Dalla struttura alla prima guida</span><h1 class="mt-2">Un passo alla volta.</h1><p class="text-muted-custom">Segui il percorso oppure riprendi da dove eri rimasto. I progressi seguono i dati che hai già salvato.</p></div></div>
    @if($properties->isNotEmpty())
        <form class="sg-card mb-4" method="GET" action="{{ route('host.setup.index') }}">
            <div class="row g-3 align-items-end"><div class="col-md-5"><label class="form-label" for="setup-property">Struttura da configurare</label><select class="form-select" id="setup-property" name="property_id" data-setup-property>
                @foreach($properties as $item)<option value="{{ $item->id }}" @selected($property?->id === $item->id)>{{ $item->name }}</option>@endforeach
            </select></div><div class="col-md-5"><label class="form-label" for="setup-room">Stanza</label><select class="form-select" id="setup-room" name="room_id"><option value="">Prima stanza disponibile</option>@foreach($rooms as $item)<option value="{{ $item->id }}" @selected($room?->id === $item->id)>{{ $item->name }}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-outline-secondary w-100" type="submit">Mostra</button></div></div>
            <noscript><p class="small mt-2">Quando cambi struttura scegli “Prima stanza disponibile”, poi premi Mostra.</p></noscript>
        </form>
    @endif
    <section class="sg-card mb-4"><h2 class="h5">{{ $completed }} di {{ count($steps) }} passaggi di preparazione completati</h2><div class="progress" role="progressbar" aria-label="Preparazione della guida" aria-valuenow="{{ $completed }}" aria-valuemin="0" aria-valuemax="{{ count($steps) }}"><div class="progress-bar" style="width: {{ $completed * 100 / count($steps) }}%"></div></div><p class="small text-muted-custom mt-3 mb-0">@if($room)Stai configurando {{ $property->name }} · {{ $room->name }}.@else Scegli o crea una struttura e una stanza per preparare la guida.@endif Le strutture e le stanze archiviate non sono incluse.</p></section>
    @if($property && $room && !$guideEnabled)<div class="setup-tip mb-4"><strong>La guida è sospesa.</strong><p class="mb-0">Puoi preparare i contenuti e guardarli in anteprima. Prima di condividere, riattiva la guida dalla <a href="{{ route('host.rooms.index', $property) }}">gestione stanze e struttura</a>.</p></div>@endif
    <ol class="list-unstyled d-grid gap-3">
        @foreach($steps as $step)
            <li class="sg-card setup-step"><div><span class="eyebrow">Passaggio {{ $loop->iteration }} · {{ $step['complete'] ? 'Completato' : 'Da completare' }}</span><h2 class="h4 mt-2">{{ $step['title'] }}</h2><p class="text-muted-custom mb-0">{{ $step['description'] }}</p></div><div class="d-flex flex-wrap gap-2">
                @if($step['url'] && auth()->user()->canWrite())<a class="btn {{ $step['complete'] ? 'btn-outline-secondary' : 'btn-primary' }}" href="{{ $step['url'] }}">{{ $step['action'] }}</a>@elseif(!$step['url'])<span class="small text-muted-custom">Completa prima i passaggi precedenti.</span>@endif
                @if($loop->iteration === 3 && $room)
                    @if(auth()->user()->canWrite())<a class="btn btn-outline-secondary" href="{{ route('host.rooms.edit', [$property, $room]) }}">Scegli sezioni della stanza</a>@endif
                    <a class="btn btn-outline-secondary" href="{{ route('host.preview.index', [$property, $room]) }}">Anteprima guida ↗</a>
                @endif
            </div></li>
        @endforeach
        <li class="sg-card setup-step"><div><span class="eyebrow">Passaggio 5</span><h2 class="h4 mt-2">Condividi la guida</h2><p class="text-muted-custom mb-0">Scegli la lingua, controlla il messaggio e apri WhatsApp o email. La condivisione non viene registrata come invio avvenuto.</p></div>
            @if($stay && $guideEnabled && $hasContent && $hasPublishedRoom)<a class="btn btn-primary" href="{{ route('host.stays.share', [$property, $room, $stay]) }}">Prepara messaggio</a>@else<span class="small text-muted-custom">Servono una guida pubblicata, contenuti visibili, un soggiorno non revocato e non terminato e una guida abilitata.</span>@endif
        </li>
    </ol>
</div>
@endsection
