<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Indirizza ogni utente autenticato all'area del proprio ruolo. */
class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isHost(), 403);

        return redirect()->route($request->user()->isAdmin() ? 'admin-home' : 'host-home');
    }
}
