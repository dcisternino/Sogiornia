{{-- Dati condivisi tra creazione dalla dashboard e dalla stanza. --}}
<x-input name="reference" label="Riferimento soggiorno" maxlength="120" :required="true" help="Usa un codice o un riferimento interno; non servono documenti o nomi degli ospiti." />
<div class="row">
    <div class="col-sm-6"><x-input name="arrival_date" label="Data di arrivo" type="date" :required="true" :value="now('Europe/Rome')->format('Y-m-d')" /></div>
    <div class="col-sm-6"><x-input name="arrival_time" label="Orario di arrivo" type="time" :required="true" value="15:00" /></div>
</div>
<x-input name="departure_date" label="Data di partenza" type="date" :required="true" :value="now('Europe/Rome')->addDay()->format('Y-m-d')" />
<p class="small text-muted-custom">Orari italiani (Europe/Rome). Il link scade alla partenza alle 10:00.</p>
