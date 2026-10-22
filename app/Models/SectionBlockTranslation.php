<?php

namespace App\Models;

use Database\Factories\SectionBlockTranslationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Raccoglie le traduzioni facoltative del singolo blocco. */
#[Fillable(['locale', 'title', 'content', 'group_title', 'image_alt', 'link_label'])]
class SectionBlockTranslation extends Model
{
    /** @use HasFactory<SectionBlockTranslationFactory> */
    use HasFactory;

    public function sectionBlock(): BelongsTo
    {
        return $this->belongsTo(SectionBlock::class);
    }
}
