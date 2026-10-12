<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

/* Welcome Page */

Route::get('/', [PageController::class, 'welcome'])->name('welcome');

/* Admin Pages */
Route::get('admin-home', [PageController::class, 'home_admin'])->name('admin-home');

/* Host Pages */
Route::get('host-home', [PageController::class, 'host_admin'])->name('host-home');

/* Guest Pages */
Route::get('guest-home', [PageController::class, 'guest_admin'])->name('guest-home');
