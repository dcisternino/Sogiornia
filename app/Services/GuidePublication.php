<?php

namespace App\Services;

use App\Models\GuideVersion;
use App\Models\GuideWorkspace;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/** Gestisce baseline, concorrenza e pubblicazione atomica, preservando gli editor esistenti come bozza. */
class GuidePublication
{
    public function __construct(private GuideSnapshot $snapshots) {}

    public function lock(Property $property): GuideWorkspace
    {
        GuideWorkspace::query()->insertOrIgnore(['property_id' => $property->id, 'revision' => 0, 'lock_sequence' => 0, 'needs_initial_version' => false, 'created_at' => now(), 'updated_at' => now()]);
        GuideWorkspace::query()->where('property_id', $property->id)->increment('lock_sequence');
        $workspace = GuideWorkspace::query()->where('property_id', $property->id)->lockForUpdate()->firstOrFail();
        if ($workspace->needs_initial_version) {
            $version = $this->createVersion($property, 1, null);
            $workspace->published_version_id = $version->id;
            $workspace->needs_initial_version = false;
            $workspace->save();
        }

        return $workspace;
    }

    public function workspace(Property $property): GuideWorkspace
    {
        $workspace = GuideWorkspace::query()->where('property_id', $property->id)->first();
        if ($workspace && ! $workspace->needs_initial_version) {
            return $workspace;
        }

        return DB::transaction(fn (): GuideWorkspace => $this->lock($property), 3);
    }

    public function publish(Property $property, User $host, int $revision, ?int $baseVersion): GuideVersion
    {
        Gate::forUser($host)->authorize('update', $property);

        return DB::transaction(function () use ($property, $host, $revision, $baseVersion): GuideVersion {
            $workspace = $this->lock($property);
            abort_unless($workspace->revision === $revision && $workspace->published_version_id === $baseVersion, 409, 'La bozza o la versione pubblicata sono cambiate. Riapri la revisione prima di pubblicare.');
            $payload = $this->snapshots->capture($property);
            $published = $workspace->publishedVersion;
            if ($published && $published->checksum === $this->snapshots->checksum($payload)) {
                return $published;
            }
            $version = $this->createVersion($property, ($published?->number ?? 0) + 1, $host);
            $workspace->published_version_id = $version->id;
            $workspace->save();

            return $version;
        }, 3);
    }

    private function createVersion(Property $property, int $number, ?User $host): GuideVersion
    {
        $payload = $this->snapshots->capture($property);

        return $property->guideVersions()->create(['number' => $number, 'payload' => $payload, 'checksum' => $this->snapshots->checksum($payload), 'published_by' => $host?->id, 'published_at' => now()]);
    }
}
