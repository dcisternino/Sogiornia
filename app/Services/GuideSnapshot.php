<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Room;
use App\Models\Section;
use App\Models\SectionBlock;

/** Serializza i contenuti senza URL, soggiorni o token, con formato esplicitamente versionato. */
class GuideSnapshot
{
    /** @return array{schema: int, property: array<string, mixed>, rooms: list<array<string, mixed>>, sections: list<array<string, mixed>>} */
    public function capture(Property $property): array
    {
        $property = $property->fresh()->load(['translations', 'profile', 'rooms.photo', 'rooms.sections', 'sections.settings', 'sections.translations', 'sections.blocks.translations']);

        return [
            'schema' => 1,
            'property' => $property->only(['name', 'city', 'address', 'welcome_message']) + ['description' => $property->profile?->description, 'image_path' => $property->profile?->image_path, 'translations' => $property->translations->sortBy('locale')->map->only(['locale', 'welcome_message'])->values()->all()],
            'rooms' => $property->rooms->sortBy('id')->map(fn (Room $room): array => $room->only(['id', 'name', 'description']) + ['image_path' => $room->photo?->path, 'section_ids' => $room->sections->pluck('id')->sort()->values()->all()])->values()->all(),
            'sections' => $property->sections->sortBy('id')->map(fn (Section $section): array => $section->only(['id', 'title', 'slug', 'content', 'image_path', 'image_alt', 'link_url', 'link_label', 'phone']) + [
                'is_active' => $section->isActive(), 'is_shared' => $section->isShared(),
                'translations' => $section->translations->sortBy('locale')->map->only(['locale', 'title', 'content', 'image_alt', 'link_label'])->values()->all(),
                'blocks' => $section->blocks->map(fn (SectionBlock $block): array => $block->only(['id', 'type', 'title', 'content', 'group_title', 'image_path', 'image_alt', 'link_url', 'link_label', 'phone', 'address', 'is_active', 'position']) + ['translations' => $block->translations->sortBy('locale')->map->only(['locale', 'title', 'content', 'group_title', 'image_alt', 'link_label'])->values()->all()])->values()->all(),
            ])->values()->all(),
        ];
    }

    public function checksum(array $payload): string
    {
        return hash('sha256', json_encode($this->canonical($payload), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /** Normalizza le chiavi associative: MySQL può riordinare gli oggetti delle colonne JSON. */
    private function canonical(array $value): array
    {
        if (! array_is_list($value)) {
            ksort($value);
        }
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->canonical($item);
            }
        }

        return $value;
    }
}
