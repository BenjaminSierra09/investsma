<?php

namespace App\Livewire\Public;

use App\Support\AmpiPropertyApi;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class PropertiesSearch extends Component
{
    protected AmpiPropertyApi $ampiPropertyApi;

    public array $neighborhoods = [];

    public array $results = [];

    public array $searchParams = [];

    public ?string $errorMessage = null;

    protected $queryString = [
        'keywords' => ['except' => ''],
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

    public string $keywords = '';

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

    public function boot(AmpiPropertyApi $ampiPropertyApi): void
    {
        $this->ampiPropertyApi = $ampiPropertyApi;
    }

    public function mount(): void
    {
        $this->hydrateFromQuery(request()->all());
        $this->neighborhoods = $this->resolveNeighborhoodOptions();
        $this->search();
    }

    public function hydrate(): void
    {
        $this->neighborhood = $this->toArrayList($this->neighborhood);
    }

    public function updating($name, $value): void
    {
        if ($name !== 'page') {
            $this->page = 1;
        }
    }

    public function updatedNeighborhood($value): void
    {
        $this->neighborhood = $this->toArrayList($value);
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

        if (! $this->ampiPropertyApi->isConfigured()) {
            $this->errorMessage = 'Falta configurar la API key de AMPI.';
            $this->results = [];

            return;
        }

        $results = $this->ampiPropertyApi->search($params);

        if (! is_array($results)) {
            $this->errorMessage = 'No pudimos conectarnos con AMPI. Intenta más tarde.';
            $this->results = [];

            return;
        }

        $this->results = $results;
        $this->searchParams = $params;
    }

    public function resetFilters(): void
    {
        $this->reset([
            'keywords',
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
        $this->dispatch('property-search-filters-reset', filters: $this->currentFilterState());
    }

    public function render(): View
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
                $params[$field] = array_values(array_filter($value));
            }
        }

        $singleFields = [
            'keywords',
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
        $this->keywords = $params['keywords'] ?? $this->keywords;
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
        $this->pool = $this->normalizeYesNoValue($params['pool'] ?? $this->pool);
        $this->casita = $this->normalizeYesNoValue($params['casita'] ?? $this->casita);
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
            $trimmedValue = trim($value);

            return $trimmedValue === '' ? [] : [$trimmedValue];
        }

        return [];
    }

    private function resolveNeighborhoodOptions(): array
    {
        return collect($this->toArrayList($this->neighborhood))
            ->merge($this->toArrayList($this->ampiPropertyApi->fetchNeighborhoodOptions()))
            ->filter(fn (mixed $item): bool => is_string($item) && filled(trim($item)))
            ->map(fn (string $item): string => trim($item))
            ->unique(fn (string $item): string => Str::lower($item))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    private function normalizeYesNoValue(?string $value): ?string
    {
        return match (Str::lower((string) $value)) {
            'yes' => 'Yes',
            'no' => 'No',
            default => $value,
        };
    }

    private function currentFilterState(): array
    {
        return [
            'keywords' => $this->keywords,
            'neighborhood' => $this->neighborhood,
            'category' => $this->category,
            'status' => $this->status,
            'currency' => $this->currency,
            'pool' => $this->pool,
            'casita' => $this->casita,
        ];
    }
}
