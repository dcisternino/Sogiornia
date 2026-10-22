<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Section;

/** Aggiorna soltanto le lingue inviate e rimuove una traduzione quando tutti i suoi campi sono vuoti. */
class GuestTranslationStorage
{
    /** @param array<string, array<string, string|null>> $translations */
    public function save(Property|Section $owner, array $translations): void
    {
        $fields = $owner instanceof Property ? ['welcome_message'] : ['title', 'content', 'image_alt', 'link_label'];
        foreach ($translations as $locale => $values) {
            $data = array_intersect_key($values, array_flip($fields));
            if (collect($data)->every(fn (?string $value): bool => blank($value))) {
                $owner->translations()->where('locale', $locale)->delete();
            } else {
                $owner->translations()->updateOrCreate(['locale' => $locale], $data);
            }
        }
        $owner->unsetRelation('translations');
    }
}
