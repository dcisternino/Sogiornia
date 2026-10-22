<?php

namespace App\Services;

use App\Models\Room;

/** Costruisce gli stessi contenuti e criteri di visibilità per guida ospite e anteprima privata. */
class GuestGuideData
{
    public function __construct(private GuideSnapshot $snapshots, private PublishedGuideContent $published) {}

    /** @return array<string, mixed> */
    public function forRoom(Room $room, ?string $token): array
    {
        $locale = app()->getLocale();
        $preview = $token === null;
        $raw = $preview ? $this->published->selectRoom($this->snapshots->capture($room->property), $room->id) : $this->published->forRoom($room);
        $property = $raw['property'];
        $roomData = $raw['room'];
        $welcome = $this->localized(collect($property['translations'])->firstWhere('locale', $locale), 'welcome_message', $property['welcome_message']);
        $sections = collect($raw['sections'])->mapWithKeys(function (array $section) use ($room, $token, $preview, $locale): array {
            $translation = collect($section['translations'])->firstWhere('locale', $locale);
            $title = $this->localized($translation, 'title', $section['title']);
            $content = $this->localized($translation, 'content', $section['content']);
            $alt = $this->localized($translation, 'image_alt', $section['image_alt']);
            $label = $this->localized($translation, 'link_label', $section['link_label']);
            $blocks = collect($section['blocks'])->map(function (array $block) use ($section, $room, $token, $preview, $locale): array {
                $translation = collect($block['translations'])->firstWhere('locale', $locale);
                $data = ['type' => $block['type'], 'phone' => $block['phone'], 'link_url' => $block['link_url'], 'address' => $block['address'], 'is_fallback' => false];
                foreach (['title', 'content', 'group_title', 'image_alt', 'link_label'] as $field) {
                    $localized = $this->localized($translation, $field, $block[$field]);
                    $data[$field] = $localized['value'];
                    if (($field !== 'image_alt' || $block['image_path']) && ($field !== 'link_label' || $block['link_url'])) {
                        $data['is_fallback'] = $data['is_fallback'] || $localized['fallback'];
                    }
                }
                $data['image_url'] = $block['image_path'] ? ($preview ? route('host.blocks.image', [$room->property_id, $section['id'], $block['id']]) : route('guide.blocks.image', ['token' => $token, 'section' => $section['slug'], 'block' => $block['id']])) : null;

                return $data;
            })->all();

            return [$section['slug'] => [
                'title' => $title['value'], 'content' => $content['value'], 'blocks' => $blocks,
                'url' => $preview ? route('host.preview.show', ['property' => $room->property_id, 'room' => $room, 'section' => $section['slug'], 'lang' => $locale]) : route('guide.show', ['token' => $token, 'section' => $section['slug'], 'lang' => $locale]),
                'image_url' => $section['image_path'] ? ($preview ? route('host.sections.image', [$room->property_id, $section['id']]) : route('guide.image', ['token' => $token, 'section' => $section['slug']])) : null,
                'image_alt' => $alt['value'], 'link_url' => $section['link_url'], 'link_label' => $label['value'], 'phone' => $section['phone'],
                'is_fallback' => $title['fallback'] || $content['fallback'] || ($section['image_path'] && $alt['fallback']) || ($section['link_url'] && $label['fallback']),
                'title_fallback' => $title['fallback'],
            ]];
        })->all();

        return [
            'propertyName' => $property['name'], 'propertyCity' => $property['city'],
            'welcomeMessage' => $welcome['value'], 'roomName' => $roomData['name'],
            'roomImageUrl' => $roomData['image_path'] ? ($preview ? route('host.rooms.image', [$room->property_id, $room]) : route('guide.room-image', $token)) : null,
            'token' => $token, 'isDemo' => false, 'isPreview' => $preview,
            'previewExitUrl' => route('host.rooms.index', $room->property_id),
            'homeUrl' => $preview ? route('host.preview.index', ['property' => $room->property_id, 'room' => $room, 'lang' => $locale]) : route('guide.index', ['token' => $token, 'lang' => $locale]),
            'hasFallback' => $welcome['fallback'] || collect($sections)->contains('title_fallback', true),
            'sections' => $sections,
        ];
    }

    /** @return array{value: string|null, fallback: bool} */
    private function localized(?array $translation, string $field, ?string $original): array
    {
        $value = app()->getLocale() === 'it' ? null : ($translation[$field] ?? null);

        return ['value' => filled($value) ? $value : $original, 'fallback' => app()->getLocale() !== 'it' && filled($original) && blank($value)];
    }
}
