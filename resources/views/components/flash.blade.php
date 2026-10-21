{{-- Avviso persistente sullo stato account e notifiche temporanee dei moduli. --}}
@if (auth()->check() && auth()->user()->isHost() && ! auth()->user()->canWrite())
    <div class="container pt-4"><div class="alert alert-warning" role="status">Il tuo account è in sola lettura: puoi consultare i dati e i soggiorni esistenti, ma non effettuare modifiche o creare nuovi soggiorni. Contatta l'amministratore per la riattivazione.</div></div>
@endif
<div class="notice-stack" aria-live="polite" aria-atomic="false">
    @if (session('status'))
        <div class="app-notice notice-success" role="status" data-auto-dismiss>
            <div><strong>Operazione completata</strong><p class="mb-0">{{ session('status') }}</p></div>
            <button class="btn-close" type="button" data-dismiss-notice aria-label="Chiudi messaggio di conferma"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="app-notice notice-error" role="alert" data-auto-dismiss>
            <div><strong>Le modifiche non sono state salvate</strong><p class="mb-1">Correggi i campi indicati e riprova.</p><ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            <button class="btn-close" type="button" data-dismiss-notice aria-label="Chiudi riepilogo errori"></button>
        </div>
    @endif
</div>
