import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Limita le stanze del filtro alla struttura scelta e azzera selezioni incompatibili.
document.querySelectorAll('[data-filter-rooms]').forEach((propertySelect) => {
    const roomSelect = document.getElementById(propertySelect.dataset.filterRooms);
    if (!(roomSelect instanceof HTMLSelectElement)) return;
    const refresh = () => {
        [...roomSelect.options].forEach((option) => {
            const excluded = Boolean(propertySelect.value && option.dataset.propertyId && option.dataset.propertyId !== propertySelect.value);
            option.hidden = excluded;
            option.disabled = excluded;
            if (excluded && option.selected) roomSelect.value = '';
        });
    };
    propertySelect.addEventListener('change', refresh);
    refresh();
});

// Salva il singolo interruttore senza inviare gli altri moduli della pagina.
document.addEventListener('change', (event) => {
    const input = event.target;
    if (input instanceof HTMLInputElement && input.matches('[data-submit-on-change]')) {
        input.form?.requestSubmit();
    }
});

// Chiude conferme e riepiloghi degli errori dopo cinque secondi, lasciando gli errori sui campi.
document.querySelectorAll('[data-auto-dismiss]').forEach((notice) => {
    window.setTimeout(() => { notice.remove(); }, 5000);
});
document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) return;
    event.target.closest('[data-dismiss-notice]')?.closest('[data-auto-dismiss]')?.remove();
});

// Mostra la foto scelta senza inviarla prima del salvataggio del modulo.
document.addEventListener('change', (event) => {
    const input = event.target;
    if (!(input instanceof HTMLInputElement) || !input.dataset.imagePreview) return;
    const preview = document.getElementById(input.dataset.imagePreview);
    if (!(preview instanceof HTMLImageElement)) return;
    preview.dataset.originalSrc ??= preview.src;
    if (preview.dataset.objectUrl) URL.revokeObjectURL(preview.dataset.objectUrl);
    const image = input.files?.[0];
    if (!image || !['image/jpeg', 'image/png', 'image/webp'].includes(image.type) || image.size > 5 * 1024 * 1024) {
        preview.src = preview.dataset.originalSrc;
        return;
    }
    preview.dataset.objectUrl = URL.createObjectURL(image);
    preview.src = preview.dataset.objectUrl;
});

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
        window.setTimeout(() => { button.textContent = 'Copia'; }, 5000);
    } catch {
        input.focus();
        input.select();
        button.textContent = 'Selezionato';
    }
});
