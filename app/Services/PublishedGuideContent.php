<?php

namespace App\Services;

use App\Models\Room;

/** Espone soltanto la versione approvata, senza leggere i contenuti in bozza. */
class PublishedGuideContent
{
    public function __construct(private GuidePublication $publication) {}

    /** @return array{property: array<string, mixed>, room: array<string, mixed>, sections: array<string, array<string, mixed>>} */
    public function forRoom(Room $room): array
    {
        $version = $this->publication->workspace($room->property)->publishedVersion;
        abort_unless($version, 404, 'La guida non è ancora stata pubblicata.');

        return $this->selectRoom($version->payload, $room->id);
    }

    /** @return array{property: array<string, mixed>, room: array<string, mixed>, sections: array<string, array<string, mixed>>} */
    public function selectRoom(array $payload, int $roomId): array
    {
        $room = collect($payload['rooms'])->firstWhere('id', $roomId);
        abort_unless($room, 404, 'Questa stanza non è presente nella guida pubblicata.');
        $sections = collect($payload['sections'])->filter(fn (array $section): bool => $section['is_active'] && ($section['is_shared'] || in_array($section['id'], $room['section_ids'], true)))->map(function (array $section): array {
            $section['blocks'] = array_values(array_filter($section['blocks'], fn (array $block): bool => (bool) $block['is_active']));

            return $section;
        })->sortBy('title')->keyBy('slug')->all();

        return ['property' => $payload['property'], 'room' => $room, 'sections' => $sections];
    }
}
