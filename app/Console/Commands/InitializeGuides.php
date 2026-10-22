<?php

namespace App\Console\Commands;

use App\Models\GuideWorkspace;
use App\Services\GuidePublication;
use Illuminate\Console\Command;

/** Conserva come versione iniziale soltanto le guide preesistenti alla migrazione. */
class InitializeGuides extends Command
{
    protected $signature = 'sogiornia:initialize-guides';

    protected $description = 'Inizializza le versioni delle guide già esistenti, senza pubblicare nuove bozze';

    public function handle(GuidePublication $publication): int
    {
        $count = 0;
        foreach (GuideWorkspace::query()->where('needs_initial_version', true)->with('property')->lazyById(100) as $workspace) {
            $publication->workspace($workspace->property);
            $count++;
        }
        $this->info('Guide inizializzate: '.$count.'.');

        return self::SUCCESS;
    }
}
