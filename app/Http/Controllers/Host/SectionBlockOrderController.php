<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Section;
use App\Models\SectionBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Sposta un blocco di una posizione oppure ne cambia la visibilità. */
class SectionBlockOrderController extends Controller
{
    public function update(Request $request, Property $property, int $section, int $block): RedirectResponse
    {
        Gate::authorize('update', $property);
        $data = $request->validate(['action' => ['required', Rule::in(['up', 'down', 'toggle'])]]);
        $section = $property->sections()->findOrFail($section);
        DB::transaction(function () use ($section, $block, $data): void {
            Section::query()->whereKey($section->id)->lockForUpdate()->firstOrFail();
            $blocks = $section->blocks()->lockForUpdate()->get();
            $current = $blocks->firstWhere('id', $block);
            abort_unless($current, 404);
            if ($data['action'] === 'toggle') {
                $current->update(['is_active' => ! $current->is_active]);

                return;
            }
            $index = $blocks->search(fn (SectionBlock $item): bool => $item->id === $current->id);
            $target = $index + ($data['action'] === 'up' ? -1 : 1);
            if ($target < 0 || $target >= $blocks->count()) {
                return;
            }
            $items = $blocks->all();
            [$items[$index], $items[$target]] = [$items[$target], $items[$index]];
            foreach ($items as $position => $item) {
                $item->position = $position;
                $item->save();
            }
        }, 3);

        return back()->with('status', $data['action'] === 'toggle' ? 'Visibilità in bozza aggiornata. Pubblica per applicarla agli ospiti.' : 'Ordine in bozza aggiornato. Pubblica per applicarlo agli ospiti.');
    }
}
