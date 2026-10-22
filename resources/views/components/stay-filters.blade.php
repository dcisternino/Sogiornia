@props(['properties', 'filters', 'period', 'month'])
{{-- Filtri combinabili, applicati soltanto ai dati dell'host autenticato. --}}
<form {{ $attributes->class(['stay-filters mb-4']) }} method="GET" action="{{ route('host.calendar.index') }}">
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="period" value="{{ $period }}">
    <div class="row g-3 align-items-end">
        <div class="col-md-6 col-xl-3"><label class="form-label" for="stay-filter-property">Struttura</label><select class="form-select" id="stay-filter-property" name="property_id" data-filter-rooms="stay-filter-room"><option value="">Tutte le strutture</option>@foreach($properties as $property)<option value="{{ $property->id }}" @selected((string)($filters['property_id'] ?? '') === (string)$property->id)>{{ $property->name }}</option>@endforeach</select></div>
        <div class="col-md-6 col-xl-3"><label class="form-label" for="stay-filter-room">Stanza</label><select class="form-select" id="stay-filter-room" name="room_id"><option value="">Tutte le stanze</option>@foreach($properties as $property)@foreach($property->rooms as $room)<option value="{{ $room->id }}" data-property-id="{{ $property->id }}" @selected((string)($filters['room_id'] ?? '') === (string)$room->id)>{{ $property->name }} · {{ $room->name }}</option>@endforeach @endforeach</select></div>
        <div class="col-sm-6 col-xl-3"><label class="form-label" for="from_date">Presenti dal</label><input class="form-control" type="date" id="from_date" name="from_date" value="{{ $filters['from_date'] ?? '' }}"></div>
        <div class="col-sm-6 col-xl-3"><label class="form-label" for="to_date">Al giorno</label><input class="form-control" type="date" id="to_date" name="to_date" value="{{ $filters['to_date'] ?? '' }}"></div>
    </div>
    <div class="d-flex gap-2 mt-3"><button class="btn btn-primary btn-sm" type="submit">Filtra soggiorni</button><a class="btn btn-outline-secondary btn-sm" href="{{ route('host.calendar.index', ['period' => $period, 'month' => $month]) }}">Azzera filtri</a></div>
    <p class="small text-muted-custom mt-2 mb-0">Struttura e stanza filtrano anche il calendario mensile. Le date filtrano l'elenco e i riepiloghi, includendo i soggiorni presenti anche solo per una parte del periodo indicato. Per cercare in qualsiasi data scegli “Tutti”.</p>
</form>
