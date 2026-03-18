<?php

namespace App\Http\Controllers;

use App\Support\SeoData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PropertiesController extends Controller
{
    /**
     * Display the MLS search form
     */
    public function index(): View
    {
        $neighborhoods = $this->getNeighborhoods();

        return view('properties', ['neighborhoods' => $neighborhoods]);
    }

    public function map(Request $request): View
    {
        return view('public.properties-map', [
            'properties' => $this->fetchPropertiesForMap($request),
            'filters' => $request->query(),
        ]);
    }

    /**
     * Fetch neighborhoods from AMPI API
     */
    private function getNeighborhoods(): array
    {
        try {
            $apiKey = config('services.ampi.api_key');
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'x-api-key' => $apiKey,
            ])->get('https://ampisanmigueldeallende.com/api/v1/neighborhoods');

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch neighborhoods', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Search MLS properties via API
     */
    public function search(Request $request)
    {
        try {
            // Build query parameters
            $params = $this->buildQueryParams($request);

            // Get API configuration
            $apiKey = config('services.ampi.api_key');
            $baseUrl = 'https://ampisanmigueldeallende.com/api/v1/properties/search';

            // Make API request
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'x-api-key' => $apiKey,
            ])->get($baseUrl, $params);

            if ($response->successful()) {
                $results = $response->json();
                $neighborhoods = $this->getNeighborhoods();

                return view('properties', [
                    'results' => $results,
                    'searchParams' => $params,
                    'neighborhoods' => $neighborhoods,
                ]);
            } else {
                Log::error('AMPI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return back()->with('error', 'Error al buscar propiedades. Por favor, inténtalo de nuevo.');
            }

        } catch (\Exception $e) {
            Log::error('MLS Search Error: '.$e->getMessage());

            return back()->with('error', 'Ocurrió un error al procesar tu búsqueda.');
        }
    }

    /**
     * Build query parameters from request
     */
    private function buildQueryParams(Request $request): array
    {
        $params = [];

        // Multi-select fields - convert arrays to comma-separated strings
        if ($request->filled('office_id')) {
            $params['office_id'] = is_array($request->office_id)
                ? implode(',', $request->office_id)
                : $request->office_id;
        }

        if ($request->filled('neighborhood')) {
            $params['neighborhood'] = is_array($request->neighborhood)
                ? implode(',', $request->neighborhood)
                : $request->neighborhood;
        }

        if ($request->filled('category')) {
            $params['category'] = is_array($request->category)
                ? implode(',', $request->category)
                : $request->category;
        }

        if ($request->filled('status')) {
            $params['status'] = is_array($request->status)
                ? implode(',', $request->status)
                : $request->status;
        }

        // Single value fields
        $singleFields = [
            'currency',
            'price_min',
            'price_max',
            'floors',
            'construction_meters_min',
            'construction_meters_max',
            'lot_meters_min',
            'lot_meters_max',
            'bathrooms',
            'bedrooms',
            'furnished',
            'parking_type',
            'with_yard',
            'pool',
            'casita',
            'gated_comm',
        ];

        foreach ($singleFields as $field) {
            if ($request->filled($field)) {
                $params[$field] = $request->input($field);
            }
        }

        // Pagination
        $params['page'] = $request->input('page', 1);
        $params['per_page'] = $request->input('per_page', 25);

        return $params;
    }

    private function fetchPropertiesForMap(Request $request): array
    {
        $apiKey = config('services.ampi.api_key');
        if (! $apiKey) {
            return [];
        }

        $params = $this->buildQueryParams($request);
        $params['per_page'] = (int) $request->integer('per_page', 100);
        $page = 1;
        $lastPage = 1;
        $maxPages = max(1, (int) $request->integer('map_pages', 10));
        $properties = collect();

        try {
            while ($page <= $lastPage && $page <= $maxPages) {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'x-api-key' => $apiKey,
                ])->get('https://ampisanmigueldeallende.com/api/v1/properties/search', [
                    ...$params,
                    'page' => $page,
                ]);

                if (! $response->successful()) {
                    Log::error('AMPI map API error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'page' => $page,
                    ]);

                    break;
                }

                $payload = $response->json();
                $items = collect($payload['data'] ?? $payload)
                    ->map(fn (array $property): ?array => $this->normalizePropertyForMap($property))
                    ->filter();

                $properties = $properties->merge($items);

                if ($items->isEmpty()) {
                    break;
                }

                $lastPage = $this->resolveMapLastPage($payload, $page);
                $page++;
            }

            return $properties
                ->unique('id')
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::error('AMPI map API exception', ['message' => $e->getMessage()]);

            return [];
        }
    }

    private function resolveMapLastPage(array $payload, int $currentPage): int
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

    private function normalizePropertyForMap(array $property): ?array
    {
        $latitude = $property['latitude'] ?? $property['lat'] ?? null;
        $longitude = $property['longitude'] ?? $property['lng'] ?? null;

        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return null;
        }

        $detailMlsId = $property['mls_id'] ?? $property['id'] ?? null;
        if (! $detailMlsId) {
            return null;
        }

        return [
            'id' => $detailMlsId,
            'name' => $property['name'] ?? 'Propiedad',
            'slug' => Str::slug($property['name'] ?? 'propiedad'),
            'latitude' => (float) $latitude,
            'longitude' => (float) $longitude,
            'price' => isset($property['price']) ? (float) $property['price'] : null,
            'currency' => $property['currency'] ?? 'USD',
            'status' => $property['status'] ?? null,
            'city' => $property['city'] ?? null,
            'neighborhood' => $property['neighborhood'] ?? null,
            'bedrooms' => $property['bedrooms'] ?? null,
            'bathrooms' => $property['bathrooms'] ?? null,
            'construction_meters' => $property['construction_meters'] ?? null,
            'image' => $property['featured_image'] ?? ($property['photos'][0] ?? null),
            'detail_url' => route('properties.show', [
                'mlsId' => $detailMlsId,
                'slug' => Str::slug($property['name'] ?? 'propiedad'),
            ]),
        ];
    }

    /**
     * Get property details
     */
    public function show($mlsId, $slug = null)
    {
        try {
            $apiKey = config('services.ampi.api_key');
            $baseUrl = "https://ampisanmigueldeallende.com/api/v1/property/mls/{$mlsId}";

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'x-api-key' => $apiKey,
            ])->get($baseUrl);

            if ($response->successful()) {
                $property = $response->json();

                // Generate slug from property name
                $propertySlug = Str::slug($property['name']);

                // Redirect to correct URL if slug is missing or incorrect
                if ($slug !== $propertySlug) {
                    return redirect()->route('properties.show', ['mlsId' => $mlsId, 'slug' => $propertySlug], 301);
                }

                // Set SEO metadata
                $locale = app()->getLocale();
                $description = $locale === 'es' && ! empty($property['description_short_es'])
                    ? $property['description_short_es']
                    : $property['description_short_en'];

                $description = Str::limit(strip_tags($description), 160, '...');
                $title = $property['name'];

                SeoData::apply(
                    title: $title.' | investsma',
                    description: $description,
                    keywords: [$title, $property['category'] ?? null, $property['neighborhood'] ?? null, $property['city'] ?? null, 'San Miguel de Allende', 'real estate', 'property'],
                    image: $property['photos'][0] ?? asset('logotipo.png'),
                    type: 'article',
                    schemaType: 'Product',
                );

                return view('public.properties', compact('property'));
            } else {
                return redirect()->route('properties.index')
                    ->with('error', 'Propiedad no encontrada.');
            }

        } catch (\Exception $e) {
            Log::error('MLS Property Detail Error: '.$e->getMessage());

            return redirect()->route('properties.index')
                ->with('error', 'Error al cargar los detalles de la propiedad.');
        }
    }
}
