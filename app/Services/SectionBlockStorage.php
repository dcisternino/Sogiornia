<?php

namespace App\Services;

use App\Models\Section;
use App\Models\SectionBlock;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/** Salva blocchi, traduzioni e immagini private mantenendo coerente l'ordine nella sezione. */
class SectionBlockStorage
{
    public function __construct(private GuestTranslationStorage $translations) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, array<string, string|null>>  $translations
     */
    public function save(Section $section, SectionBlock $block, array $data, ?UploadedFile $image, bool $remove, array $translations): void
    {
        $oldPath = $block->image_path;
        $newPath = $image ? $image->store('section-block-images', 'local') : ($remove ? null : $oldPath);
        if ($image && $newPath === false) {
            throw new RuntimeException('Salvataggio immagine non riuscito.');
        }
        try {
            DB::transaction(function () use ($section, $block, $data, $newPath, $translations): void {
                Section::query()->whereKey($section->id)->lockForUpdate()->firstOrFail();
                $block->fill($data);
                $block->image_path = $newPath;
                if (! $block->exists) {
                    $block->position = ((int) $section->blocks()->max('position')) + 1;
                }
                $section->blocks()->save($block);
                $this->translations->save($block, $translations);
            }, 3);
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
