<?php

namespace App\Livewire\Public;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PropertiesSearch extends Component
{
    public array $neighborhoods = [];
    public array $results = [];
    public array $searchParams = [];
    public ?string $errorMessage = null;

    protected $queryString = [
        'office_id' => ['except' => ''],
        'neighborhood' => ['except' => []],
        'category' => ['except' => ''],
        'status' => ['except' => ''],
        'currency' => ['except' => null],
        'price_min' => ['except' => null],
        'price_max' => ['except' => null],
        'floors' => ['except' => null],
        'construction_meters_min' => ['except' => null],
        'construction_meters_max' => ['except' => null],
        'lot_meters_min' => ['except' => null],
        'lot_meters_max' => ['except' => null],
        'bathrooms' => ['except' => null],
        'bedrooms' => ['except' => null],
        'furnished' => ['except' => null],
        'parking_type' => ['except' => null],
        'with_yard' => ['except' => null],
        'pool' => ['except' => null],
        'casita' => ['except' => null],
        'gated_comm' => ['except' => null],
        'page' => ['except' => 1],
        'perPage' => ['except' => 25],
    ];

    public string $office_id = '';
    public $neighborhood = [];
    public string $category = '';
    public string $status = '';

    public ?string $currency = null;
    public ?string $price_min = null;
    public ?string $price_max = null;
    public ?string $floors = null;
    public ?string $construction_meters_min = null;
    public ?string $construction_meters_max = null;
    public ?string $lot_meters_min = null;
    public ?string $lot_meters_max = null;
    public ?string $bathrooms = null;
    public ?string $bedrooms = null;
    public ?string $furnished = null;
    public ?string $parking_type = null;
    public ?string $with_yard = null;
    public ?string $pool = null;
    public ?string $casita = null;
    public ?string $gated_comm = null;

    public int $page = 1;
    public int $perPage = 25;

    public function mount(): void
    {
        $this->neighborhoods = $this->getNeighborhoods();
        $this->hydrateFromQuery(request()->all());
        $this->search();
    }

    public function updating($name, $value): void
    {
        if ($name !== 'page') {
            $this->page = 1;
        }
    }

    public function updatedPage(): void
    {
        $this->search();
    }

    public function nextPage(): void
    {
        $this->page++;
        $this->search();
    }

    public function prevPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->search();
        }
    }

    public function search(): void
    {
        $this->errorMessage = null;
        $params = $this->buildQueryParams();

        $apiKey = config('services.ampi.api_key');
        if (! $apiKey) {
            $this->errorMessage = 'Falta configurar la API key de AMPI.';
            $this->results = [];
            return;
        }

        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'x-api-key' => $apiKey,
            ])->get('https://ampisanmigueldeallende.com/api/v1/properties/search', $params);

            if ($response->successful()) {
                $this->results = $response->json();
                $this->searchParams = $params;
            } else {
                Log::error('AMPI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                $this->errorMessage = 'Error al buscar propiedades. Intenta de nuevo en unos minutos.';
                $this->results = [];
            }
        } catch (\Throwable $e) {
            Log::error('AMPI API exception', ['message' => $e->getMessage()]);
            $this->errorMessage = 'No pudimos conectarnos con AMPI. Intenta más tarde.';
            $this->results = [];
        }
    }

    public function resetFilters(): void
    {
        $this->reset([
            'office_id',
            'neighborhood',
            'category',
            'status',
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
            'page',
        ]);

        $this->page = 1;
        $this->search();
    }

    public function render()
    {
        return view('livewire.public.properties-search')
            ->layout('layouts.public', [
                'title' => 'Propiedades | investsma',
            ]);
    }

    private function buildQueryParams(): array
    {
        $params = [];

        $arrayFields = ['neighborhood'];
        foreach ($arrayFields as $field) {
            $value = $this->{$field};
            if (is_array($value) && count(array_filter($value))) {
                $params[$field] = implode(',', array_filter($value));
            }
        }

        $singleFields = [
            'office_id',
            'category',
            'status',
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
            $value = $this->{$field};
            if ($value !== null && $value !== '') {
                $params[$field] = $value;
            }
        }

        $params['page'] = $this->page;
        $params['per_page'] = $this->perPage;

        return $params;
    }

    private function hydrateFromQuery(array $params): void
    {
        $this->office_id = $params['office_id'] ?? $this->office_id;
        $this->category = $params['category'] ?? $this->category;
        $this->status = $params['status'] ?? $this->status;
        $this->currency = $params['currency'] ?? $this->currency;
        $this->price_min = $params['price_min'] ?? $this->price_min;
        $this->price_max = $params['price_max'] ?? $this->price_max;
        $this->floors = $params['floors'] ?? $this->floors;
        $this->construction_meters_min = $params['construction_meters_min'] ?? $this->construction_meters_min;
        $this->construction_meters_max = $params['construction_meters_max'] ?? $this->construction_meters_max;
        $this->lot_meters_min = $params['lot_meters_min'] ?? $this->lot_meters_min;
        $this->lot_meters_max = $params['lot_meters_max'] ?? $this->lot_meters_max;
        $this->bathrooms = $params['bathrooms'] ?? $this->bathrooms;
        $this->bedrooms = $params['bedrooms'] ?? $this->bedrooms;
        $this->furnished = $params['furnished'] ?? $this->furnished;
        $this->parking_type = $params['parking_type'] ?? $this->parking_type;
        $this->with_yard = $params['with_yard'] ?? $this->with_yard;
        $this->pool = $params['pool'] ?? $this->pool;
        $this->casita = $params['casita'] ?? $this->casita;
        $this->gated_comm = $params['gated_comm'] ?? $this->gated_comm;

        if (isset($params['page'])) {
            $this->page = (int) $params['page'];
        }

        if (isset($params['neighborhood'])) {
            $this->neighborhood = $this->toArrayList($params['neighborhood']);
        }
    }

    private function toArrayList($value): array
    {
        if (is_array($value)) {
            return array_filter($value);
        }

        if (is_string($value)) {
            return array_filter(array_map('trim', explode(',', $value)));
        }

        return [];
    }

    private function getNeighborhoods(): array
    {
        $apiKey = config('services.ampi.api_key');
        if (! $apiKey) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'x-api-key' => $apiKey,
            ])->get('https://ampisanmigueldeallende.com/api/v1/neighborhoods');

            if ($response->successful()) {
                return collect($response->json())
                    ->pluck('name')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();
            }
        } catch (\Throwable $e) {
            Log::error('Failed to fetch neighborhoods', ['message' => $e->getMessage()]);
        }

        return [];
    }
}
