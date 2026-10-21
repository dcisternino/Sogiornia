<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Controlla pubblicazione e condivisione della sezione nelle stanze della struttura. */
#[Fillable(['is_active', 'is_shared'])]
class SectionSetting extends Model
{
    use HasFactory;

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_shared' => 'boolean'];
    }
}
