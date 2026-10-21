<?php

namespace App\Http\Controllers;

use App\Models\Stay;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Serve immagini soltanto con soggiorno valido e sezione abilitata per la stanza. */
class GuestSectionImageController extends Controller
{
    public function show(string $token, string $section): BinaryFileResponse
    {
        $stay = Stay::query()->where('guide_token', $token)->with('room.property')->firstOrFail();
        abort_unless($stay->isAccessible(), 404);
        $section = $stay->room->sections()->where('property_id', $stay->room->property_id)->where('slug', $section)->firstOrFail();
        abort_unless($section->image_path && Storage::disk('local')->exists($section->image_path), 404);

        return response()->file(Storage::disk('local')->path($section->image_path), [
            'Content-Type' => Storage::disk('local')->mimeType($section->image_path),
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
