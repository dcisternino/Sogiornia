@extends('layouts.app')
@section('title', 'Limiti traduzione · '.$host->name)

{{-- Eccezione del singolo host, distinta dalla disattivazione del suo account. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <a class="back-link" href="{{ route('admin.monitoring.index', ['host_id' => $host->id]) }}">← Monitoraggio host</a>
    <h1 class="mt-4">Traduzioni di {{ $host->name }}</h1><p class="text-muted-custom">{{ $host->email }}</p>
    <section class="sg-card mb-4"><h2 class="h5">Consumo del mese corrente</h2><x-translation-budget :used="$used" :limit="$host->translationLimit && !$host->translationLimit->uses_default_limit ? $host->translationLimit->monthly_character_limit : $settings->default_host_character_limit" /><p class="small text-muted-custom mt-3 mb-0">Il tetto globale si applica comunque. Modificare la soglia non azzera il consumo e non cambia l'accesso dell'host a strutture e soggiorni.</p></section>
    <form class="sg-card" method="POST" action="{{ route('admin.monitoring.hosts.update', $host) }}">@csrf @method('PATCH')
        <input type="hidden" name="is_enabled" value="0"><div class="form-check form-switch mb-4"><input class="form-check-input" type="checkbox" role="switch" id="host-translations-enabled" name="is_enabled" value="1" @checked(old('is_enabled', $host->translationLimit?->is_enabled ?? true))><label class="form-check-label" for="host-translations-enabled">Consenti traduzioni automatiche per questo host</label></div>
        <input type="hidden" name="uses_default_limit" value="0"><div class="form-check mb-4"><input class="form-check-input" type="checkbox" id="uses_default_limit" name="uses_default_limit" value="1" @checked(old('uses_default_limit', $host->translationLimit?->uses_default_limit ?? true))><label class="form-check-label" for="uses_default_limit">Usa il limite predefinito: {{ $settings->default_host_character_limit === null ? 'senza limite per host' : number_format($settings->default_host_character_limit, 0, ',', '.').' caratteri/mese' }}</label></div>
        <x-input name="monthly_character_limit" label="Limite mensile personalizzato in caratteri" type="number" min="0" max="100000000000" step="1" :value="$host->translationLimit?->monthly_character_limit" help="Compilalo solo se togli la spunta al limite predefinito. Vuoto significa nessun limite individuale; 0 blocca le nuove traduzioni. Il limite globale resta sempre valido." />
        <div class="form-actions"><button class="btn btn-primary" type="submit">Salva limiti host</button><a class="btn btn-outline-secondary" href="{{ route('admin.monitoring.index', ['host_id' => $host->id]) }}">Annulla</a></div>
    </form>
</div>
@endsection
