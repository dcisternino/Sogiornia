<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HostAccessController;
use App\Http\Controllers\Admin\HostController;
use App\Http\Controllers\Admin\HostInvitationController;
use App\Http\Controllers\Admin\HostStatusController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestBlockImageController;
use App\Http\Controllers\GuestGuideController;
use App\Http\Controllers\GuestRoomImageController;
use App\Http\Controllers\GuestSectionImageController;
use App\Http\Controllers\Host\BookingController;
use App\Http\Controllers\Host\DashboardController as HostDashboardController;
use App\Http\Controllers\Host\GuestPreviewController;
use App\Http\Controllers\Host\PropertyController;
use App\Http\Controllers\Host\PropertyImageController;
use App\Http\Controllers\Host\PropertyLifecycleController;
use App\Http\Controllers\Host\RoomController;
use App\Http\Controllers\Host\RoomImageController;
use App\Http\Controllers\Host\RoomLifecycleController;
use App\Http\Controllers\Host\SectionBlockController;
use App\Http\Controllers\Host\SectionBlockImageController;
use App\Http\Controllers\Host\SectionBlockOrderController;
use App\Http\Controllers\Host\SectionController;
use App\Http\Controllers\Host\SectionImageController;
use App\Http\Controllers\Host\SectionSettingController;
use App\Http\Controllers\Host\StayController;
use App\Http\Controllers\Host\StayRevocationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Presenta il servizio e l'offerta commerciale.
Route::get('/', [PageController::class, 'welcome'])->name('welcome');
// Mostra una guida dimostrativa senza credenziali reali.
Route::get('/guest-home', [GuestGuideController::class, 'index'])->middleware('guest.locale')->name('guest-home');
// Mostra una sezione dimostrativa consentita.
Route::get('/guest/sections/{section}', [GuestGuideController::class, 'show'])->middleware('guest.locale')->name('guest.section');

// Protegge la riservatezza del link del soggiorno e delle relative immagini.
Route::middleware('guide.privacy')->where(['token' => '[A-Za-z0-9]{64}'])->group(function (): void {
    // Mostra la guida solo durante il periodo del soggiorno.
    Route::get('/g/{token}', [GuestGuideController::class, 'index'])->middleware('guest.locale')->name('guide.index');
    // Mostra la copertina della stanza soltanto con soggiorno valido.
    Route::get('/g/{token}/room-image', [GuestRoomImageController::class, 'show'])->name('guide.room-image');
    // Mostra una sezione abilitata nella stanza.
    Route::get('/g/{token}/sections/{section}', [GuestGuideController::class, 'show'])->middleware('guest.locale')->name('guide.show');
    // Serve l'immagine privata dopo gli stessi controlli della guida.
    Route::get('/g/{token}/sections/{section}/image', [GuestSectionImageController::class, 'show'])->name('guide.image');
    // Mostra la foto di un contenuto attivo soltanto con soggiorno e sezione accessibili.
    Route::get('/g/{token}/sections/{section}/blocks/{block}/image', [GuestBlockImageController::class, 'show'])->whereNumber('block')->name('guide.blocks.image');
});

// Gestisce accesso, inviti e recupero password senza una registrazione pubblica.
Route::middleware('guest')->group(function (): void {
    // Mostra il modulo di accesso.
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    // Verifica le credenziali limitando i tentativi.
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    // Mostra il modulo per richiedere un nuovo link.
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    // Invia il link senza rivelare gli account esistenti.
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    // Mostra la scelta password per inviti e recupero, senza diffondere il token.
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->middleware('guide.privacy')->name('password.reset');
    // Consuma il token monouso e aggiorna la password.
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.update');
});

// Riserva profilo e navigazione agli utenti autenticati.
Route::middleware('auth')->group(function (): void {
    // Termina la sessione anche per account in sola lettura.
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    // Sceglie la dashboard corrispondente al ruolo.
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    // Mostra il profilo personale.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Aggiorna il profilo solo se l'account può effettuare modifiche.
    Route::patch('/profile', [ProfileController::class, 'update'])->middleware('host.write')->name('profile.update');
});

// Riserva all'amministratore inviti e stato degli host.
Route::middleware(['auth', 'role:admin'])->group(function (): void {
    // Riepiloga gli host attivi, disattivi e prossimi alla scadenza.
    Route::get('/admin-home', AdminDashboardController::class)->name('admin-home');
    // Elenca gli host con filtri sullo stato operativo.
    Route::get('/admin/hosts', [HostController::class, 'index'])->name('admin.hosts.index');
    // Mostra il modulo di invito.
    Route::get('/admin/hosts/create', [HostController::class, 'create'])->name('admin.hosts.create');
    // Crea l'host e invia il link per scegliere la password.
    Route::post('/admin/hosts', [HostController::class, 'store'])->name('admin.hosts.store');
    // Mostra dati, strutture, stanze e soggiorni del singolo host.
    Route::get('/admin/hosts/{host}', [HostController::class, 'show'])->whereNumber('host')->name('admin.hosts.show');
    // Imposta o rimuove la scadenza operativa dell'host.
    Route::patch('/admin/hosts/{host}/access', [HostAccessController::class, 'update'])->whereNumber('host')->name('admin.hosts.access');
    // Reinvia un invito non ancora accettato.
    Route::post('/admin/hosts/{host}/invitation', [HostInvitationController::class, 'store'])->middleware('throttle:10,1')->name('admin.hosts.invitation');
    // Disattiva o riattiva l'host senza bloccare i soggiorni.
    Route::patch('/admin/hosts/{host}/status', [HostStatusController::class, 'update'])->name('admin.hosts.status');
});

