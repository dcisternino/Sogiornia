<?php

use App\Http\Controllers\Admin\HostController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestGuideController;
use App\Http\Controllers\Host\PropertyController;
use App\Http\Controllers\Host\RoomController;
use App\Http\Controllers\Host\RoomGuideLinkController;
use App\Http\Controllers\Host\SectionController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Presenta il servizio e l'offerta commerciale.
Route::get('/', [PageController::class, 'welcome'])->name('welcome');

// Mostra una guida dimostrativa priva di credenziali reali.
Route::get('/guest-home', [GuestGuideController::class, 'index'])->name('guest-home');
// Mostra il dettaglio di una sezione dimostrativa consentita.
Route::get('/guest/sections/{section}', [GuestGuideController::class, 'show'])->name('guest.section');

// Apre la guida tramite il link riservato della stanza.
Route::get('/g/{token}', [GuestGuideController::class, 'index'])->where('token', '[A-Za-z0-9]{64}')->middleware('guide.privacy')->name('guide.index');
// Mostra soltanto una sezione abilitata per la stanza del link.
Route::get('/g/{token}/sections/{section}', [GuestGuideController::class, 'show'])->where('token', '[A-Za-z0-9]{64}')->middleware('guide.privacy')->name('guide.show');

// Riserva il modulo di accesso ai visitatori non autenticati.
Route::middleware('guest')->group(function (): void {
    // Mostra il modulo di accesso condiviso da admin e host.
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    // Verifica le credenziali con limitazione dei tentativi.
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

// Protegge l'uscita e l'accesso alla propria dashboard.
Route::middleware('auth')->group(function (): void {
    // Termina la sessione corrente.
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    // Indirizza l'utente alla dashboard corrispondente al ruolo.
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

// Riserva all'amministratore la gestione degli account host.
Route::middleware(['auth', 'role:admin'])->group(function (): void {
    // Mostra la dashboard amministrativa e l'elenco degli host.
    Route::get('/admin-home', [HostController::class, 'index'])->name('admin-home');
    // Mostra il modulo per creare un host.
    Route::get('/admin/hosts/create', [HostController::class, 'create'])->name('admin.hosts.create');
    // Crea un account con il solo ruolo host.
    Route::post('/admin/hosts', [HostController::class, 'store'])->name('admin.hosts.store');
});

// Riserva agli host la gestione delle proprie strutture.
Route::middleware(['auth', 'role:host'])->group(function (): void {
    // Mostra la dashboard con le strutture dell'host.
    Route::get('/host-home', [PropertyController::class, 'index'])->name('host-home');
    // Gestisce la creazione e la modifica delle strutture possedute.
    Route::resource('/host/properties', PropertyController::class)->except(['index', 'destroy'])->names('host.properties');
    // Gestisce stanze e selezione delle sezioni visibili agli ospiti.
    Route::resource('/host/properties/{property}/rooms', RoomController::class)->except(['index', 'show', 'destroy'])->names('host.rooms');
    // Gestisce i contenuti informativi appartenenti alla struttura.
    Route::resource('/host/properties/{property}/sections', SectionController::class)->except(['index', 'show'])->names('host.sections');
    // Revoca il vecchio link della stanza e ne genera uno nuovo.
    Route::patch('/host/properties/{property}/rooms/{room}/guide-link', [RoomGuideLinkController::class, 'update'])->name('host.rooms.guide-link.update');
});
