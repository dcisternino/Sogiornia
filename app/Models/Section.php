<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** Conserva un contenuto informativo riutilizzabile nelle stanze della struttura. */
#[Fillable(['title', 'slug', 'content', 'image_alt', 'link_url', 'link_label', 'phone'])]
class Section extends Model
{
    use HasFactory;

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /** Le sezioni precedenti alla nuova tabella restano attive e non condivise. */
    public function settings(): HasOne
    {
        return $this->hasOne(SectionSetting::class);
    }

    /** Traduzioni facoltative dei contenuti della sezione. */
    public function translations(): HasMany
    {
        return $this->hasMany(SectionTranslation::class);
    }

    public function isActive(): bool
    {
        return $this->settings?->is_active ?? true;
    }

    public function isShared(): bool
    {
        return $this->settings?->is_shared ?? false;
    }

    /** Applica lo stesso criterio a home, dettagli e immagini, senza oltrepassare la struttura. */
    #[Scope]
    protected function visibleToRoom(Builder $query, Room $room): void
    {
        $query->where('property_id', $room->property_id)
            ->where(function (Builder $query): void {
                $query->whereDoesntHave('settings')->orWhereHas('settings', fn (Builder $query) => $query->where('is_active', true));
            })
            ->where(function (Builder $query) use ($room): void {
                $query->whereHas('settings', fn (Builder $query) => $query->where('is_shared', true))
                    ->orWhereHas('rooms', fn (Builder $query) => $query->whereKey($room->id));
            });
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class)->withTimestamps();
    }
}
