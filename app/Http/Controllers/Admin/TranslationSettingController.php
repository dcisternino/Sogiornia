<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TranslationSettingRequest;
use App\Services\TranslationBudget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/** Aggiorna i limiti globali sotto lo stesso blocco usato per riservare i consumi. */
class TranslationSettingController extends Controller
{
    public function update(TranslationSettingRequest $request, TranslationBudget $budget): RedirectResponse
    {
        DB::transaction(function () use ($request, $budget): void {
            $settings = $budget->lockSettings();
            $settings->update($request->validated());
        }, 3);

        return redirect()->route('admin.monitoring.index')->with('status', 'Impostazioni di traduzione aggiornate. Le nuove soglie valgono subito; i consumi già registrati non vengono azzerati.');
    }
}
