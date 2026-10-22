<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HostTranslationLimitRequest;
use App\Models\TranslationSetting;
use App\Models\User;
use App\Services\TranslationBudget;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/** Modifica soltanto il budget di traduzione dell'host, senza sospenderne le guide. */
class HostTranslationLimitController extends Controller
{
    public function edit(User $host, TranslationBudget $budget): View
    {
        abort_unless($host->isHost(), 404);
        $host->load('translationLimit');

        return view('pages.admin.monitoring.host', [
            'host' => $host, 'settings' => TranslationSetting::query()->findOrFail(1),
            'used' => (int) $host->translationUsages()->where('period', $budget->currentPeriod())->sum('budget_characters'),
        ]);
    }

    public function update(HostTranslationLimitRequest $request, User $host, TranslationBudget $budget): RedirectResponse
    {
        abort_unless($host->isHost(), 404);
        $data = $request->validated();
        if ($request->boolean('uses_default_limit')) {
            $data['monthly_character_limit'] = null;
        }
        DB::transaction(function () use ($budget, $host, $data): void {
            $budget->lockSettings();
            $host->translationLimit()->updateOrCreate([], $data);
        }, 3);

        return redirect()->route('admin.monitoring.index', ['host_id' => $host->id])->with('status', 'Limiti di traduzione dell’host aggiornati. Guide, soggiorni e traduzioni manuali restano disponibili.');
    }
}
