<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/** Rappresenta un soggiorno e controlla la validità della sua guida a ogni richiesta. */
#[Fillable(['reference', 'starts_at', 'ends_at'])]
#[Hidden(['guide_token'])]
class Stay extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Stay $stay): void {
            $stay->guide_token = Str::random(64);
        });
    }

    protected function casts(): array
    {
        return ['starts_at' => 'immutable_datetime', 'ends_at' => 'immutable_datetime', 'revoked_at' => 'datetime'];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** Filtra il calendario senza confondere la revoca del link con le date del soggiorno. */
    #[Scope]
    protected function inPeriod(Builder $query, string $period): void
    {
        $instant = now();
        match ($period) {
            'past' => $query->where('ends_at', '<=', $instant),
            'scheduled' => $query->where('starts_at', '>', $instant),
            default => $query->where('starts_at', '<=', $instant)->where('ends_at', '>', $instant),
        };
    }

    public function isAccessible(): bool
    {
        return $this->revoked_at === null
            && now()->greaterThanOrEqualTo($this->starts_at)
            && now()->lessThan($this->ends_at)
            && $this->room->archived_at === null && $this->room->guide_enabled
            && $this->room->property->archived_at === null && $this->room->property->guide_enabled;
    }

    public function statusLabel(): string
    {
        if ($this->revoked_at !== null) {
            return 'Revocato';
        }
        if (now()->greaterThanOrEqualTo($this->ends_at)) {
            return 'Terminato';
        }
        if (now()->lessThan($this->starts_at)) {
            return 'In programma';
        }

        return $this->isAccessible() ? 'In corso' : 'Guida sospesa';
    }
}
