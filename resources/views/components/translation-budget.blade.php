@props(['used', 'limit'])
{{-- Indicatore accessibile delle soglie: zero blocca, il valore vuoto non impone un tetto. --}}
@php
    $percent = $limit === null ? 0 : ($limit > 0 ? min(100, $used * 100 / $limit) : 100);
@endphp
<div {{ $attributes }}>
    <p class="small mb-2">{{ number_format($used, 0, ',', '.') }} / {{ $limit === null ? 'senza limite' : number_format($limit, 0, ',', '.') }} caratteri</p>
    @if($limit !== null)
        <div class="progress" role="progressbar" aria-label="Consumo del budget di traduzione" aria-valuenow="{{ round($percent) }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar {{ $percent >= 100 ? 'bg-danger' : ($percent >= 80 ? 'bg-warning' : '') }}" style="width: {{ $percent }}%"></div></div>
        @if($percent >= 100)<p class="small text-danger mt-2 mb-0">Limite raggiunto: nuove traduzioni bloccate.</p>@elseif($percent >= 80)<p class="small text-warning-emphasis mt-2 mb-0">Attenzione: raggiunto almeno l'80% della soglia.</p>@endif
    @endif
</div>
