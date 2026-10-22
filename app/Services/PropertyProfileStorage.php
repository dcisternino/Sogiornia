<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

/** Salva dati e copertina della struttura, rimuovendo i file sostituiti. */
class PropertyProfileStorage
{
    public function __construct(private GuestTranslationStorage $translations) {}

    public function save(Property $property, ?string $description, ?UploadedFile $image, bool $remove, array $translations = []): void
    {
        $oldPath = $property->exists ? $property->profile?->image_path : null;
        $newPath = $image ? $image->store('property-images', 'local') : ($remove ? null : $oldPath);
        if ($image && $newPath === false) {
            throw ValidationException::withMessages(['image' => 'Foto non salvata. Seleziona nuovamente il file e riprova.']);
        }
        try {
            DB::transaction(function () use ($property, $description, $newPath, $translations): void {
                $property->save();
                $property->profile()->updateOrCreate([], ['description' => $description, 'image_path' => $newPath]);
                $this->translations->save($property, $translations);
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
        $property->unsetRelation('profile');
    }
}
