<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HostAccessRequest;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;

/** Imposta una scadenza valida fino a fine giornata italiana, senza bloccare le guide. */
class HostAccessController extends Controller
{
    public function update(HostAccessRequest $request, User $host): RedirectResponse
    {
        $date = $request->validated('expires_on');
        $host->hostAccess()->updateOrCreate([], [
            'expires_at' => $date ? CarbonImmutable::createFromFormat('!Y-m-d', $date, 'Europe/Rome')->addDay()->utc() : null,
        ]);

        return back()->with('status', 'Scadenza aggiornata. I soggiorni esistenti mantengono la propria validità.');
    }
}
