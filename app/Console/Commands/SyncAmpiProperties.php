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
        {--delete-missing : Permanently delete local properties missing from a complete sync}';

    protected $description = 'Sync AMPI MLS properties into the local database';

    public function handle(AmpiPropertySyncService $service): int
    {
        $result = $service->sync([
            'page' => (int) $this->option('page'),
            'per_page' => (int) $this->option('per-page'),
            'max_pages' => (int) $this->option('max-pages'),
            'office_id' => $this->option('office-id'),
            'delete_missing' => (bool) $this->option('delete-missing'),
        ]);

        if (! $result['success']) {
            $this->components->error(
                "AMPI sync incomplete: {$result['synced']} properties across {$result['pages']} page(s). Missing properties were preserved."
            );

            return self::FAILURE;
        }

        $this->components->info(
            "AMPI sync completed: {$result['synced']} properties across {$result['pages']} page(s); {$result['deleted']} missing properties permanently deleted."
        );

        return self::SUCCESS;
    }
}
