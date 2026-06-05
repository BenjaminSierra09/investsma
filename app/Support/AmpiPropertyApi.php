<?php

namespace App\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AmpiPropertyApi
{
    /**
     * Fetch property search results from the AMPI API.
     */
    public function search(array $params = []): ?array
    {
        return $this->rememberSuccessful(
            $this->cacheKey('property-search', $params),
            now()->addMinutes($this->searchCacheTtlMinutes()),
            fn (): ?array => $this->fetchJson('/api/v1/properties/search', $params),
        );
    }

    public function fetchProperty(string $mlsId): ?array
    {
        return $this->rememberSuccessful(
            $this->cacheKey('property-detail', ['mls_id' => $mlsId]),
            now()->addMinutes($this->propertyCacheTtlMinutes()),
            fn (): ?array => $this->fetchJson("/api/v1/property/mls/{$mlsId}"),
        );
    }

    public function fetchNeighborhoodOptions(array $filters = []): array
    {
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
}
