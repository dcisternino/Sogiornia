@props(['property' => null, 'current' => 'property'])
{{-- Percorso guidato: dati della struttura, contenuti condivisi e configurazione delle stanze. --}}
<nav {{ $attributes->class(['setup-steps']) }} aria-label="Configurazione della guida">
    @foreach([
        'property' => ['number' => '01', 'label' => 'La struttura', 'help' => 'Nome, posizione e benvenuto', 'href' => $property?->exists ? route('host.properties.edit', $property) : route('host.properties.create')],
        'sections' => ['number' => '02', 'label' => 'Le sezioni', 'help' => 'Le informazioni da condividere', 'href' => $property?->exists ? route('host.properties.show', $property).'#sezioni' : null],
        'rooms' => ['number' => '03', 'label' => 'Le stanze', 'help' => 'Foto e sezioni visibili per stanza', 'href' => $property?->exists ? route('host.properties.show', $property).'#stanze' : null],
    ] as $key => $step)
        <div class="setup-step {{ $current === $key ? 'is-current' : '' }}">
            <span class="setup-number" aria-hidden="true">{{ $step['number'] }}</span>
            <div>
                @if($step['href'] && auth()->user()->canWrite())<a href="{{ $step['href'] }}" @if($current === $key) aria-current="step" @endif>{{ $step['label'] }}</a>@else<strong>{{ $step['label'] }}</strong>@endif
                <small>{{ $step['help'] }}</small>
            </div>
        </div>
    @endforeach
</nav>
