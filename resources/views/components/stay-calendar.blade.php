@props(['calendar', 'month', 'filters', 'period', 'canCreate'])
{{-- Calendario per notti, con dettagli delle prenotazioni e creazione rapida senza JavaScript. --}}
<section {{ $attributes->class(['sg-card']) }} aria-labelledby="calendar-heading">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <h2 class="h4 mb-0 text-capitalize" id="calendar-heading">{{ $calendar['label'] }}</h2>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($calendar['previous'])<a class="btn btn-sm btn-outline-secondary" aria-label="Mese precedente" href="{{ route('host.calendar.index', [...$filters, 'period' => $period, 'month' => $calendar['previous']]) }}">←</a>@endif
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('host.calendar.index', [...$filters, 'period' => $period, 'month' => now('Europe/Rome')->format('Y-m')]) }}">Questo mese</a>
            @if($calendar['next'])<a class="btn btn-sm btn-outline-secondary" aria-label="Mese successivo" href="{{ route('host.calendar.index', [...$filters, 'period' => $period, 'month' => $calendar['next']]) }}">→</a>@endif
            <form class="d-flex gap-2" method="GET" action="{{ route('host.calendar.index') }}">
                @foreach([...$filters, 'period' => $period] as $name => $value)@if(filled($value))<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endif @endforeach
                <label class="visually-hidden" for="calendar-month">Mese da visualizzare</label><input class="form-control form-control-sm" type="month" id="calendar-month" name="month" value="{{ $month }}" min="1900-01" max="2199-12" required><button class="btn btn-sm btn-outline-secondary" type="submit">Vai</button>
            </form>
        </div>
    </div>
    <p class="small text-muted-custom">Le celle colorate indicano notti prenotate. Il giorno di partenza è escluso e permette un nuovo arrivo. Revocare il link ospite non libera la stanza.</p>
    <div class="calendar-scroll" tabindex="0" role="region" aria-label="Calendario mensile, scorri orizzontalmente da telefono">
        <table class="occupancy-calendar">
            <caption class="visually-hidden">Notti prenotate nel mese di {{ $calendar['label'] }}, orari Europe/Rome.</caption>
            <thead><tr>@foreach(['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'] as $weekday)<th scope="col">{{ $weekday }}</th>@endforeach</tr></thead>
            <tbody>
                @foreach(array_chunk($calendar['days'], 7) as $week)
                    <tr>@foreach($week as $day)
                        <td class="{{ !$day['inMonth'] ? 'calendar-outside' : '' }} {{ $day['occupiedRooms'] ? 'calendar-occupied' : '' }} {{ $day['today'] ? 'calendar-today' : '' }}" data-calendar-date="{{ $day['date']->format('Y-m-d') }}" data-occupied-rooms="{{ $day['occupiedRooms'] }}">
                            <div class="calendar-day-heading"><time datetime="{{ $day['date']->format('Y-m-d') }}">{{ $day['date']->day }}</time>
                                @if($canCreate)<a class="calendar-add" aria-label="Nuovo soggiorno con arrivo il {{ $day['date']->format('d/m/Y') }}" href="{{ route('host.bookings.create', array_filter(['arrival_date' => $day['date']->format('Y-m-d'), 'departure_date' => $day['date']->addDay()->format('Y-m-d'), 'property_id' => $filters['property_id'] ?? null, 'room_id' => $filters['room_id'] ?? null])) }}">+</a>@endif
                            </div>
                            @if($day['occupiedRooms'])
                                <details class="calendar-day-details"><summary>{{ $day['occupiedRooms'] }} {{ $day['occupiedRooms'] === 1 ? 'stanza' : 'stanze' }}</summary>
                                    <ul class="list-unstyled mt-2 mb-0">@foreach($day['stays'] as $booking)<li class="mb-2"><a class="calendar-booking" href="{{ route('host.stays.index', [$booking->room->property_id, $booking->room_id, 'stay_id' => $booking->id]) }}">{{ $booking->reference }}<small>{{ $booking->room->property->name }} · {{ $booking->room->name }}</small></a></li>@endforeach</ul>
                                </details>
                            @else<span class="calendar-free">Nessuna notte prenotata</span>@endif
                        </td>
                    @endforeach</tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="small text-muted-custom mt-3 mb-0">@if($canCreate)Premi + sul giorno di arrivo per trovare la data già compilata.@elseif(auth()->user()->canWrite())Per aggiungere soggiorni dalla selezione corrente serve almeno una stanza non archiviata in una struttura non archiviata.@endif Apri il numero di stanze per consultare i soggiorni.</p>
</section>
