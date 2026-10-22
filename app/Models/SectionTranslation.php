<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Conserva le traduzioni facoltative dei contenuti visibili agli ospiti. */
#[Fillable(['locale', 'title', 'content', 'image_alt', 'link_label'])]
class SectionTranslation extends Model
{
    use HasFactory;

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
