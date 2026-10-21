@props(['record', 'updateUrl', 'destroyUrl'])

{{-- Comandi riutilizzabili con archiviazione reversibile e conferma esplicita del nome. --}}
@if (auth()->user()->canWrite())
<div class="lifecycle-controls mt-3">
    <div class="d-flex flex-wrap gap-2">
        <form method="POST" action="{{ $updateUrl }}">
            @csrf @method('PATCH')
            <input type="hidden" name="action" value="{{ $record->archived_at ? 'restore' : 'archive' }}">
            <button class="btn btn-sm btn-outline-secondary" type="submit">{{ $record->archived_at ? 'Ripristina' : 'Archivia' }}</button>
        </form>
        <form method="POST" action="{{ $updateUrl }}">
            @csrf @method('PATCH')
            <input type="hidden" name="action" value="{{ $record->guide_enabled ? 'suspend' : 'resume' }}">
            <button class="btn btn-sm btn-outline-secondary" type="submit">{{ $record->guide_enabled ? 'Sospendi guida' : 'Riattiva guida' }}</button>
        </form>
    </div>
    <details class="mt-3">
        <summary class="small text-danger">Elimina definitivamente</summary>
        <form class="mt-3" method="POST" action="{{ $destroyUrl }}">
            @csrf @method('DELETE')
            <label class="small d-block">Per eliminare anche i dati collegati digita esattamente <strong>{{ $record->name }}</strong>.
                <input class="form-control form-control-sm mt-2" name="confirmation" required autocomplete="off">
            </label>
            <button class="btn btn-sm btn-outline-danger mt-2" type="submit">Conferma eliminazione definitiva</button>
        </form>
    </details>
</div>
@endif
