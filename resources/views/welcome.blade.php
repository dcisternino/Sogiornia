@extends('layouts.app')

@section('title', 'Meno messaggi, più ospitalità')
@section('description', 'Una guida digitale per ogni stanza: Wi-Fi, check-in e consigli sempre a portata di mano. Scopri Sogiornia: 30 giorni gratuiti, poi 19 € al mese.')

{{-- Presentazione del servizio con invito a scoprire la guida e ad accedere. --}}
@section('content')
<section class="landing-hero overflow-hidden">
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 pe-lg-5">
                <span class="eyebrow"><span class="status-dot"></span> IL TUO MODO DI ACCOGLIERE, DIGITALE</span>
                <h1 class="hero-title mt-4">Falli sentire a casa.<br><em>Prima ancora<br>di arrivare.</em></h1>
                <p class="hero-description mt-4">Tutto quello che i tuoi ospiti devono sapere, in una guida semplice e curata. Meno domande ripetute. Più tempo per la tua ospitalità.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a class="btn btn-primary" href="#prezzi">Scopri i 30 giorni gratuiti <span aria-hidden="true">↗</span></a>
                    <a class="btn btn-outline-secondary" href="{{ route('guest-home') }}">Esplora una guida demo</a>
                </div>
                <p class="small text-muted-custom mt-3">Per B&amp;B, affittacamere e case vacanza. Fatto per chi accoglie.</p>
                <div class="hero-proof mt-5 d-flex gap-4">
                    <div><strong>Una guida</strong><span>per ogni stanza</span></div>
                    <div><strong>Un solo link</strong><span>da condividere</span></div>
                    <div><strong>Zero app</strong><span>per i tuoi ospiti</span></div>
                </div>
            </div>
            <div class="col-lg-6">
                {{-- Anteprima illustrativa della guida, distinta dalla demo navigabile. --}}
                <div class="hero-scene">
                    <div class="scene-orbit orbit-one"></div><div class="scene-orbit orbit-two"></div>
                    <span class="scene-caption">PICCOLE ATTENZIONI.<br>GRANDI SOGGIORNI.</span>
                    <div class="guide-preview">
                        <div class="preview-top"><span class="brand-word">sogiornia<span>.</span></span><span class="preview-pill">La tua guida</span></div>
                        <div class="preview-art" aria-hidden="true"><div class="sun"></div><div class="arch arch-back"></div><div class="arch arch-front"></div><div class="plant">✳</div></div>
                        <div class="preview-body">
                            <span class="eyebrow">BENVENUTO A CASA MARINA</span>
                            <h2>Il tuo soggiorno,<br>con un tocco in più.</h2>
                            <p>Stanza Levante · Bari</p>
                            <div class="preview-grid">
                                <div><span aria-hidden="true">⌁</span>Wi-Fi <b>↗</b></div>
                                <div><span aria-hidden="true">⌂</span>Check-in <b>↗</b></div>
                                <div><span aria-hidden="true">☷</span>La casa <b>↗</b></div>
                                <div><span aria-hidden="true">✦</span>Da scoprire <b>↗</b></div>
                            </div>
                        </div>
                    </div>
                    <div class="floating-note"><span aria-hidden="true">✓</span><div>Le risposte sono già qui.<small>Tu goditi il piacere di accogliere.</small></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Benefici concreti per chi gestisce una struttura ricettiva. --}}
<section id="vantaggi" class="section-space benefits-section">
    <div class="container">
        <div class="row mb-5 align-items-end g-4">
            <div class="col-lg-7"><span class="eyebrow">L'ACCOGLIENZA È NEI DETTAGLI</span><h2 class="section-title mt-3">La stessa cura di sempre.<br>Molte interruzioni in meno.</h2></div>
            <div class="col-lg-5"><p class="text-muted-custom mb-0">La password del Wi-Fi, l'orario del check-out, quel ristorante che consigli sempre. Dai a ogni informazione il suo posto.</p></div>
        </div>
        <div class="row g-4">
            <div class="col-md-4"><article class="benefit-card h-100"><span class="feature-symbol" aria-hidden="true">↗</span><h3 class="h4 mt-4">Un link. Tutte le risposte.</h3><p class="text-muted-custom">Condividi la guida della stanza. Gli ospiti la aprono dal telefono, senza scaricare un'app o creare un account.</p><span class="feature-label">SEMPLICE PER CHI ARRIVA</span></article></div>
            <div class="col-md-4"><article class="benefit-card h-100"><span class="feature-symbol" aria-hidden="true">☷</span><h3 class="h4 mt-4">Ogni stanza, la sua guida.</h3><p class="text-muted-custom">Crea le tue sezioni e scegli quali mostrare in ogni stanza. Un'accoglienza su misura, con il controllo nelle tue mani.</p><span class="feature-label">FLESSIBILE PER CHI OSPITA</span></article></div>
            <div class="col-md-4"><article class="benefit-card h-100"><span class="feature-symbol" aria-hidden="true">✦</span><h3 class="h4 mt-4">I tuoi consigli fanno casa.</h3><p class="text-muted-custom">Regole chiare, contatti utili e luoghi del cuore. Trasforma le informazioni pratiche in un'esperienza più personale.</p><span class="feature-label">CURATO IN OGNI DETTAGLIO</span></article></div>
        </div>
    </div>
