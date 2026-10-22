<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Impostazioni uniche del budget di traduzione, protette dall'area amministrativa. */
#[Fillable(['is_enabled', 'monthly_character_limit', 'default_host_character_limit'])]
class TranslationSetting extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'monthly_character_limit' => 'integer', 'default_host_character_limit' => 'integer'];
    }
}
