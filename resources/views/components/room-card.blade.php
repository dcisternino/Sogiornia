@props(['property', 'room', 'showProperty' => false])
{{-- Scheda stanza condivisa tra elenco generale ed elenco della struttura. --}}
<article class="sg-card room-card h-100">
                <img class="room-card-photo" src="{{ $room->photo ? route('host.rooms.image', [$property, $room]) : asset('image/guest-welcome.webp') }}" alt="{{ $room->photo ? 'Foto di '.$room->name : '' }}" loading="lazy">
                <div class="room-card-content">
                    <span class="badge badge-soft mb-3">{{ $room->archived_at ? 'Archiviata' : ($property->archived_at ? 'Struttura archiviata' : ($room->guide_enabled && $property->guide_enabled ? 'Guida abilitata' : 'Guida sospesa')) }}</span>
                    @if($showProperty)<p class="small text-muted-custom mb-2">{{ $property->name }}</p>@endif<h2 class="h4">{{ $room->name }}</h2><p class="text-muted-custom small">{{ Str::limit($room->description ?: 'Foto e sezioni personalizzabili per questa stanza.', 130) }}</p>
                    <div class="d-grid gap-2">@if(auth()->user()->canWrite())<a class="btn btn-primary" href="{{ route('host.rooms.edit', [$property, $room]) }}">Configura stanza<span class="visually-hidden"> {{ $room->name }}</span></a>@endif<a class="btn btn-outline-secondary" href="{{ route('host.stays.index', [$property, $room]) }}">Soggiorni e link ospite</a><a class="btn btn-outline-secondary" href="{{ route('host.preview.index', [$property, $room]) }}">Anteprima guida<span class="visually-hidden"> di {{ $room->name }}</span></a></div>
                    @if(auth()->user()->canWrite())<details class="advanced-settings mt-3"><summary>Stato e altre opzioni</summary><x-lifecycle :record="$room" :update-url="route('host.rooms.lifecycle', [$property, $room])" :destroy-url="route('host.rooms.destroy', [$property, $room])" /></details>@endif
                </div>
            </article>
