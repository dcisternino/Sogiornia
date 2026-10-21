{{-- Messaggi di conferma ed errori dei moduli. --}}
@if (auth()->check() && auth()->user()->isHost() && ! auth()->user()->canWrite())
    <div class="container pt-4"><div class="alert alert-warning" role="status">Il tuo account è in sola lettura: puoi consultare i dati e i soggiorni esistenti, ma non effettuare modifiche o creare nuovi soggiorni. Contatta l'amministratore per la riattivazione.</div></div>
@endif
@if (session('status'))
    <div class="container pt-4"><div class="alert alert-success" role="status">{{ session('status') }}</div></div>
@endif
@if ($errors->any())
    <div class="container pt-4">
        <div class="alert alert-danger" role="alert">
            <strong>Controlla i dati inseriti.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
