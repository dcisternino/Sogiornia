@props(['activeProperties', 'rooms', 'sections'])
{{-- Riepilogo dell'host, mostrato esclusivamente nell'elenco delle strutture. --}}
<div {{ $attributes->class(['setup-steps property-summary']) }} aria-label="Riepilogo delle strutture">
    <div class="setup-step"><span class="summary-number">{{ $activeProperties }}</span><div><strong>Strutture attive</strong><small>Strutture non archiviate</small></div></div>
    <div class="setup-step"><span class="summary-number">{{ $rooms }}</span><div><strong>Stanze</strong><small>In tutte le tue strutture</small></div></div>
    <div class="setup-step"><span class="summary-number">{{ $sections }}</span><div><strong>Sezioni</strong><small>Contenuti delle tue guide</small></div></div>
</div>
