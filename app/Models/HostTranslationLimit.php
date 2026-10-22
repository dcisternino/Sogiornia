<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Personalizza le traduzioni dell'host senza modificarne l'accesso a strutture e soggiorni. */
#[Fillable(['is_enabled', 'uses_default_limit', 'monthly_character_limit'])]
class HostTranslationLimit extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'uses_default_limit' => 'boolean', 'monthly_character_limit' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
