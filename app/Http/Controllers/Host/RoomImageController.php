<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Serve la copertina soltanto all'host proprietario della stanza. */
class RoomImageController extends Controller
{
    public function show(Property $property, int $room): BinaryFileResponse
    {
        Gate::authorize('view', $property);
        $room = $property->rooms()->with('photo')->findOrFail($room);
        $path = $room->photo?->path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => Storage::disk('local')->mimeType($path),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
