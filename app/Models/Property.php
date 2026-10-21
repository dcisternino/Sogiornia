<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** Rappresenta una struttura appartenente a un solo host. */
#[Fillable(['name', 'city', 'address', 'welcome_message'])]
class Property extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Presentazione della struttura indipendente dalle copertine delle stanze. */
    public function profile(): HasOne
    {
        return $this->hasOne(PropertyProfile::class);
    }

    protected function casts(): array
    {
        return ['archived_at' => 'datetime', 'guide_enabled' => 'boolean'];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
