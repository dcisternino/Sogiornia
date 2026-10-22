@extends('layouts.app')
@section('title', 'Template Word · '.$property->name)

{{-- Spiega cosa contiene il modello e distingue il download dal futuro caricamento AI. --}}
@section('content')
<div class="container py-5 form-container workspace">
    <a class="back-link" href="{{ $setupNavigation->back('host.sections.index', $property) }}">{{ $setupNavigation->active() ? '← Torna alla configurazione guidata' : '← Sezioni della struttura' }}</a>
    <span class="eyebrow d-block mt-4">PASSAGGIO 3</span>
    <h1 class="mt-2">La tua guida parte dal Word</h1>
    <p class="text-muted-custom">Raccogli in un unico documento le informazioni di {{ $property->name }}. Nome, posizione e stanze già presenti sono precompilati.</p>
    <section class="sg-card my-4"><h2 class="h4">Scarica e compila il modello</h2>
        <p>Troverai spazi per benvenuto, arrivo e partenza, Wi-Fi, regole della casa, numeri utili, luoghi da visitare e dove mangiare.</p>
        <ol class="ps-3"><li class="mb-2">Sostituisci i segnaposto con informazioni corrette e aggiornate.</li><li class="mb-2">Indica per ogni sezione se vale per tutte le stanze o soltanto per alcune.</li><li class="mb-2">Duplica i blocchi per aggiungere più contatti, pulsanti e luoghi nella stessa sezione.</li><li>Salva una copia del documento compilato.</li></ol>
        <a class="btn btn-primary" href="{{ $setupNavigation->url('host.guide-template.download', $property) }}">Scarica il template Word</a>
    </section>
    <section class="sg-card mb-4"><h2 class="h4">Come usare i contenuti adesso</h2><p>Puoi inserire i testi nella pagina delle sezioni, controllare l’anteprima e approvare la pubblicazione. Il modello Word è facoltativo e il download non modifica la guida degli ospiti.</p><p class="small text-muted-custom">Il caricamento del documento e l’elaborazione AI non sono ancora disponibili: saranno aggiunti nel prossimo sviluppo. Questo modello precompila i dati principali, non esporta ancora le sezioni esistenti.</p><a class="btn btn-outline-secondary" href="{{ $setupNavigation->url('host.sections.index', $property) }}">Prepara le sezioni</a></section>
</div>
@endsection
