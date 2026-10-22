@props(['record', 'type'])
{{-- Traduzioni facoltative; ogni campo vuoto mantiene il corrispondente testo italiano. --}}
@php
    $fields = $type === 'property'
        ? ['welcome_message' => ['label' => 'Messaggio di benvenuto', 'max' => 3000, 'rows' => 4]]
        : ['title' => ['label' => 'Titolo', 'max' => 255], 'content' => ['label' => 'Testo della sezione', 'max' => 20000, 'rows' => 7], 'image_alt' => ['label' => 'Descrizione immagine', 'max' => 255], 'link_label' => ['label' => 'Testo del collegamento', 'max' => 255]];
    if ($type === 'section') {
        unset($fields['image_alt'], $fields['link_label']);
        $fields['content']['label'] = 'Introduzione della sezione';
    }
    if ($type === 'block') {
        $fields['content']['label'] = 'Descrizione';
        $fields['group_title'] = ['label' => 'Titolo di gruppo', 'max' => 255];
    }
@endphp
<fieldset {{ $attributes->class(['editor-section mt-4']) }} data-translation-editor data-translation-type="{{ $type }}" data-translation-url="{{ route('host.translations.store') }}">
    <legend>Traduzioni per gli ospiti <small>facoltative</small></legend>
    <p class="small text-muted-custom">I campi principali sono in italiano. Compila le altre lingue quando disponibili: per ogni campo vuoto mostreremo il testo italiano. Per rimuovere una traduzione svuota i relativi campi e salva.</p>
    <p class="small text-muted-custom">Con “Traduci” i testi italiani vengono inviati a Google Cloud Translation. La bozza viene inserita nel modulo: controllala e premi Salva per pubblicarla. @if(blank(config('services.google_translation.key')))<strong>Il servizio attende la configurazione della chiave Google da parte dell'amministratore.</strong>@endif</p>
    <noscript><p>Attiva JavaScript per la traduzione assistita. L'inserimento manuale resta disponibile.</p></noscript>
    @foreach(['en' => 'Inglese', 'es' => 'Spagnolo', 'de' => 'Tedesco'] as $locale => $language)
        @php
            $translation = $record->exists ? $record->translations->firstWhere('locale', $locale) : null;
        @endphp
        <details class="translation-editor mb-3" @if($errors->has('translations.'.$locale.'.*')) open @endif>
            <summary>{{ $language }} · {{ strtoupper($locale) }} @if($translation)<span class="small text-muted-custom">— traduzione presente</span>@endif</summary>
            <div class="pt-3">
                {{-- La sostituzione di traduzioni esistenti richiede una scelta esplicita. --}}
                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-translate-locale="{{ $locale }}" @disabled(blank(config('services.google_translation.key')))>Traduci in {{ strtolower($language) }}</button>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="replace-{{ $type }}-{{ $locale }}" data-translation-replace="{{ $locale }}"><label class="form-check-label small" for="replace-{{ $type }}-{{ $locale }}">Sostituisci anche i testi già compilati</label></div>
                </div>
                <p class="small" role="status" aria-live="polite" data-translation-status="{{ $locale }}"></p>
                @foreach($fields as $field => $options)
                    @php
                        $key = 'translations.'.$locale.'.'.$field;
                        $id = 'translation-'.$type.'-'.$locale.'-'.$field;
                        $value = old($key, $translation?->getAttribute($field));
                    @endphp
                    <div class="mb-3">
                        <label class="form-label" for="{{ $id }}">{{ $options['label'] }} in {{ strtolower($language) }}</label>
                        @if(isset($options['rows']))
                            <textarea id="{{ $id }}" name="translations[{{ $locale }}][{{ $field }}]" data-translation-field="{{ $field }}" data-translation-locale="{{ $locale }}" class="form-control @error($key) is-invalid @enderror" rows="{{ $options['rows'] }}" maxlength="{{ $options['max'] }}" lang="{{ $locale }}" @error($key) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror>{{ is_scalar($value) ? $value : '' }}</textarea>
                        @else
                            <input id="{{ $id }}" name="translations[{{ $locale }}][{{ $field }}]" data-translation-field="{{ $field }}" data-translation-locale="{{ $locale }}" class="form-control @error($key) is-invalid @enderror" value="{{ is_scalar($value) ? $value : '' }}" maxlength="{{ $options['max'] }}" lang="{{ $locale }}" @error($key) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror>
                        @endif
                        @error($key)<div class="invalid-feedback" id="{{ $id }}-error">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>
        </details>
    @endforeach
</fieldset>
