@props(['label', 'value', 'href' => null, 'hint' => null])
{{-- Indicatore riepilogativo condiviso tra le dashboard. --}}
<div {{ $attributes->class(['sg-card metric-card']) }}>
    <span class="text-muted-custom">{{ $label }}</span>
    <strong class="metric-value">{{ $value }}</strong>
    @if($hint)<small class="text-muted-custom">{{ $hint }}</small>@endif
    @if($href)<a class="stretched-link" href="{{ $href }}">Visualizza <span aria-hidden="true">↗</span><span class="visually-hidden">: {{ $label }}</span></a>@endif
</div>
