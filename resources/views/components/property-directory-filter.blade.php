@props(['properties', 'selectedProperty', 'indexRoute', 'createRoute', 'createLabel'])
{{-- Filtro comune agli elenchi generali e scelta esplicita della struttura per le nuove risorse. --}}
<div {{ $attributes->class(['sg-card mb-4 d-flex flex-wrap justify-content-between align-items-end gap-3']) }}>
    <form method="GET" action="{{ route($indexRoute) }}" class="d-flex flex-wrap align-items-end gap-2">
        <div><label class="form-label" for="directory-property">Struttura</label><select class="form-select" name="property_id" id="directory-property"><option value="">Tutte le strutture</option>@foreach($properties as $property)<option value="{{ $property->id }}" @selected((string)$selectedProperty === (string)$property->id)>{{ $property->name }}{{ $property->archived_at ? ' · Archiviata' : '' }}</option>@endforeach</select></div>
        <button class="btn btn-outline-secondary" type="submit">Mostra</button>@if($selectedProperty)<a class="btn btn-outline-secondary" href="{{ route($indexRoute) }}">Azzera</a>@endif
    </form>
    @if(auth()->user()->canWrite() && $properties->isNotEmpty())
        @if($selectedProperty)<a class="btn btn-primary" href="{{ route($createRoute, $selectedProperty) }}">+ {{ $createLabel }}</a>@else
            <div class="dropdown"><button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">+ {{ $createLabel }}</button><ul class="dropdown-menu dropdown-menu-end directory-create-menu">@foreach($properties as $property)<li><a class="dropdown-item" href="{{ route($createRoute, $property) }}">{{ $property->name }}</a></li>@endforeach</ul></div>
            <noscript><div class="d-flex flex-wrap gap-2">@foreach($properties as $property)<a href="{{ route($createRoute, $property) }}">{{ $createLabel }} · {{ $property->name }}</a>@endforeach</div></noscript>
        @endif
    @endif
</div>
