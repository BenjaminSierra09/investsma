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
     * @return array{success: bool, synced: int, pages: int}
     */
    public function sync(array $options = []): array
    {
        $perPage = max(1, (int) ($options['per_page'] ?? config('services.ampi.sync.per_page', 100)));
        $maxPages = max(1, (int) ($options['max_pages'] ?? config('services.ampi.sync.max_pages', 25)));
        $page = max(1, (int) ($options['page'] ?? 1));
        $lastPage = $page;
        $synced = 0;
        $seenMlsIds = [];

        do {
            $payload = $this->ampiPropertyApi->searchRemote(array_filter([
                'office_id' => $options['office_id'] ?? config('services.ampi.sync.office_id'),
                'page' => $page,
                'per_page' => $perPage,
            ], fn (mixed $value): bool => $value !== null && $value !== ''));

            if (! is_array($payload)) {
                break;
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

            $lastPage = $this->resolveLastPage($payload, $page);
            $page++;
        } while ($page <= $lastPage && $page <= $maxPages);

        if (($options['deactivate_missing'] ?? false) && $seenMlsIds !== []) {
            AmpiProperty::query()
                ->whereNotIn('mls_id', $seenMlsIds)
                ->update(['is_active' => false]);
        }

        return [
            'success' => $synced > 0,
            'synced' => $synced,
            'pages' => max(0, $page - 1),
        ];
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
    private function resolveLastPage(array $payload, int $currentPage): int
    {
        return (int) (
            data_get($payload, 'meta.last_page')
            ?? data_get($payload, 'pagination.last_page')
            ?? data_get($payload, 'last_page')
            ?? data_get($payload, 'meta.total_pages')
            ?? data_get($payload, 'total_pages')
            ?? $currentPage
        );
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
