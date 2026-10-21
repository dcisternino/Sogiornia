<?php

use App\Http\Controllers\Admin\HostController;
use App\Http\Controllers\Admin\HostInvitationController;
use App\Http\Controllers\Admin\HostStatusController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestGuideController;
use App\Http\Controllers\GuestSectionImageController;
use App\Http\Controllers\Host\PropertyController;
use App\Http\Controllers\Host\PropertyLifecycleController;
use App\Http\Controllers\Host\RoomController;
use App\Http\Controllers\Host\RoomLifecycleController;
use App\Http\Controllers\Host\SectionController;
use App\Http\Controllers\Host\SectionImageController;
use App\Http\Controllers\Host\StayController;
use App\Http\Controllers\Host\StayRevocationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Presenta il servizio e l'offerta commerciale.
Route::get('/', [PageController::class, 'welcome'])->name('welcome');
// Mostra una guida dimostrativa senza credenziali reali.
Route::get('/guest-home', [GuestGuideController::class, 'index'])->name('guest-home');
// Mostra una sezione dimostrativa consentita.
Route::get('/guest/sections/{section}', [GuestGuideController::class, 'show'])->name('guest.section');

// Protegge la riservatezza del link del soggiorno e delle relative immagini.
Route::middleware('guide.privacy')->where(['token' => '[A-Za-z0-9]{64}'])->group(function (): void {
    // Mostra la guida solo durante il periodo del soggiorno.
    Route::get('/g/{token}', [GuestGuideController::class, 'index'])->name('guide.index');
    // Mostra una sezione abilitata nella stanza.
    Route::get('/g/{token}/sections/{section}', [GuestGuideController::class, 'show'])->name('guide.show');
    // Serve l'immagine privata dopo gli stessi controlli della guida.
    Route::get('/g/{token}/sections/{section}/image', [GuestSectionImageController::class, 'show'])->name('guide.image');
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
    // Elenca gli host, compresi quelli disattivati.
    Route::get('/admin-home', [HostController::class, 'index'])->name('admin-home');
    // Mostra il modulo di invito.
    Route::get('/admin/hosts/create', [HostController::class, 'create'])->name('admin.hosts.create');
    // Crea l'host e invia il link per scegliere la password.
    Route::post('/admin/hosts', [HostController::class, 'store'])->name('admin.hosts.store');
    // Reinvia un invito non ancora accettato.
    Route::post('/admin/hosts/{host}/invitation', [HostInvitationController::class, 'store'])->middleware('throttle:10,1')->name('admin.hosts.invitation');
    // Disattiva o riattiva l'host senza bloccare i soggiorni.
    Route::patch('/admin/hosts/{host}/status', [HostStatusController::class, 'update'])->name('admin.hosts.status');
});

// Applica proprietà dei dati e modalità in sola lettura a tutta l'area host.
Route::middleware(['auth', 'role:host', 'host.write'])
    ->where(['property' => '[0-9]+', 'room' => '[0-9]+', 'section' => '[0-9]+', 'stay' => '[0-9]+'])
    ->group(function (): void {
        // Elenca anche le strutture archiviate per consentirne il ripristino.
        Route::get('/host-home', [PropertyController::class, 'index'])->name('host-home');
        // Gestisce i dati principali delle strutture.
        Route::resource('/host/properties', PropertyController::class)->except(['index', 'destroy'])->names('host.properties');
        // Gestisce i dati delle stanze e le sezioni abilitate.
        Route::resource('/host/properties/{property}/rooms', RoomController::class)->except(['index', 'show', 'destroy'])->names('host.rooms');
        // Gestisce i contenuti informativi arricchiti.
        Route::resource('/host/properties/{property}/sections', SectionController::class)->except(['index', 'show'])->names('host.sections');
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
        // Revoca anticipatamente l'accesso di un soggiorno.
        Route::patch('/host/properties/{property}/rooms/{room}/stays/{stay}/revoke', [StayRevocationController::class, 'update'])->name('host.stays.revoke');
    });
