<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/** Conserva i file referenziati dallo storico e cancella gli altri soltanto dopo il commit. */
class GuideMedia
{
    public function paths(array $payload): array
    {
        $paths = [];
        array_walk_recursive($payload, function (mixed $value, string|int $key) use (&$paths): void {
            if ($key === 'image_path' && is_string($value) && $value !== '') {
                $paths[] = $value;
            }
        });

        return array_values(array_unique($paths));
    }

    public function historicalPaths(Property $property): array
    {
        $paths = [];
        foreach ($property->guideVersions()->select('id', 'payload')->lazyById(50) as $version) {
            $paths = [...$paths, ...$this->paths($version->payload)];
        }

        return array_values(array_unique($paths));
    }

    public function deleteUnused(Property $property, array $paths): void
    {
        $unused = array_values(array_diff(array_filter($paths), $this->historicalPaths($property)));
        if ($unused !== []) {
            DB::afterCommit(fn () => Storage::disk('local')->delete($unused));
        }
    }
}
