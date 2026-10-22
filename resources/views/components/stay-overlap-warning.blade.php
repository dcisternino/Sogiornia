{{-- Conferma esplicita mostrata soltanto dopo il rilevamento di una sovrapposizione. --}}
@if($errors->has('overlap'))
    <div class="alert alert-warning" role="alert">
        <strong>Attenzione: soggiorni sovrapposti</strong><p>{{ $errors->first('overlap') }}</p>
        <p class="small">Anche i soggiorni con link revocato occupano il periodo: la revoca riguarda solo l'accesso alla guida.</p>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="overlap_confirmed" name="overlap_confirmed" value="1"><label class="form-check-label" for="overlap_confirmed">Ho controllato le date e confermo il salvataggio anche con la sovrapposizione.</label></div>
    </div>
@endif
