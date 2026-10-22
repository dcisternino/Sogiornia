<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Inizializza solo le guide preesistenti, senza pubblicare dati di prova o nuove bozze. */
class GuideVersionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->call('sogiornia:initialize-guides');
    }
}
