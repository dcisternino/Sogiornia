<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Mostra l'immagine privata soltanto all'host proprietario. */
class SectionImageController extends Controller
{
    public function show(Property $property, int $section): BinaryFileResponse
    {
        Gate::authorize('view', $property);
        $section = $property->sections()->findOrFail($section);
        abort_unless($section->image_path && Storage::disk('local')->exists($section->image_path), 404);

        return response()->file(Storage::disk('local')->path($section->image_path), [
            'Content-Type' => Storage::disk('local')->mimeType($section->image_path),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
