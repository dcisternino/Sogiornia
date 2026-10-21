import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Chiede conferma soltanto per eliminazioni e revoca dei link già condivisi.
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form instanceof HTMLFormElement && form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});

// Copia il link della stanza con una selezione manuale di riserva.
document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-target]');
    if (!button) return;
    const input = document.getElementById(button.dataset.copyTarget);
    if (!(input instanceof HTMLInputElement)) return;
    try {
        await navigator.clipboard.writeText(input.value);
        button.textContent = 'Copiato!';
        window.setTimeout(() => { button.textContent = 'Copia'; }, 2000);
    } catch {
        input.focus();
        input.select();
        button.textContent = 'Selezionato';
    }
});
