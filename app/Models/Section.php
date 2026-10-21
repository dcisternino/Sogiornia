<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Conserva un contenuto informativo riutilizzabile nelle stanze della struttura. */
#[Fillable(['title', 'slug', 'content', 'image_alt', 'link_url', 'link_label', 'phone'])]
class Section extends Model
{
    use HasFactory;

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class)->withTimestamps();
    }
}
