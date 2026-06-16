<?php

namespace App\Support;

use App\Models\AmpiProperty;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class AmpiPropertyApi
{
    /**
     * Fetch property search results from the AMPI API.
     */
    public function search(array $params = []): ?array
    {
        $localResults = $this->searchLocal($params);

        if (is_array($localResults)) {
            return $localResults;
        }

        return $this->searchRemote($params);
    }

    public function searchRemote(array $params = []): ?array
    {
        unset($params['sort']);

        return $this->rememberSuccessful(
            $this->cacheKey('property-search', $params),
            now()->addMinutes($this->searchCacheTtlMinutes()),
            fn (): ?array => $this->fetchJson('/api/v1/properties/search', $params),
        );
    }

    public function fetchProperty(string $mlsId): ?array
    {
        $property = $this->findLocalProperty($mlsId);

        if ($property instanceof AmpiProperty) {
            return $property->toAmpiArray();
        }

        return $this->fetchPropertyRemote($mlsId);
    }

    public function fetchPropertyRemote(string $mlsId): ?array
    {
        return $this->rememberSuccessful(
            $this->cacheKey('property-detail', ['mls_id' => $mlsId]),
            now()->addMinutes($this->propertyCacheTtlMinutes()),
            fn (): ?array => $this->fetchJson("/api/v1/property/mls/{$mlsId}"),
        );
    }

    public function fetchNeighborhoodOptions(array $filters = []): array
    {
        $localNeighborhoods = $this->fetchLocalNeighborhoodOptions($filters);

        if ($localNeighborhoods !== []) {
            return $localNeighborhoods;
        }

        if (! $this->isConfigured()) {
            return [];
        }

        $cacheKey = 'ampi.neighborhood-options.'.md5(json_encode($filters, JSON_THROW_ON_ERROR));

        return $this->normalizeNeighborhoodList(
            $this->rememberSuccessful($cacheKey, now()->addHours(6), function () use ($filters): ?array {
                $officialNeighborhoods = $this->fetchOfficialNeighborhoods();
                $observedNeighborhoods = $this->fetchObservedNeighborhoods($filters);

                if ($officialNeighborhoods === null && $observedNeighborhoods === null) {
                    return null;
                }

                return $this->mergeNeighborhoods(
                    $officialNeighborhoods ?? [],
                    $observedNeighborhoods ?? [],
                );
            }) ?? []
        );
    }

    public function isConfigured(): bool
    {
        return filled(config('services.ampi.api_key'));
    }

    public function hasLocalProperties(): bool
    {
        if (! $this->localStoreIsReady()) {
            return false;
        }

        return AmpiProperty::query()->active()->exists();
    }

    public function url(string $path): string
    {
        return rtrim((string) config('services.ampi.base_url'), '/').'/'.ltrim($path, '/');
    }

    private function fetchOfficialNeighborhoods(): ?array
    {
        $payload = $this->fetchJson('/api/v1/neighborhoods');

        if (! is_array($payload)) {
            return null;
        }

        return $this->normalizeNeighborhoodList(
            collect($payload)
                ->map(fn (mixed $item): ?string => is_array($item) ? ($item['name'] ?? null) : $item)
                ->values()
                ->all()
        );
    }

    private function fetchObservedNeighborhoods(array $filters = []): ?array
    {
        $page = 1;
        $lastPage = 1;
        $maxPages = 10;
        $neighborhoods = collect();
        $loadedAtLeastOnePage = false;

        while ($page <= $lastPage && $page <= $maxPages) {
            $payload = $this->search([
                ...$filters,
                'page' => $page,
                'per_page' => 100,
            ]);

            if (! is_array($payload)) {
                return $loadedAtLeastOnePage ? $neighborhoods->values()->all() : null;
            }

            $loadedAtLeastOnePage = true;
            $items = collect($payload['data'] ?? $payload);

            if ($items->isEmpty()) {
                break;
            }

            $neighborhoods = $neighborhoods->merge(
                $items
                    ->pluck('neighborhood')
                    ->map(fn (mixed $value): ?string => $this->normalizeNeighborhoodValue($value))
                    ->filter()
            );

            $lastPage = $this->resolveLastPage($payload, $page);
            $page++;
        }

        return $neighborhoods->values()->all();
    }

    private function mergeNeighborhoods(array $officialNeighborhoods, array $observedNeighborhoods): array
    {
        return collect($this->normalizeNeighborhoodList([...$officialNeighborhoods, ...$observedNeighborhoods]))
            ->unique(fn (string $item): string => Str::lower($item))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    private function normalizeNeighborhoodList(array $items): array
    {
        return collect($items)
            ->map(fn (mixed $item): ?string => $this->normalizeNeighborhoodValue($item))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeNeighborhoodValue(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = preg_replace('/\s+/u', ' ', trim($decoded));

        return filled($normalized) ? $normalized : null;
    }

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

    private function request(string $path, array $query = []): ?Response
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            return Http::withHeaders([
                'accept' => 'application/json',
                'x-api-key' => config('services.ampi.api_key'),
            ])
                ->connectTimeout($this->connectTimeoutSeconds())
                ->timeout($this->timeoutSeconds())
                ->retry($this->retryTimes(), $this->retrySleepMilliseconds(), throw: false)
                ->get($this->url($path), $query);
        } catch (Throwable $e) {
            Log::error('AMPI API exception', [
                'path' => $path,
                'query' => $query,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function fetchJson(string $path, array $query = []): ?array
    {
        $response = $this->request($path, $query);

        if (! $response || ! $response->successful()) {
            if ($response) {
                Log::warning('AMPI API request failed', [
                    'path' => $path,
                    'query' => $query,
                    'status' => $response->status(),
                ]);
            }

            return null;
        }

        $payload = $response->json();

        return is_array($payload) ? $payload : null;
    }

    private function rememberSuccessful(string $key, \DateTimeInterface $ttl, callable $resolver): ?array
    {
        $cachedValue = Cache::get($key);

        if (is_array($cachedValue)) {
            return $cachedValue;
        }

        $resolvedValue = $resolver();

        if (! is_array($resolvedValue)) {
            return null;
        }

        Cache::put($key, $resolvedValue, $ttl);

        return $resolvedValue;
    }

    private function cacheKey(string $prefix, array $params = []): string
    {
        ksort($params);

        return 'ampi.'.$prefix.'.'.md5(json_encode($params, JSON_THROW_ON_ERROR));
    }

    private function searchCacheTtlMinutes(): int
    {
        return max(1, (int) config('services.ampi.cache.search_ttl_minutes', 5));
    }

    private function propertyCacheTtlMinutes(): int
    {
        return max(1, (int) config('services.ampi.cache.property_ttl_minutes', 15));
    }

    private function connectTimeoutSeconds(): int
    {
        return max(1, (int) config('services.ampi.http.connect_timeout_seconds', 3));
    }

    private function timeoutSeconds(): int
    {
        return max(1, (int) config('services.ampi.http.timeout_seconds', 8));
    }

    private function retryTimes(): int
    {
        return max(1, (int) config('services.ampi.http.retry_times', 2));
    }

    private function retrySleepMilliseconds(): int
    {
        return max(0, (int) config('services.ampi.http.retry_sleep_milliseconds', 200));
    }

    private function searchLocal(array $params = []): ?array
    {
        if (! $this->hasLocalProperties()) {
            return null;
        }

        $perPage = max(1, min(100, (int) ($params['per_page'] ?? 25)));
        $currentPage = max(1, (int) ($params['page'] ?? 1));
        $query = AmpiProperty::query()->active();

        $this->applyLocalFilters($query, $params);
        $this->applyLocalSort($query, (string) ($params['sort'] ?? 'mls_desc'));

        $paginator = $query->paginate($perPage, ['*'], 'page', $currentPage);
        $items = $paginator->getCollection()
            ->map(fn (AmpiProperty $property): array => $property->toAmpiArray())
            ->values()
            ->all();

        return [
            'data' => $items,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function applyLocalFilters(Builder $query, array $params): void
    {
        $this->whereLike($query, 'name', $params['keywords'] ?? null, ['city', 'neighborhood', 'category']);
        $this->whereEquals($query, 'office_id', $params['office_id'] ?? null);
        $this->whereEquals($query, 'category', $params['category'] ?? null);
        $this->whereEquals($query, 'status', $params['status'] ?? null);
        $this->whereEquals($query, 'parking_type', $params['parking_type'] ?? null);
        $this->whereEquals($query, 'furnished', $params['furnished'] ?? null);
        $this->whereMinimum($query, 'bedrooms', $params['bedrooms'] ?? null);
        $this->whereMinimum($query, 'bathrooms', $params['bathrooms'] ?? null);
        $this->whereMinimum($query, 'floors', $params['floors'] ?? null);
        $this->whereMinimum($query, 'construction_meters', $params['construction_meters_min'] ?? null);
        $this->whereMaximum($query, 'construction_meters', $params['construction_meters_max'] ?? null);
        $this->whereMinimum($query, 'lot_meters', $params['lot_meters_min'] ?? null);
        $this->whereMaximum($query, 'lot_meters', $params['lot_meters_max'] ?? null);
        $this->whereBoolean($query, 'with_yard', $params['with_yard'] ?? null);
        $this->whereBoolean($query, 'pool', $params['pool'] ?? null);
        $this->whereBoolean($query, 'casita', $params['casita'] ?? null);
        $this->whereBoolean($query, 'gated_comm', $params['gated_comm'] ?? null);

        $neighborhoods = array_values(array_filter((array) ($params['neighborhood'] ?? [])));
        if ($neighborhoods !== []) {
            $query->whereIn('neighborhood', $neighborhoods);
        }

        $priceCurrency = $params['currency'] ?? 'USD';
        $rate = Currency::rateFor(is_string($priceCurrency) ? $priceCurrency : 'USD');

        if (is_numeric($params['price_min'] ?? null)) {
            $query->where('normalized_price', '>=', (float) $params['price_min'] * $rate);
        }

        if (is_numeric($params['price_max'] ?? null)) {
            $query->where('normalized_price', '<=', (float) $params['price_max'] * $rate);
        }
    }

    private function applyLocalSort(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query
                ->orderByRaw('normalized_price IS NULL')
                ->orderBy('normalized_price')
                ->orderByDesc('api_updated_at')
                ->orderByDesc('id'),
            'newest', 'mls_desc' => $query
                ->orderByRaw($this->mlsIdNumericOrderExpression().' DESC')
                ->orderByDesc('id'),
            default => $query
                ->orderByRaw($this->mlsIdNumericOrderExpression().' DESC')
                ->orderByDesc('id'),
        };
    }

    private function mlsIdNumericOrderExpression(): string
    {
        $numericMlsId = "REPLACE(REPLACE(mls_id, 'SMA-', ''), 'sma-', '')";

        if (AmpiProperty::query()->getConnection()->getDriverName() === 'mysql') {
            return "CAST({$numericMlsId} AS UNSIGNED)";
        }

        return "CAST({$numericMlsId} AS INTEGER)";
    }

    private function fetchLocalNeighborhoodOptions(array $filters = []): array
    {
        if (! $this->hasLocalProperties()) {
            return [];
        }

        $query = AmpiProperty::query()->active();
        $this->whereEquals($query, 'office_id', $filters['office_id'] ?? null);

        return $query
            ->whereNotNull('neighborhood')
            ->distinct()
            ->orderBy('neighborhood')
            ->pluck('neighborhood')
            ->filter()
            ->values()
            ->all();
    }

    private function findLocalProperty(string $mlsId): ?AmpiProperty
    {
        if (! $this->hasLocalProperties()) {
            return null;
        }

        return AmpiProperty::query()
            ->active()
            ->where('mls_id', $mlsId)
            ->first();
    }

    private function whereLike(Builder $query, string $column, mixed $value, array $extraColumns = []): void
    {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        $term = '%'.trim($value).'%';
        $query->where(function (Builder $query) use ($column, $extraColumns, $term): void {
            $query->where($column, 'like', $term);

            foreach ($extraColumns as $extraColumn) {
                $query->orWhere($extraColumn, 'like', $term);
            }
        });
    }

    private function whereEquals(Builder $query, string $column, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $query->where($column, $value);
    }

    private function whereMinimum(Builder $query, string $column, mixed $value): void
    {
        if (is_numeric($value)) {
            $query->where($column, '>=', $value);
        }
    }

    private function whereMaximum(Builder $query, string $column, mixed $value): void
    {
        if (is_numeric($value)) {
            $query->where($column, '<=', $value);
        }
    }

    private function whereBoolean(Builder $query, string $column, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $normalized = Str::lower((string) $value);

        if (in_array($normalized, ['yes', '1', 'true', 'si', 'sí'], true)) {
            $query->where($column, true);

            return;
        }

        if (in_array($normalized, ['no', '0', 'false'], true)) {
            $query->where($column, false);
        }
    }

    private function localStoreIsReady(): bool
    {
        try {
            return Schema::hasTable('ampi_properties');
        } catch (Throwable) {
            return false;
        }
    }
}
