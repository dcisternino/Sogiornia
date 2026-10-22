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
    {{-- Quattro requisiti necessari; Word facoltativo e invio esterno non falsano la percentuale. --}}
    <section class="sg-card mb-4"><h2 class="h5">{{ $completed }} di {{ $total }} requisiti di preparazione completati</h2><div class="progress" role="progressbar" aria-label="Preparazione della guida" aria-valuenow="{{ $completed }}" aria-valuemin="0" aria-valuemax="{{ $total }}"><div class="progress-bar" style="width: {{ $completed * 100 / $total }}%"></div></div><p class="small text-muted-custom mt-3 mb-0">@if($room)Stai configurando {{ $property->name }} · {{ $room->name }}.@else Scegli o crea una struttura e una stanza per preparare la guida.@endif Il modello Word è facoltativo. La condivisione tramite WhatsApp o email non viene registrata come invio.</p></section>
    @if($property && $room && !$guideEnabled)<div class="setup-tip mb-4"><strong>La guida è sospesa.</strong><p class="mb-0">Puoi preparare i contenuti e guardarli in anteprima. Prima di condividere, riattiva la guida dalla <a href="{{ route('host.rooms.index', [$property, 'from' => 'setup', 'setup_room_id' => $room->id]) }}">gestione stanze e struttura</a>.</p></div>@endif
    @if($hasChanges && $hasPublishedRoom)<div class="setup-tip mb-4"><strong>Ci sono modifiche in bozza.</strong><p class="mb-0">Gli ospiti continuano a vedere l’ultima versione pubblicata. Rivedi e pubblica dal passaggio 4 per aggiornarla.</p></div>@endif
    <ol class="list-unstyled d-grid gap-3">
        @foreach($steps as $step)
            {{-- Ogni passaggio espone le azioni disponibili senza bloccare il percorso manuale. --}}
            <li class="sg-card setup-step"><div><span class="eyebrow">Passaggio {{ $loop->iteration }} · {{ $step['status'] ?? ($step['complete'] ? 'Completato' : 'Da completare') }}</span><h2 class="h4 mt-2">{{ $step['title'] }}</h2><p class="text-muted-custom mb-0">{{ $step['description'] }}</p></div><div class="d-flex flex-wrap gap-2">
                @foreach($step['actions'] as $action)
                    @if(!$action['write'] || auth()->user()->canWrite())<a class="btn {{ $loop->first && !$step['complete'] ? 'btn-primary' : 'btn-outline-secondary' }}" href="{{ $action['url'] }}">{{ $action['label'] }}</a>@endif
                @endforeach
                @if(empty($step['actions']))<span class="small text-muted-custom">{{ $loop->last ? 'Servono contenuti pubblicati, una guida abilitata e un soggiorno non revocato o terminato.' : 'Crea prima la struttura'.($loop->iteration > 4 ? ' e una stanza.' : '.') }}</span>@endif
            </div></li>
        @endforeach
    </ol>
</div>
@endsection
