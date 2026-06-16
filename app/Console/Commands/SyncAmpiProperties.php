<?php

namespace App\Console\Commands;

use App\Services\AmpiPropertySyncService;
use Illuminate\Console\Command;

class SyncAmpiProperties extends Command
{
    protected $signature = 'ampi:sync-properties
        {--page=1 : Starting page}
        {--per-page=100 : Results per page}
        {--max-pages=25 : Maximum pages to sync}
        {--office-id= : Optional AMPI office id filter}
        {--deactivate-missing : Mark local properties missing from the sync as inactive}';

    protected $description = 'Sync AMPI MLS properties into the local database';

    public function handle(AmpiPropertySyncService $service): int
    {
        $result = $service->sync([
            'page' => (int) $this->option('page'),
            'per_page' => (int) $this->option('per-page'),
            'max_pages' => (int) $this->option('max-pages'),
            'office_id' => $this->option('office-id'),
            'deactivate_missing' => (bool) $this->option('deactivate-missing'),
        ]);

        $this->components->info("AMPI sync completed: {$result['synced']} properties across {$result['pages']} page(s).");

        return self::SUCCESS;
    }
}
