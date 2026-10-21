<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Collega una stanza alla struttura e alle sezioni della sua guida. */
#[Fillable(['name', 'description'])]
class Room extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['archived_at' => 'datetime', 'guide_enabled' => 'boolean'];
    }

    public function stays(): HasMany
    {
        return $this->hasMany(Stay::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class)->withTimestamps();
    }
}
