<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Section;
use Illuminate\Database\Eloquent\Model;

/** Costruisce gli stessi contenuti e criteri di visibilità per guida ospite e anteprima privata. */
class GuestGuideData
{
    /** @return array<string, mixed> */
    public function forRoom(Room $room, ?string $token): array
    {
        $room->loadMissing(['property.translations', 'photo']);
        $locale = app()->getLocale();
        $preview = $token === null;
        $welcome = $this->localized($room->property->translations->firstWhere('locale', $locale), 'welcome_message', $room->property->welcome_message);
        $sections = Section::query()->visibleToRoom($room)->with('translations')->orderBy('title')->orderBy('id')->get()->mapWithKeys(function (Section $section) use ($room, $token, $preview, $locale): array {
            $translation = $section->translations->firstWhere('locale', $locale);
            $title = $this->localized($translation, 'title', $section->title);
            $content = $this->localized($translation, 'content', $section->content);
            $alt = $this->localized($translation, 'image_alt', $section->image_alt);
            $label = $this->localized($translation, 'link_label', $section->link_label);

            return [$section->slug => [
                'title' => $title['value'], 'content' => $content['value'],
                'url' => $preview ? route('host.preview.show', ['property' => $room->property_id, 'room' => $room, 'section' => $section->slug, 'lang' => $locale]) : route('guide.show', ['token' => $token, 'section' => $section->slug, 'lang' => $locale]),
                'image_url' => $section->image_path ? ($preview ? route('host.sections.image', [$room->property_id, $section]) : route('guide.image', ['token' => $token, 'section' => $section->slug])) : null,
                'image_alt' => $alt['value'], 'link_url' => $section->link_url, 'link_label' => $label['value'], 'phone' => $section->phone,
                'is_fallback' => $title['fallback'] || $content['fallback'] || ($section->image_path && $alt['fallback']) || ($section->link_url && $label['fallback']),
                'title_fallback' => $title['fallback'],
            ]];
        })->all();

        return [
            'propertyName' => $room->property->name, 'propertyCity' => $room->property->city,
            'welcomeMessage' => $welcome['value'], 'roomName' => $room->name,
            'roomImageUrl' => $room->photo ? ($preview ? route('host.rooms.image', [$room->property_id, $room]) : route('guide.room-image', $token)) : null,
            'token' => $token, 'isDemo' => false, 'isPreview' => $preview,
            'previewExitUrl' => route('host.rooms.index', $room->property_id),
            'homeUrl' => $preview ? route('host.preview.index', ['property' => $room->property_id, 'room' => $room, 'lang' => $locale]) : route('guide.index', ['token' => $token, 'lang' => $locale]),
            'hasFallback' => $welcome['fallback'] || collect($sections)->contains('title_fallback', true),
            'sections' => $sections,
        ];
    }

    /** @return array{value: string|null, fallback: bool} */
    private function localized(?Model $translation, string $field, ?string $original): array
    {
        $value = app()->getLocale() === 'it' ? null : $translation?->getAttribute($field);

        return ['value' => filled($value) ? $value : $original, 'fallback' => app()->getLocale() !== 'it' && filled($original) && blank($value)];
    }
}
