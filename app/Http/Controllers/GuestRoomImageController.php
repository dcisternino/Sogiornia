<?php

namespace App\Http\Controllers;

use App\Models\Stay;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Serve la foto della stanza esclusivamente durante un soggiorno con guida accessibile. */
class GuestRoomImageController extends Controller
{
    public function show(string $token): BinaryFileResponse
    {
        $stay = Stay::query()->where('guide_token', $token)->with(['room.property', 'room.photo'])->firstOrFail();
        abort_unless($stay->isAccessible(), 404);
        $path = $stay->room->photo?->path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => Storage::disk('local')->mimeType($path),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