</section>

{{-- Percorso operativo dalla struttura al link del soggiorno. --}}
<section id="come-funziona" class="section-space">
    <div class="container">
        <div class="text-center mb-5"><span class="eyebrow">POCO DA IMPARARE. TANTO DA CONDIVIDERE.</span><h2 class="section-title mt-3">La tua guida prende forma<br>in tre semplici passi.</h2></div>
        <div class="row g-5">
            <div class="col-md-4"><div class="step-number">01</div><h3 class="h4 mt-4">Racconta la tua struttura</h3><p class="text-muted-custom">Aggiungi la struttura, il messaggio di benvenuto e le informazioni utili ai tuoi ospiti.</p></div>
            <div class="col-md-4"><div class="step-number">02</div><h3 class="h4 mt-4">Prepara ogni stanza</h3><p class="text-muted-custom">Crea le stanze e seleziona, per ciascuna, le sezioni che desideri rendere visibili.</p></div>
            <div class="col-md-4"><div class="step-number">03</div><h3 class="h4 mt-4">Condividi l'accoglienza</h3><p class="text-muted-custom">Crea il soggiorno e condividi il suo link riservato. Scade alla partenza e puoi revocarlo prima.</p></div>
        </div>
    </div>
</section>

{{-- Offerta commerciale informativa: nessun pagamento o rinnovo automatico attivo. --}}
<section id="prezzi" class="section-space pt-0">
    <div class="container">
        <div class="pricing-panel">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="eyebrow">PIÙ OSPITALITÀ, CON LEGGEREZZA</span>
                    <h2 class="section-title mt-3">Un piccolo prezzo.<br>Spazio a un'accoglienza migliore.</h2>
                    <p class="mt-4 pricing-description">Parti con 30 giorni gratuiti: il primo mese lo offriamo noi. Scopri un modo più ordinato di prenderti cura dei tuoi ospiti.</p>
                    <ul class="pricing-features list-unstyled mt-4 mb-0">
                        <li><span aria-hidden="true">✓</span> Gestione di strutture e stanze</li>
                        <li><span aria-hidden="true">✓</span> Sezioni configurabili per ogni stanza</li>
                        <li><span aria-hidden="true">✓</span> Guide accessibili dal browser, senza app</li>
                        <li><span aria-hidden="true">✓</span> Link per soggiorno con scadenza e revoca</li>
                    </ul>
                </div>
                <div class="col-lg-4 offset-lg-1">
                    <div class="price-card">
                        <span class="badge badge-soft">30 GIORNI DI PROVA GRATUITA</span>
                        <div class="price-amount mt-4">19<span>€</span><small>/ mese</small></div>
                        <p class="text-muted-custom">dopo i primi 30 giorni gratuiti</p>
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 mt-3">Accedi al tuo account <span aria-hidden="true">↗</span></a>
                        <p class="small text-muted-custom mt-3 mb-0">L'attivazione dell'account è gestita dall'amministratore. L'offerta è informativa: i pagamenti online non sono ancora attivi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Risposte alle domande principali, senza promesse di funzionalità non disponibili. --}}
<section class="container pb-5 mb-4">
    <div class="row g-5">
        <div class="col-lg-4"><span class="eyebrow">PRIMA DI INIZIARE</span><h2 class="h2 mt-3">Qualche domanda?</h2></div>
        <div class="col-lg-8">
            <details class="faq-item"><summary>Gli ospiti devono installare un'app?</summary><p>No. La guida si apre dal link del soggiorno direttamente nel browser del telefono.</p></details>
            <details class="faq-item"><summary>Posso mostrare informazioni diverse per stanza?</summary><p>Sì. Scrivi le sezioni della struttura e scegli quali abilitare in ogni stanza. Puoi cambiare la selezione quando vuoi.</p></details>
            <details class="faq-item"><summary>Chi può vedere la guida?</summary><p>Chi possiede il link riservato del soggiorno, durante il periodo di validità. Il link scade alla partenza e può essere revocato prima.</p></details>
            <details class="faq-item"><summary>Come ottengo il mio account host?</summary><p>L'amministratore ti invita via email. Apri il link e scegli personalmente la tua password. Se il link scade puoi richiederne uno nuovo.</p></details>
        </div>
    </div>
</section>
@endsection
