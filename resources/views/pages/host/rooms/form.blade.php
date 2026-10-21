@extends('layouts.app')
@section('title', $room->exists ? 'Modifica stanza' : 'Nuova stanza')

{{-- Configurazione guidata della stanza, della copertina e dei contenuti visibili. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <a class="back-link" href="{{ route('host.rooms.index', $property) }}">← Stanze di {{ $property->name }}</a>
    <span class="eyebrow d-block mt-4">UNA GUIDA SU MISURA</span>
    <h1 class="mt-2">{{ $room->exists ? 'Modifica '.$room->name : 'Aggiungi una stanza' }}</h1>
    <p class="text-muted-custom">Dai un nome alla stanza, scegli una foto e abilita le informazioni che vedranno i suoi ospiti.</p>
    <form class="sg-card editor-form" method="POST" enctype="multipart/form-data" action="{{ $room->exists ? route('host.rooms.update', [$property, $room]) : route('host.rooms.store', $property) }}">
        @csrf
        @if ($room->exists) @method('PUT') @endif
        {{-- Identificazione della stanza e nota riservata all'host. --}}
        <fieldset class="editor-section">
            <legend><span class="editor-number">1</span> I dati della stanza</legend>
            <x-input name="name" label="Nome della stanza" :value="$room->name" maxlength="255" :required="true" placeholder="Es. Stanza Levante" help="Comparirà nella guida dei tuoi ospiti." />
            <x-textarea name="description" label="Nota interna sulla stanza" :value="$room->description" maxlength="3000" :rows="3" help="Solo per te: questa nota non viene mostrata agli ospiti." />
        </fieldset>
        {{-- Foto privata con anteprima locale prima del salvataggio. --}}
        <fieldset class="editor-section">
            <legend><span class="editor-number">2</span> La foto di benvenuto <small>facoltativa</small></legend>
            <p class="small text-muted-custom">Sostituisce l'illustrazione iniziale nel link del soggiorno. Consigliamo una foto orizzontale.</p>
            <img class="room-photo-preview mb-3" id="room-photo-preview" src="{{ $room->exists && $room->photo ? route('host.rooms.image', [$property, $room]) : asset('image/guest-welcome.webp') }}" alt="Anteprima della copertina della stanza">
            <label class="form-label" for="image">{{ $room->exists && $room->photo ? 'Sostituisci la foto' : 'Carica una foto' }}</label>
            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" class="form-control @error('image') is-invalid @enderror" data-image-preview="room-photo-preview" aria-describedby="image-feedback" @error('image') aria-invalid="true" @enderror>
            <div id="image-feedback" class="{{ $errors->has('image') ? 'invalid-feedback' : 'form-text' }}">@error('image'){{ $message }}@else JPEG, PNG o WebP, massimo 5 MB e 4000 × 4000 pixel. Dopo un errore seleziona nuovamente il file.@enderror</div>
            @if($room->exists && $room->photo)
                <div class="form-check mt-3"><input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1" @checked(old('remove_image'))><label class="form-check-label" for="remove_image">Rimuovi la foto e torna all'illustrazione Sogiornia</label></div>
                @error('remove_image')<p class="text-danger small mt-2">{{ $message }}</p>@enderror
            @endif
        </fieldset>
        {{-- Pubblicazione esplicita delle sezioni già create nella struttura. --}}
        <fieldset class="editor-section">
            <legend><span class="editor-number">3</span> Cosa vedranno gli ospiti?</legend>
            <p class="small text-muted-custom">Seleziona le sezioni da mostrare in questa stanza. Puoi scegliere contenuti diversi per ogni stanza.</p>
            <input type="hidden" name="sections_present" value="1">
            <div class="section-options">
                @forelse ($sections as $section)
                    <div class="form-check section-choice">
                        <input class="form-check-input" type="checkbox" name="sections[]" id="section-{{ $section->id }}" value="{{ $section->id }}" @checked($section->isShared() || in_array($section->id, (array) (old('sections_present') ? old('sections', []) : $selectedSections))) @disabled($section->isShared())>
                        <label class="form-check-label" for="section-{{ $section->id }}"><strong>{{ $section->title }}</strong>@if($section->isShared())<span class="badge badge-soft ms-2">Condivisa con tutte le stanze</span>@endif @unless($section->isActive())<span class="badge badge-soft">Disattivata</span>@endunless<span class="d-block small text-muted-custom">{{ Str::limit($section->content, 90) }}</span></label>
                    </div>
                @empty
                    <div class="onboarding-note"><strong>Non ci sono ancora sezioni.</strong><p class="mb-0 mt-1">Salva la stanza, crea i contenuti dalla struttura e torna qui per selezionarli.</p></div>
                @endforelse
            </div>
            <p class="small text-muted-custom mt-3 mb-0">Le sezioni condivise si applicano automaticamente. Quelle disattivate restano nascoste anche se selezionate. Gestisci questi interruttori dalla <a href="{{ route('host.sections.index', $property) }}">pagina sezioni</a>.</p>
            @error('sections')<p class="text-danger small">{{ $message }}</p>@enderror
            @error('sections.*')<p class="text-danger small">{{ $message }}</p>@enderror
        </fieldset>
        <div class="form-actions editor-actions"><button class="btn btn-primary" type="submit">{{ $room->exists ? 'Salva le modifiche' : 'Crea la stanza' }}</button><a class="btn btn-outline-secondary" href="{{ route('host.rooms.index', $property) }}">Annulla</a></div>
    </form>
</div>
@endsection
