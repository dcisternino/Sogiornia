<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Storico minimo delle richieste: non contiene testo originale, traduzione o chiavi. */
#[Fillable(['period', 'resource_type', 'locale', 'characters', 'budget_characters', 'status', 'error_code', 'provider_status', 'duration_ms'])]
class TranslationUsage extends Model
{
    use HasFactory;

    public const STATUSES = ['reserved' => 'In corso o interrotta', 'success' => 'Riuscita', 'failed' => 'Fallita', 'uncertain' => 'Esito incerto', 'blocked' => 'Bloccata prima di Google'];

    protected function casts(): array
    {
        return ['period' => 'immutable_date', 'characters' => 'integer', 'budget_characters' => 'integer', 'duration_ms' => 'integer', 'provider_status' => 'integer'];
    }

    /** Mantiene la chiave mensile come data pura anche su SQLite, senza aggiungere l'orario. */
    protected function period(): Attribute
    {
        return Attribute::make(set: fn (string|DateTimeInterface $value): string => CarbonImmutable::parse($value)->format('Y-m-d'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
