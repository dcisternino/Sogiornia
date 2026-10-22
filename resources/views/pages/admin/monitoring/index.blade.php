@extends('layouts.app')
@section('title', 'Monitoraggio traduzioni')

{{-- Consumi registrati dall'applicazione, separati dalla fatturazione ufficiale Google. --}}
@section('content')
<div class="container py-5 workspace">
    <div class="page-heading"><div><span class="eyebrow">AREA AMMINISTRATORE</span><h1 class="mt-2">Consumi e limiti</h1><p class="text-muted-custom">Google Cloud Translation · storico attivo dal {{ $settings->created_at->timezone(config('translation.timezone'))->format('d/m/Y H:i') }}.</p></div></div>
    <form class="sg-card mb-4" method="GET" action="{{ route('admin.monitoring.index') }}">
        <div class="row g-3 align-items-end"><div class="col-md-3"><label class="form-label" for="monitor-month">Mese</label><input class="form-control" type="month" id="monitor-month" name="month" value="{{ $month }}" min="1900-01" max="2199-12" required></div>
            <div class="col-md-4"><label class="form-label" for="host_id">Host</label><select class="form-select" id="host_id" name="host_id"><option value="">Tutti gli host</option>@foreach($hostOptions as $hostOption)<option value="{{ $hostOption->id }}" @selected((string)($filters['host_id'] ?? '') === (string)$hostOption->id)>{{ $hostOption->name }} · {{ $hostOption->email }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label" for="usage-status">Esito nello storico</label><select class="form-select" id="usage-status" name="status"><option value="">Tutti gli esiti</option>@foreach(\App\Models\TranslationUsage::STATUSES as $code => $label)<option value="{{ $code }}" @selected(($filters['status'] ?? '') === $code)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-2"><button class="btn btn-primary" type="submit">Mostra</button> <a class="d-inline-block small mt-2" href="{{ route('admin.monitoring.index') }}">Azzera filtri</a></div>
        </div>
    </form>
    <p class="small text-muted-custom">I riepiloghi e la stima costi mostrano sempre il totale globale del mese selezionato. I filtri host ed esito restringono le tabelle sottostanti.</p>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3"><x-metric-card label="Caratteri impegnati" :value="number_format($used, 0, ',', '.')" hint="Conteggio prudenziale di richieste e prenotazioni" /></div>
        <div class="col-md-6 col-xl-3"><x-metric-card label="Residuo soglia globale attuale" :value="$remaining === null ? 'Senza limite' : number_format($remaining, 0, ',', '.')" hint="Il limite viene verificato prima di ogni chiamata" /></div>
        <div class="col-md-6 col-xl-3"><x-metric-card label="Richieste riuscite" :value="$totals->get('success')?->requests ?? 0" /></div>
        <div class="col-md-6 col-xl-3"><x-metric-card label="Fallite o incerte" :value="($totals->get('failed')?->requests ?? 0) + ($totals->get('uncertain')?->requests ?? 0)" /></div>
    </div>
    <div class="row g-4 mb-4">
        <section class="col-lg-6"><div class="sg-card h-100"><h2 class="h4">Budget globale</h2><x-translation-budget :used="$used" :limit="$settings->monthly_character_limit" />
            @if(!$settings->is_enabled)<p class="text-danger mt-3 mb-0">Le nuove traduzioni automatiche sono sospese per tutti gli host.</p>@endif
            <p class="small text-muted-custom mt-3 mb-0">Bloccate prima di Google: {{ $totals->get('blocked')?->requests ?? 0 }}. In corso o interrotte: {{ $totals->get('reserved')?->requests ?? 0 }}.</p>
            <p class="small text-muted-custom mt-2 mb-0">Il mese segue il fuso {{ config('translation.timezone') }}. Cambiando mese viene considerato un nuovo budget, senza cancellare lo storico. Le soglie visualizzate sono quelle attuali, anche nei mesi passati.</p>
        </div></section>
        <section class="col-lg-6"><div class="sg-card h-100"><h2 class="h4">Stima prudenziale Google</h2><p class="metric-value">{{ number_format($estimatedCost, 2, ',', '.') }} USD</p><p class="small text-muted-custom">Ipotesi di calcolo: {{ number_format(config('translation.free_characters'), 0, ',', '.') }} caratteri gratuiti/mese, poi {{ config('translation.usd_per_million') }} USD per milione di caratteri aggiuntivi, imposte escluse.</p>
            <p class="small text-muted-custom mb-0">È una stima sui soli dati di Sogiornia: il conteggio include anche prenotazioni in corso, errori e timeout, per prudenza. Non recupera i consumi precedenti all'attivazione né quelli esterni all'applicazione. Il periodo può differire da quello di fatturazione. Verifica sempre <a href="https://console.cloud.google.com/billing" target="_blank" rel="noopener noreferrer">fatturazione Google</a> e <a href="https://cloud.google.com/products/translate/pricing" target="_blank" rel="noopener noreferrer">tariffe ufficiali</a>.</p>
        </div></section>
    </div>
    {{-- Soglie valide subito, senza modificare i consumi registrati. --}}
    <section class="sg-card mb-4"><h2 class="h4">Impostazioni globali</h2>
        @if(blank(config('services.google_translation.key')))<p class="setup-tip">La chiave Google non è configurata sul server. I limiti sono pronti, ma le traduzioni automatiche restano indisponibili.</p>@endif
        <form method="POST" action="{{ route('admin.monitoring.settings') }}">@csrf @method('PATCH')
            <input type="hidden" name="is_enabled" value="0"><div class="form-check form-switch my-3"><input class="form-check-input" type="checkbox" role="switch" id="global-translations-enabled" name="is_enabled" value="1" @checked(old('is_enabled', $settings->is_enabled))><label class="form-check-label" for="global-translations-enabled">Consenti traduzioni automatiche</label></div>
            <div class="row"><div class="col-md-6"><x-input name="monthly_character_limit" label="Limite mensile globale in caratteri" type="number" min="0" max="100000000000" step="1" :value="$settings->monthly_character_limit" help="Condiviso da tutti gli host. Vuoto significa nessun limite globale; 0 blocca nuove traduzioni." /></div><div class="col-md-6"><x-input name="default_host_character_limit" label="Limite mensile predefinito per host" type="number" min="0" max="100000000000" step="1" :value="$settings->default_host_character_limit" help="Vale per host nuovi e host senza eccezioni. Vuoto significa nessun limite individuale." /></div></div>
            <p class="small text-muted-custom">I cambiamenti valgono subito e non azzerano il consumo. Guide e traduzioni manuali non vengono sospese. Alzare o togliere la soglia può permettere consumi a pagamento.</p><button class="btn btn-primary" type="submit">Salva impostazioni globali</button>
        </form>
    </section>
    {{-- Riepilogo per host con eccezioni amministrative e avvisi all'80% e al 100%. --}}
    <section class="sg-card mb-4"><h2 class="h4">Consumi per host</h2><p class="small text-muted-custom">Gli esiti non filtrano questa tabella. Le impostazioni del singolo host restano subordinate alla soglia globale e allo stato operativo dell'account.</p>
        <div class="table-responsive"><table class="table align-middle"><caption>Consumi del mese selezionato; limiti attualmente in vigore.</caption><thead><tr><th>Host</th><th>Consumo / soglia</th><th>Richieste</th><th>Traduzioni</th><th>Gestione</th></tr></thead><tbody>
            @forelse($hosts as $host)
                <tr><th scope="row"><a href="{{ route('admin.hosts.show', $host) }}">{{ $host->name }}</a><small class="d-block text-muted-custom">{{ $host->email }}</small></th><td><x-translation-budget :used="(int)$host->used_characters" :limit="$host->translationLimit && !$host->translationLimit->uses_default_limit ? $host->translationLimit->monthly_character_limit : $settings->default_host_character_limit" /></td><td class="small">{{ $host->successful_requests }} riuscite<br>{{ $host->failed_requests }} fallite/incerte<br>{{ $host->blocked_requests }} bloccate</td><td class="small">{{ $host->translationLimit && !$host->translationLimit->is_enabled ? 'Sospese per host' : 'Consentite per host' }}<br>{{ $host->translationLimit && !$host->translationLimit->uses_default_limit ? 'Soglia personalizzata' : 'Soglia predefinita' }}</td><td><a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.monitoring.hosts.edit', $host) }}">Gestisci limiti</a></td></tr>
            @empty<tr><td colspan="5">Nessun host da mostrare.</td></tr>@endforelse
        </tbody></table></div>{{ $hosts->links() }}
    </section>
    {{-- Storico privo di testi tradotti, token della guida o chiavi API. --}}
    <section class="sg-card"><h2 class="h4">Storico richieste</h2><p class="small text-muted-custom">Le richieste bloccate non partono verso Google e non impegnano budget. Quelle inviate mantengono il conteggio anche in caso di errore o esito incerto; non vengono liberate automaticamente. Una richiesta “in corso o interrotta” potrebbe essere stata interrotta dal server.</p>
        <div class="table-responsive"><table class="table align-middle"><caption>Metadati registrati da Sogiornia; nessun testo o credenziale viene salvato.</caption><thead><tr><th>Data</th><th>Host</th><th>Contenuto / lingua</th><th>Caratteri richiesti / budget</th><th>Esito</th><th>Durata</th></tr></thead><tbody>
            @forelse($logs as $usage)<tr><td class="small text-nowrap">{{ $usage->created_at->timezone(config('translation.timezone'))->format('d/m/Y H:i:s') }}</td><td>{{ $usage->user?->name ?? 'Account rimosso' }}</td><td class="small">{{ ['property' => 'Benvenuto', 'section' => 'Sezione', 'block' => 'Blocco'][$usage->resource_type] ?? 'Contenuto' }} · {{ strtoupper($usage->locale) }}</td><td>{{ number_format($usage->characters, 0, ',', '.') }} / {{ number_format($usage->budget_characters, 0, ',', '.') }}</td><td class="small">{{ \App\Models\TranslationUsage::STATUSES[$usage->status] ?? 'Sconosciuto' }}@if($usage->provider_status)<br>Google HTTP {{ $usage->provider_status }}@endif @if($usage->error_code)<br><span class="text-muted-custom">{{ ['global_disabled' => 'Sospensione globale', 'host_disabled' => 'Sospensione host', 'global_limit' => 'Soglia globale', 'host_limit' => 'Soglia host', 'connection_or_interruption' => 'Connessione o interruzione', 'invalid_result' => 'Risposta non utilizzabile', 'provider_rejected' => 'Richiesta non completata dal provider'][$usage->error_code] ?? 'Errore tecnico' }}</span>@endif</td><td>{{ $usage->duration_ms === null ? '—' : number_format($usage->duration_ms / 1000, 2, ',', '.').' s' }}</td></tr>
            @empty<tr><td colspan="6">Nessuna richiesta registrata con questi filtri.</td></tr>@endforelse
        </tbody></table></div>{{ $logs->links() }}
    </section>
    <p class="small text-muted-custom mt-4">Le metriche della VPS verranno collegate dopo il deployment: qui non vengono mostrate misurazioni del computer di sviluppo.</p>
</div>
@endsection
