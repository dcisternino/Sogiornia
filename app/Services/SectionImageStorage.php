<?php

namespace App\Services;

use App\Models\Section;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/** Conserva le immagini fuori dalla cartella pubblica e ripulisce i file sostituiti. */
class SectionImageStorage
{
    public function save(Section $section, ?UploadedFile $image, bool $remove): void
    {
        $oldPath = $section->image_path;
        $newPath = $image ? $image->store('section-images', 'local') : ($remove ? null : $oldPath);
        if ($image && $newPath === false) {
            throw new RuntimeException('Salvataggio immagine non riuscito.');
        }
        try {
            $section->image_path = $newPath;
            $section->save();
        } catch (Throwable $exception) {
            if ($newPath && $newPath !== $oldPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $exception;
        }
        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }
    }
}
