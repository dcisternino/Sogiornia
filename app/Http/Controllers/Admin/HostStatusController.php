<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Disattiva o riattiva un host mantenendo accessibili i soggiorni esistenti. */
class HostStatusController extends Controller
{
    public function update(Request $request, User $host): RedirectResponse
    {
        abort_unless($host->isHost(), 404);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $host->is_active = $data['is_active'];
        $host->save();

        return back()->with('status', $host->is_active ? 'Host riattivato.' : 'Host in sola lettura. I soggiorni esistenti restano validi.');
    }
}
