<?php

namespace App\Console\Commands;

use App\Models\CachedOffer;
use Illuminate\Console\Command;

class PruneOffers extends Command
{
    protected $signature = 'tc:prune-offers';

    protected $description = 'Delete expired cached offers and keep the search index lean.';

    public function handle(): int
    {
        $deleted = CachedOffer::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Pruned {$deleted} expired offers.");

        return self::SUCCESS;
    }
}
