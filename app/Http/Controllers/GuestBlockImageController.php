<?php

namespace App\Http\Controllers;

use App\Models\Stay;
use App\Services\PublishedGuideContent;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Protegge le immagini dei blocchi con validità del soggiorno e visibilità della sezione. */
class GuestBlockImageController extends Controller
{
    public function __construct(private PublishedGuideContent $published) {}

    public function show(string $token, string $section, int $block): BinaryFileResponse
    {
        $stay = Stay::query()->where('guide_token', $token)->with('room.property')->firstOrFail();
        abort_unless($stay->isAccessible(), 404);
        $section = $this->published->forRoom($stay->room)['sections'][$section] ?? null;
        abort_unless($section, 404);
        $block = collect($section['blocks'])->firstWhere('id', $block);
        abort_unless($block, 404);
        abort_unless($block['image_path'] && Storage::disk('local')->exists($block['image_path']), 404);

        return response()->file(Storage::disk('local')->path($block['image_path']), [
            'Content-Type' => Storage::disk('local')->mimeType($block['image_path']),
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
