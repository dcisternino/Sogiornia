<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/** Collega una stanza alla struttura e alle sezioni della sua guida. */
#[Fillable(['name', 'description'])]
#[Hidden(['guide_token'])]
class Room extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Room $room): void {
            $room->guide_token = Str::random(64);
        });
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
