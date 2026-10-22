<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Coordina la revisione della bozza e il puntatore alla sola versione pubblica. */
class GuideWorkspace extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['published_version_id' => 'integer', 'revision' => 'integer', 'needs_initial_version' => 'boolean'];
    }

    public function publishedVersion(): BelongsTo
    {
        return $this->belongsTo(GuideVersion::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
