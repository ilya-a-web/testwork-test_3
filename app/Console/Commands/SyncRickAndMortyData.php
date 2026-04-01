<?php

namespace App\Console\Commands;

use App\Services\RickAndMorty\RickAndMortySyncService;
use Illuminate\Console\Command;

class SyncRickAndMortyData extends Command
{
    protected $signature = 'rickandmorty:sync';

    protected $description = 'Import and refresh characters, episodes and initial reviews from Rick and Morty API';

    public function handle(RickAndMortySyncService $syncService): int
    {
        $stats = $syncService->sync();

        $this->info(sprintf(
            'Synced: %d characters, %d episodes, %d reviews seeded.',
            $stats['characters'],
            $stats['episodes'],
            $stats['seeded_reviews'],
        ));

        return self::SUCCESS;
    }
}
