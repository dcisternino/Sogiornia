@extends('layouts.app')
@section('title', $block->exists ? 'Modifica contenuto' : 'Nuovo contenuto')

{{-- Editor del singolo contenuto, con campi pertinenti al tipo scelto. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <a class="back-link" href="{{ route('host.blocks.index', [$property, $section]) }}">← {{ $section->title }}</a>
    <h1 class="mt-4">{{ $block->exists ? 'Modifica contenuto' : 'Aggiungi contenuto' }}</h1>
    <p class="text-muted-custom">Scegli il tipo e compila i dati in italiano. Le traduzioni sono facoltative.</p>
    <form class="sg-card editor-form mt-4" method="POST" enctype="multipart/form-data" action="{{ $block->exists ? route('host.blocks.update', [$property, $section, $block]) : route('host.blocks.store', [$property, $section]) }}" data-block-editor>
        @csrf
        @if($block->exists) @method('PUT') @endif
        <div class="mb-3"><label class="form-label" for="type">Tipo di contenuto</label><select class="form-select @error('type') is-invalid @enderror" name="type" id="type" data-block-type required>
            @foreach(\App\Models\SectionBlock::TYPES as $value => $label)<option value="{{ $value }}" @selected(old('type', $block->type) === $value)>{{ $label }}</option>@endforeach
        </select>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <x-input name="title" label="Titolo in italiano" :value="$block->title" maxlength="255" :required="true" placeholder="Es. Guardia medica oppure Spiaggia di San Vito" />
        <x-textarea name="content" label="Descrizione in italiano" :value="$block->content" maxlength="20000" :rows="5" help="Spiega cosa offre il luogo o quando utilizzare il contatto. Testo semplice, senza HTML." />
        <x-input name="group_title" label="Titolo di gruppo (facoltativo)" :value="$block->group_title" maxlength="255" placeholder="Es. Nel centro storico" help="Il titolo appare sopra questo contenuto. Ripetilo nei blocchi consecutivi dello stesso gruppo: nella guida apparirà una sola volta." />
        <fieldset class="my-4" data-block-fields="contact place text"><legend class="h5">Contatto telefonico</legend><x-input name="phone" label="Numero da chiamare" type="tel" :value="$block->phone" maxlength="40" placeholder="+39 ..." help="Obbligatorio per il tipo Contatto. Il pulsante apre la chiamata dal telefono dell'ospite." /></fieldset>
        <fieldset class="my-4" data-block-fields="link place text"><legend class="h5">Pulsante con collegamento</legend><x-input name="link_url" label="Indirizzo web" type="url" :value="$block->link_url" maxlength="2048" placeholder="https://..." help="Obbligatorio per il tipo Collegamento. Puoi inserire la mappa, il sito o il link di prenotazione." /><x-input name="link_label" label="Testo del pulsante" :value="$block->link_label" maxlength="255" placeholder="Es. Apri la mappa" /></fieldset>
        <div data-block-fields="place"><x-input name="address" label="Indirizzo del luogo" :value="$block->address" maxlength="500" /></div>
        {{-- Foto private, mai esposte direttamente dalla cartella public. --}}
        <fieldset class="my-4"><legend class="h5">Foto (facoltativa)</legend>
            @if($block->image_path)<img class="section-image mb-3" src="{{ route('host.blocks.image', [$property, $section, $block]) }}" alt="{{ $block->image_alt ?: $block->title }}"><div class="form-check mb-3"><input class="form-check-input" id="remove_image" name="remove_image" value="1" type="checkbox" @checked(old('remove_image'))><label class="form-check-label" for="remove_image">Rimuovi la foto attuale</label></div>@endif
            <label class="form-label" for="image">Carica foto</label><input class="form-control @error('image') is-invalid @enderror" name="image" id="image" type="file" accept="image/jpeg,image/png,image/webp">@error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <p class="small text-muted-custom mt-2">JPEG, PNG o WebP, massimo 5 MB e 4000 × 4000 pixel.</p><x-input name="image_alt" label="Descrizione della foto" :value="$block->image_alt" maxlength="255" />
        </fieldset>
        <x-guest-content-translations :record="$block" type="block" />
        <input type="hidden" name="is_active" value="0"><div class="form-check my-4"><input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $block->is_active))><label class="form-check-label" for="is_active">Mostra questo contenuto nella guida</label></div>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Salva contenuto</button><a class="btn btn-outline-secondary" href="{{ route('host.blocks.index', [$property, $section]) }}">Annulla</a></div>
    </form>
</div>
@endsection