// Applica proprietà dei dati e modalità in sola lettura a tutta l'area host.
Route::middleware(['auth', 'role:host', 'host.write'])
    ->where(['property' => '[0-9]+', 'room' => '[0-9]+', 'section' => '[0-9]+', 'stay' => '[0-9]+'])
    ->group(function (): void {
        // Mostra il calendario dei soggiorni attivi, programmati e passati.
        Route::get('/host-home', HostDashboardController::class)->name('host-home');
        // Prepara un soggiorno scegliendo una stanza delle proprie strutture.
        Route::get('/host/stays/create', [BookingController::class, 'create'])->name('host.bookings.create');
        // Associa il nuovo soggiorno a una stanza autorizzata.
        Route::post('/host/stays', [BookingController::class, 'store'])->name('host.bookings.store');
        // Gestisce i dati principali delle strutture.
        Route::resource('/host/properties', PropertyController::class)->except(['destroy'])->names('host.properties');
        // Mostra la foto della struttura esclusivamente all'host proprietario.
        Route::get('/host/properties/{property}/image', [PropertyImageController::class, 'show'])->name('host.properties.image');
        // Gestisce i dati delle stanze e le sezioni abilitate.
        Route::resource('/host/properties/{property}/rooms', RoomController::class)->except(['show', 'destroy'])->names('host.rooms');
        // Mostra la guida in anteprima soltanto all'host proprietario, anche senza soggiorno.
        Route::get('/host/properties/{property}/rooms/{room}/preview', [GuestPreviewController::class, 'index'])->middleware(['guest.locale', 'guide.privacy'])->name('host.preview.index');
        // Mostra una sezione effettivamente abilitata nell'anteprima privata della stanza.
        Route::get('/host/properties/{property}/rooms/{room}/preview/sections/{section}', [GuestPreviewController::class, 'show'])->middleware(['guest.locale', 'guide.privacy'])->where('section', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('host.preview.show');
        // Mostra la foto della stanza esclusivamente all'host proprietario.
        Route::get('/host/properties/{property}/rooms/{room}/image', [RoomImageController::class, 'show'])->name('host.rooms.image');
        // Gestisce i contenuti informativi arricchiti.
        Route::resource('/host/properties/{property}/sections', SectionController::class)->except(['show'])->names('host.sections');
        // Gestisce i singoli contenuti del contenitore con controlli sulla struttura e sulla sezione.
        Route::resource('/host/properties/{property}/sections/{section}/blocks', SectionBlockController::class)->except(['show'])->whereNumber('block')->names('host.blocks');
        // Ordina o nasconde un contenuto senza cancellarlo.
        Route::patch('/host/properties/{property}/sections/{section}/blocks/{block}/order', [SectionBlockOrderController::class, 'update'])->whereNumber('block')->name('host.blocks.order');
        // Serve le immagini private dei contenuti al solo proprietario.
        Route::get('/host/properties/{property}/sections/{section}/blocks/{block}/image', [SectionBlockImageController::class, 'show'])->whereNumber('block')->name('host.blocks.image');
        // Attiva o condivide una sezione senza cambiare le assegnazioni delle stanze.
        Route::patch('/host/properties/{property}/sections/{section}/settings', [SectionSettingController::class, 'update'])->name('host.sections.settings');
        // Mostra l'anteprima privata di un'immagine all'host proprietario.
        Route::get('/host/properties/{property}/sections/{section}/image', [SectionImageController::class, 'show'])->name('host.sections.image');
        // Archivia, ripristina o sospende la struttura.
        Route::patch('/host/properties/{property}/lifecycle', [PropertyLifecycleController::class, 'update'])->name('host.properties.lifecycle');
        // Elimina la struttura dopo conferma del nome.
        Route::delete('/host/properties/{property}', [PropertyLifecycleController::class, 'destroy'])->name('host.properties.destroy');
        // Archivia, ripristina o sospende la stanza.
        Route::patch('/host/properties/{property}/rooms/{room}/lifecycle', [RoomLifecycleController::class, 'update'])->name('host.rooms.lifecycle');
        // Elimina la stanza dopo conferma del nome.
        Route::delete('/host/properties/{property}/rooms/{room}', [RoomLifecycleController::class, 'destroy'])->name('host.rooms.destroy');
        // Elenca i soggiorni della stanza.
        Route::get('/host/properties/{property}/rooms/{room}/stays', [StayController::class, 'index'])->name('host.stays.index');
        // Mostra il modulo del nuovo soggiorno.
        Route::get('/host/properties/{property}/rooms/{room}/stays/create', [StayController::class, 'create'])->name('host.stays.create');
        // Crea un soggiorno con un link sempre nuovo.
        Route::post('/host/properties/{property}/rooms/{room}/stays', [StayController::class, 'store'])->name('host.stays.store');
        // Mostra il modulo di modifica di un soggiorno della stanza posseduta.
        Route::get('/host/properties/{property}/rooms/{room}/stays/{stay}/edit', [StayController::class, 'edit'])->name('host.stays.edit');
        // Aggiorna date e riferimento mantenendo token e revoca del soggiorno.
        Route::put('/host/properties/{property}/rooms/{room}/stays/{stay}', [StayController::class, 'update'])->name('host.stays.update');
        // Revoca anticipatamente l'accesso di un soggiorno.
        Route::patch('/host/properties/{property}/rooms/{room}/stays/{stay}/revoke', [StayRevocationController::class, 'update'])->name('host.stays.revoke');
    });
