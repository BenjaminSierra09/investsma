<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;

class PropertiesController extends Controller
{
    /**
     * Display the MLS search form
     */
    public function index()
    {
        $neighborhoods = $this->getNeighborhoods();
        return view('properties', ['neighborhoods' => $neighborhoods]);
    }

    /**
     * Fetch neighborhoods from AMPI API
     */
    private function getNeighborhoods()
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
                    'neighborhoods' => $neighborhoods
                ]);
            } else {
                Log::error('AMPI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return back()->with('error', 'Error al buscar propiedades. Por favor, inténtalo de nuevo.');
            }
            
        } catch (\Exception $e) {
            Log::error('MLS Search Error: ' . $e->getMessage());
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
                $description = $locale === 'es' && !empty($property['description_short_es'])
                    ? $property['description_short_es']
                    : $property['description_short_en'];
                
                $description = Str::limit(strip_tags($description), 160, '...');
                $title = $property['name'];
                
                SEOMeta::setTitle($title)
                    ->setDescription($description)
                    ->addKeyword([$title, $property['category'], $property['neighborhood'], $property['city'], 'San Miguel de Allende', 'real estate', 'property'])
                    ->setCanonical(request()->url())
                    ->addMeta('robots', 'index,follow');

                OpenGraph::setTitle($title)
                    ->setDescription($description)
                    ->setUrl(request()->url())
                    ->setSiteName(config('app.name'))
                    ->addProperty('type', 'article')
                    ->addProperty('locale', app()->getLocale())
                    ->addProperty('site_name', config('app.name'));
                
                // Add property images to OpenGraph
                if (isset($property['photos']) && count($property['photos']) > 0) {
                    foreach (array_slice($property['photos'], 0, 4) as $photo) {
                        OpenGraph::addImage([
                            'url' => $photo,
                            'width' => 1200,
                            'height' => 630,
                            'alt' => $title
                        ]);
                    }
                } else {
                    OpenGraph::addImage([
                        'url' => asset('images/placeholder-property.jpg'),
                        'width' => 1200,
                        'height' => 630,
                        'alt' => $title
                    ]);
                }
                
                return view('public.properties', compact('property'));
            } else {
                return redirect()->route('properties.index')
                    ->with('error', 'Propiedad no encontrada.');
            }
            
        } catch (\Exception $e) {
            Log::error('MLS Property Detail Error: ' . $e->getMessage());
            return redirect()->route('properties.index')
                ->with('error', 'Error al cargar los detalles de la propiedad.');
        }
    }
}
