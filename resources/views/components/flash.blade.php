{{-- Messaggi di conferma ed errori dei moduli. --}}
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
