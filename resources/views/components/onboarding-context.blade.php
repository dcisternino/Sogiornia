{{-- Mantiene il percorso guidato nel salvataggio e dopo gli errori dei moduli. --}}
@if(($setupNavigation ?? null)?->active())
    @foreach($setupNavigation->parameters() as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach
@endif
