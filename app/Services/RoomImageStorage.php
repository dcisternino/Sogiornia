<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/** Salva stanza, sezioni e copertina insieme e ripulisce le immagini sostituite. */
class RoomImageStorage
{
    /** @param array<int, int|string> $sections */
    public function save(Room $room, ?UploadedFile $image, bool $remove, array $sections): void
    {
        $oldPath = $room->exists ? $room->photo?->path : null;
        $newPath = $image ? $image->store('room-images', 'local') : ($remove ? null : $oldPath);
        if ($image && $newPath === false) {
            throw new RuntimeException('Foto non salvata. Riprova scegliendo nuovamente il file.');
        }

        try {
            DB::transaction(function () use ($room, $sections, $newPath): void {
                $room->save();
                $room->sections()->sync($sections);
                if ($newPath) {
                    $room->photo()->updateOrCreate([], ['path' => $newPath]);
                } else {
                    $room->photo()->delete();
                }
            });
        } catch (Throwable $exception) {
            if ($newPath && $newPath !== $oldPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $exception;
        }

        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }
        $room->unsetRelation('photo');
    }
}
