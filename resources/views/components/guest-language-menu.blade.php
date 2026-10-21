{{-- Selettore predisposto: soltanto l'italiano è attualmente disponibile. --}}
<div {{ $attributes->class(['dropdown guest-language']) }}>
    <button class="guest-guide-badge dropdown-toggle border-0" type="button" id="guest-language-button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Lingua della guida: italiano">
        <span class="flag-it" aria-hidden="true"></span> IT
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="guest-language-button">
        <li><span class="dropdown-item-text fw-semibold"><span class="flag-it me-2" aria-hidden="true"></span>Italiano · IT <span aria-hidden="true">✓</span></span></li>
        <li><hr class="dropdown-divider"></li>
        <li><button class="dropdown-item" type="button" disabled lang="en">English · EN <small>Prossimamente</small></button></li>
        <li><button class="dropdown-item" type="button" disabled lang="es">Español · ES <small>Prossimamente</small></button></li>
        <li><button class="dropdown-item" type="button" disabled lang="de">Deutsch · DE <small>Prossimamente</small></button></li>
    </ul>
</div>
