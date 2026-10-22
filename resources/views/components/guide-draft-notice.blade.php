@props(['property'])
{{-- Promemoria persistente: il salvataggio non modifica la guida degli ospiti. --}}
<div {{ $attributes->class(['sg-card', 'mb-4']) }} role="note">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3"><div><strong>Stai lavorando sulla bozza.</strong><p class="small mb-0">Dati, foto, sezioni e traduzioni saranno visibili agli ospiti dopo la pubblicazione.</p></div><a class="btn btn-outline-secondary" href="{{ $setupNavigation->url('host.guide.show', $property) }}">Rivedi e pubblica</a></div>
</div>
