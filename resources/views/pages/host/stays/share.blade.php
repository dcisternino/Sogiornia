@extends('layouts.app')
@section('title', 'Condividi la guida · '.$room->name)

{{-- Messaggio pronto nella lingua dell'ospite, inviato solo tramite il canale scelto dall'host. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <a class="back-link" href="{{ route('host.stays.index', [$property, $room]) }}">← Soggiorni di {{ $room->name }}</a>
    <h1 class="mt-4">Condividi la guida</h1>
    <p class="text-muted-custom">{{ $property->name }} · {{ $room->name }} · {{ $stay->reference }}</p>
    <form method="GET" action="{{ route('host.stays.share', [$property, $room, $stay]) }}" class="sg-card mb-4">
        <label class="form-label" for="share-language">Lingua dell'ospite</label><div class="d-flex flex-wrap gap-2"><select id="share-language" name="lang" class="form-select share-language">
            @foreach(config('guest.locales') as $code => $language)<option value="{{ $code }}" @selected($locale === $code)>{{ $language['name'] }}</option>@endforeach
        </select><button class="btn btn-outline-secondary" type="submit">Prepara messaggio</button></div>
        <p class="small text-muted-custom mb-0 mt-2">Cambiare lingua ricrea il messaggio e sostituisce le modifiche locali. Anche il link aprirà la guida nella lingua scelta.</p>
    </form>
    <section class="sg-card" data-share-editor data-share-subject="{{ $share['subject'] }}">
        <label class="form-label" for="share-message">Messaggio da controllare e personalizzare</label>
        <textarea class="form-control" id="share-message" rows="12" maxlength="10000" lang="{{ $locale }}" data-share-message>{{ $share['message'] }}</textarea>
        <p class="small text-muted-custom mt-3">Il link funziona dall'arrivo alla partenza, in orario italiano. Chi lo riceve può consultare la guida. Non vengono inviati messaggi automaticamente.</p>
        <div class="d-flex flex-wrap gap-2"><a class="btn btn-primary" href="{{ $share['whatsappUrl'] }}" target="_blank" rel="noopener noreferrer" data-share-whatsapp>Apri WhatsApp ↗</a><a class="btn btn-outline-secondary" href="{{ $share['emailUrl'] }}" data-share-email>Apri email</a><button class="btn btn-outline-secondary" type="button" data-copy-target="share-message">Copia messaggio</button></div>
        <p class="small text-muted-custom mt-3 mb-0">Scegli il destinatario e conferma l'invio in WhatsApp o nel programma email. Le modifiche al messaggio restano solo in questa pagina.</p>
        <noscript><p class="mt-3">Senza JavaScript i pulsanti usano il messaggio originale. Per inviare un testo modificato, selezionalo e copialo manualmente.</p></noscript>
    </section>
</div>
@endsection
