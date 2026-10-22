<?php

namespace App\Models;

use Database\Factories\SectionBlockFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Conserva un singolo contenuto ordinabile all'interno della sezione. */
#[Fillable(['type', 'title', 'content', 'group_title', 'image_alt', 'link_url', 'link_label', 'phone', 'address', 'is_active'])]
class SectionBlock extends Model
{
    /** @use HasFactory<SectionBlockFactory> */
    use HasFactory;

    public const TYPES = ['text' => 'Testo', 'contact' => 'Contatto', 'link' => 'Collegamento', 'place' => 'Luogo'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'position' => 'integer'];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SectionBlockTranslation::class);
    }
}
