<?php

namespace App\Jobs;

use App\Services\AmpiPropertySyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class SyncAmpiPropertiesJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 1800;

    public int $tries = 2;

    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(public array $options = [])
    {
        $this->onQueue('default');
    }

    /** @return list<object> */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('ampi-properties-sync'))
                ->shared()
                ->releaseAfter(300)
                ->expireAfter($this->timeout + 300),
        ];
    }

    public function handle(AmpiPropertySyncService $service): void
    {
        $service->sync($this->options);
    }
}
