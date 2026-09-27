<?php

namespace App\Console\Commands;

use App\Services\ProductSnapshotService;
use Illuminate\Console\Command;

class GenerateProductSnapshotsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-snapshots {--clear : Remove all existing snapshots without regenerating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate or clear static HTML snapshots for all active products for search engine indexing';

    /**
     * Execute the console command.
     */
    public function handle(ProductSnapshotService $snapshotService): int
    {
        if ($this->option('clear')) {
            $this->info('Clearing all product snapshots...');
            $cleared = $snapshotService->clearAll();
            $this->info("Successfully removed {$cleared} snapshot(s).");
            return Command::SUCCESS;
        }

        $this->info('Generating static HTML snapshots for active products...');
        $count = $snapshotService->generateAll();
        $this->info("Successfully generated snapshots for {$count} product(s).");

        return Command::SUCCESS;
    }
}
