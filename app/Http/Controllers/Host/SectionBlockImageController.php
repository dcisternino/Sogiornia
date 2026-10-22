<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Serve la foto di un blocco soltanto al proprietario della struttura. */
class SectionBlockImageController extends Controller
{
    public function show(Property $property, int $section, int $block): BinaryFileResponse
    {
        Gate::authorize('view', $property);
        $section = $property->sections()->findOrFail($section);
        $block = $section->blocks()->findOrFail($block);
        abort_unless($block->image_path && Storage::disk('local')->exists($block->image_path), 404);

        return response()->file(Storage::disk('local')->path($block->image_path), [
            'Content-Type' => Storage::disk('local')->mimeType($block->image_path),
            'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store',
        ]);
    }
}
