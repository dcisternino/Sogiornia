<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/** Versione pubblicata immutabile: ogni nuova approvazione produce una nuova riga. */
#[Fillable(['number', 'payload', 'checksum', 'published_at', 'published_by'])]
class GuideVersion extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['payload' => 'array', 'number' => 'integer', 'published_at' => 'immutable_datetime'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Una versione pubblicata non può essere modificata.'));
        static::deleting(fn () => throw new LogicException('Lo storico si elimina soltanto insieme alla struttura.'));
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
