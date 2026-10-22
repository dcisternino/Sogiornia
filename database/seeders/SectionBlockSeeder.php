<?php

namespace Database\Seeders;

use App\Models\SectionBlock;
use App\Models\SectionBlockTranslation;
use Illuminate\Database\Seeder;

/** Genera un contenuto dimostrativo con traduzione soltanto quando richiamato esplicitamente. */
class SectionBlockSeeder extends Seeder
{
    public function run(): void
    {
        SectionBlock::factory()
            ->has(SectionBlockTranslation::factory(), 'translations')
            ->create();
    }
}
