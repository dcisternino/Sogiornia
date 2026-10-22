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
    if (!(event.target instanceof Element)) return;
    const button = event.target.closest('[data-copy-target]');
    if (!button) return;
    const input = document.getElementById(button.dataset.copyTarget);
    if (!(input instanceof HTMLInputElement) && !(input instanceof HTMLTextAreaElement)) return;
    const originalLabel = button.textContent;
    try {
        await navigator.clipboard.writeText(input.value);
        button.textContent = 'Copiato!';
        window.setTimeout(() => { button.textContent = originalLabel; }, 5000);
    } catch {
        input.focus();
        input.select();
        button.textContent = 'Selezionato';
        window.setTimeout(() => { button.textContent = originalLabel; }, 5000);
    }
});

// Mostra i campi utili per il tipo di contenuto senza perdere i valori già inseriti.
document.querySelectorAll('[data-block-editor]').forEach((editor) => {
    const select = editor.querySelector('[data-block-type]');
    const refresh = () => {
        editor.querySelectorAll('[data-block-fields]').forEach((group) => {
            group.hidden = !group.dataset.blockFields.split(' ').includes(select.value) && !group.querySelector('.is-invalid');
        });
    };
    select.addEventListener('change', refresh);
    refresh();
});

// Aggiorna i canali di condivisione usando soltanto il messaggio visibile, senza invii automatici.
document.querySelectorAll('[data-share-editor]').forEach((editor) => {
    const message = editor.querySelector('[data-share-message]');
    const refresh = () => {
        editor.querySelector('[data-share-whatsapp]').href = `https://wa.me/?text=${encodeURIComponent(message.value)}`;
        editor.querySelector('[data-share-email]').href = `mailto:?subject=${encodeURIComponent(editor.dataset.shareSubject)}&body=${encodeURIComponent(message.value)}`;
    };
    message.addEventListener('input', refresh);
    refresh();
});

// Azzera la stanza quando il percorso viene spostato su una struttura diversa.
document.querySelectorAll('[data-setup-property]').forEach((select) => {
    select.addEventListener('change', () => {
        const rooms = document.getElementById('setup-room');
        rooms.value = '';
        [...rooms.options].forEach((option) => { option.disabled = Boolean(option.value); });
    });
});

// Inserisce bozze tradotte nel modulo, proteggendo i campi modificati durante l'attesa.
document.querySelectorAll('[data-translation-editor]').forEach((editor) => {
    const form = editor.closest('form');
    let pending = false;
    form.addEventListener('submit', (event) => { if (pending) event.preventDefault(); });
    editor.querySelectorAll('[data-translate-locale]').forEach((button) => {
        button.addEventListener('click', async () => {
            if (pending) return;
            const locale = button.dataset.translateLocale;
            const status = editor.querySelector(`[data-translation-status="${locale}"]`);
            const showStatus = (message, persistent = false) => {
                window.clearTimeout(Number(status.dataset.dismissTimer));
                status.textContent = message;
                if (!persistent) status.dataset.dismissTimer = String(window.setTimeout(() => { status.textContent = ''; }, 5000));
            };
            const replace = editor.querySelector(`[data-translation-replace="${locale}"]`).checked;
            const snapshots = [];
            const fields = {};
            editor.querySelectorAll(`[data-translation-locale="${locale}"]`).forEach((target) => {
                const field = target.dataset.translationField;
                const source = form.elements.namedItem(field);
                if (!source || !source.value.trim() || (!replace && target.value.trim())) return;
                fields[field] = source.value;
                snapshots.push({ field, source, target, sourceValue: source.value, targetValue: target.value });
            });
            if (!snapshots.length) {
                showStatus('Nessun campo da tradurre: inserisci i testi italiani oppure abilita la sostituzione dei testi già compilati.');
                return;
            }
            pending = true;
            const buttons = [...form.querySelectorAll('button[type="submit"], [data-translate-locale]')];
            const originalStates = buttons.map((item) => item.disabled);
            buttons.forEach((item) => { item.disabled = true; });
            editor.setAttribute('aria-busy', 'true');
            showStatus('Traduzione in corso. Attendi prima di salvare.', true);
            const controller = new AbortController();
            const timeout = window.setTimeout(() => controller.abort(), 30000);
            try {
                const response = await fetch(editor.dataset.translationUrl, {
                    method: 'POST', credentials: 'same-origin', signal: controller.signal,
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': form.elements.namedItem('_token').value },
                    body: JSON.stringify({ type: editor.dataset.translationType, locale, fields }),
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const message = response.status === 419 ? 'Sessione scaduta: copia i testi prima di ricaricare la pagina.' : response.status === 429 ? 'Troppe richieste: attendi un minuto prima di riprovare.' : data.message;
                    throw new Error(message || 'Traduzione non disponibile. I testi sono rimasti invariati.');
                }
                if (!data.fields || snapshots.some((item) => typeof data.fields[item.field] !== 'string' || data.fields[item.field].length > item.target.maxLength)) {
                    throw new Error('Risposta incompleta o troppo lunga: nessuna traduzione applicata.');
                }
                let applied = 0;
                snapshots.forEach((item) => {
                    if (item.source.value === item.sourceValue && item.target.value === item.targetValue) {
                        item.target.value = data.fields[item.field];
                        item.target.dispatchEvent(new Event('input', { bubbles: true }));
                        applied++;
                    }
                });
                showStatus(`${applied} campi tradotti. Controlla la bozza e premi Salva per pubblicarla.${applied < snapshots.length ? ' I campi modificati durante l’attesa sono rimasti invariati.' : ''}`);
            } catch (error) {
                showStatus(error.name === 'AbortError' ? 'La traduzione sta impiegando troppo tempo. Nessun testo è stato modificato: riprova tra poco.' : error.message);
            } finally {
                window.clearTimeout(timeout);
                pending = false;
                buttons.forEach((item, index) => { item.disabled = originalStates[index]; });
                editor.removeAttribute('aria-busy');
            }
        });
    });
});
