<?php

namespace App\Services;

use App\Models\AmpiProperty;
use App\Models\Currency;
use App\Support\AmpiPropertyApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AmpiPropertySyncService
{
    public function __construct(private readonly AmpiPropertyApi $ampiPropertyApi) {}

    /**
     * @param  array<string, mixed>  $options
     * @return array{success: bool, complete: bool, synced: int, pages: int, deleted: int, remote_total: ?int}
     */
    public function sync(array $options = []): array
    {
        $perPage = max(1, (int) ($options['per_page'] ?? config('services.ampi.sync.per_page', 100)));
        $maxPages = max(1, (int) ($options['max_pages'] ?? config('services.ampi.sync.max_pages', 25)));
        $page = max(1, (int) ($options['page'] ?? 1));
        $startingPage = $page;
        $synced = 0;
        $pages = 0;
        $deleted = 0;
        $complete = false;
        $remoteTotal = null;
        $catalogTotalIsStable = true;
        $seenMlsIds = [];

        while ($pages < $maxPages) {
            $payload = $this->ampiPropertyApi->searchRemote(array_filter([
                'office_id' => $options['office_id'] ?? config('services.ampi.sync.office_id'),
                'page' => $page,
                'per_page' => $perPage,
            ], fn (mixed $value): bool => $value !== null && $value !== ''));

            if (! is_array($payload)) {
                break;
            }

            $pages++;
            $payloadTotal = $this->resolveTotal($payload);

            if ($payloadTotal !== null) {
                if ($remoteTotal !== null && $remoteTotal !== $payloadTotal) {
                    $catalogTotalIsStable = false;
                }

                $remoteTotal ??= $payloadTotal;
            }

            $items = collect($payload['data'] ?? $payload)
                ->filter(fn (mixed $item): bool => is_array($item));

            foreach ($items as $item) {
                $property = $this->syncProperty($item);

                if ($property !== null) {
                    $seenMlsIds[] = $property->mls_id;
                    $synced++;
                }
            }

            $lastPage = $this->resolveLastPage($payload);

            if ($lastPage === null) {
                break;
            }

            if ($page >= $lastPage) {
                $complete = true;

                break;
            }

            $page++;
        }

        $seenMlsIds = array_values(array_unique($seenMlsIds));
        $deleteMissing = (bool) ($options['delete_missing'] ?? false);
        $catalogCountMatches = $remoteTotal !== null && count($seenMlsIds) === $remoteTotal;
        $safeToDelete = $startingPage === 1 && $catalogTotalIsStable && $catalogCountMatches;
        $success = $complete && $seenMlsIds !== [] && (! $deleteMissing || $safeToDelete);

        if ($deleteMissing && $success) {
            $missingProperties = AmpiProperty::query()
                ->whereNotIn('mls_id', $seenMlsIds);

            $officeId = $options['office_id'] ?? config('services.ampi.sync.office_id');

            if (filled($officeId)) {
                $missingProperties->where('office_id', $officeId);
            }

            $deleted = $missingProperties->delete();
        }

        $result = [
            'success' => $success,
            'complete' => $complete,
            'synced' => $synced,
            'pages' => $pages,
            'deleted' => $deleted,
            'remote_total' => $remoteTotal,
        ];

        Log::log($success ? 'info' : 'warning', 'AMPI property sync finished.', $result);

        return $result;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function syncProperty(array $payload): ?AmpiProperty
    {
        $mlsId = trim((string) ($payload['mls_id'] ?? ''));

        if ($mlsId === '') {
            Log::warning('AMPI property skipped because mls_id is missing.', [
                'keys' => array_keys($payload),
            ]);

            return null;
        }

        $price = $this->decimal($payload['price'] ?? null);
        $currency = strtoupper(trim((string) ($payload['currency'] ?? 'USD'))) ?: 'USD';

        return AmpiProperty::query()->updateOrCreate(
            ['mls_id' => $mlsId],
            [
                'external_id' => $this->integer($payload['id'] ?? null),
                'name' => $payload['name'] ?? $payload['title'] ?? null,
                'slug' => Str::slug((string) ($payload['name'] ?? $payload['title'] ?? $mlsId)),
                'status' => $payload['status'] ?? null,
                'category' => $payload['category'] ?? $payload['property_type'] ?? null,
                'office_id' => $this->integer($payload['office_id'] ?? null),
                'city' => $payload['city'] ?? null,
                'neighborhood' => $payload['neighborhood'] ?? null,
                'price' => $price,
                'currency' => $currency,
                'normalized_price' => $price !== null ? $price * Currency::rateFor($currency) : null,
                'bedrooms' => $this->integer($payload['bedrooms'] ?? null),
                'bathrooms' => $this->decimal($payload['bathrooms'] ?? null),
                'floors' => $this->integer($payload['floors'] ?? null),
                'construction_meters' => $this->decimal($payload['construction_meters'] ?? null),
                'lot_meters' => $this->decimal($payload['lot_meters'] ?? null),
                'furnished' => $payload['furnished'] ?? null,
                'parking_type' => $payload['parking_type'] ?? null,
                'with_yard' => $this->boolean($payload['with_yard'] ?? null),
                'pool' => $this->boolean($payload['pool'] ?? null),
                'casita' => $this->boolean($payload['casita'] ?? null),
                'gated_comm' => $this->boolean($payload['gated_comm'] ?? null),
                'latitude' => $this->decimal($payload['latitude'] ?? $payload['lat'] ?? null),
                'longitude' => $this->decimal($payload['longitude'] ?? $payload['lng'] ?? null),
                'featured_image' => $payload['featured_image'] ?? ($payload['photos'][0] ?? null),
                'photos' => is_array($payload['photos'] ?? null) ? $payload['photos'] : [],
                'raw_payload' => $payload,
                'api_created_at' => $this->dateTime($payload['created_at'] ?? null),
                'api_updated_at' => $this->dateTime($payload['updated_at'] ?? null),
                'last_synced_at' => now(),
                'is_active' => true,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function resolveLastPage(array $payload): ?int
    {
        $lastPage = (
            data_get($payload, 'meta.last_page')
            ?? data_get($payload, 'pagination.last_page')
            ?? data_get($payload, 'last_page')
            ?? data_get($payload, 'meta.total_pages')
            ?? data_get($payload, 'total_pages')
        );

        return is_numeric($lastPage) ? max(1, (int) $lastPage) : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function resolveTotal(array $payload): ?int
    {
        $total = data_get($payload, 'meta.total')
            ?? data_get($payload, 'pagination.total')
            ?? data_get($payload, 'total');

        return is_numeric($total) ? max(0, (int) $total) : null;
    }

    private function integer(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function decimal(mixed $value): ?float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $number = preg_replace('/[^\d.]/', '', str_replace(',', '', $value));

        return is_numeric($number) ? (float) $number : null;
    }

    private function boolean(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        return in_array(Str::lower((string) $value), ['1', 'true', 'yes', 'si', 'sí'], true);
    }

    private function dateTime(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
