<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\Page;
use App\Support\EditorJsRenderer;
use App\Support\SeoData;
use App\Support\StaticPageRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(Request $request): View
    {
        $filters = $request->only([
            'office_id',
            'neighborhood',
            'category',
            'status',
            'currency',
            'price_min',
            'price_max',
            'bedrooms',
            'bathrooms',
            'page',
            'per_page',
        ]);

        SeoData::apply(
            title: 'Bienes raíces en San Miguel de Allende | investsma',
            description: 'Invierte en San Miguel de Allende con propiedades seleccionadas, análisis local y acompañamiento inmobiliario para compra, renta o plusvalía.',
            keywords: ['investsma', 'bienes raíces', 'San Miguel de Allende', 'propiedades', 'inversión inmobiliaria'],
            image: asset('logotipo.png'),
        );

        return view('public.home', [
            'properties' => $this->fetchOfficeProperties($filters),
            'neighborhoods' => $this->fetchNeighborhoods(),
        ]);
    }

    public function about(): View
    {
        SeoData::apply(
            title: 'Nosotros | investsma',
            description: 'Conoce al equipo de investsma y nuestra metodología para evaluar propiedades, plusvalía y riesgos en San Miguel de Allende.',
            keywords: ['investsma', 'nosotros', 'San Miguel de Allende', 'asesoría inmobiliaria'],
            image: asset('logotipo.png'),
        );

        return view('public.about');
    }

    public function contact(): View
    {
        SeoData::apply(
            title: 'Contacto | investsma',
            description: 'Habla con investsma para encontrar casas, lotes y oportunidades de inversión inmobiliaria en San Miguel de Allende.',
            keywords: ['contacto', 'investsma', 'San Miguel de Allende', 'bienes raíces'],
            image: asset('logotipo.png'),
        );

        return view('public.contact');
    }

    public function properties(): View
    {
        SeoData::apply(
            title: 'Propiedades | investsma',
            description: 'Explora propiedades en San Miguel de Allende con filtros por zona, precio, tipo y características para identificar mejores oportunidades.',
            keywords: ['propiedades', 'San Miguel de Allende', 'casas', 'lotes', 'investsma'],
            image: asset('logotipo.png'),
        );

        return view('public.properties-index');
    }

    public function contactSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'objetivo' => ['nullable', 'string', 'max:255'],
            'mensaje' => ['nullable', 'string', 'max:2000'],
        ]);

        Mail::to('info@investsma.com')->send(new ContactMessage($data));

        return back()->with('status', 'Gracias, recibimos tu mensaje. Te contactamos en breve.');
    }

    public function show(Page $page): View
    {
        abort_unless($page->status === 'published', 404);

        if ($page->isStatic() && filled($page->static_view)) {
            SeoData::apply(
                title: ($page->meta_title ?: $page->title).' | investsma',
                description: $page->meta_description,
                keywords: [$page->title, 'investsma', 'San Miguel de Allende'],
                image: asset('logotipo.png'),
            );

            return view($page->static_view, ['page' => $page]);
        }

        $html = data_get($page->content, 'html')
            ?? EditorJsRenderer::render($page->content ?? []);

        SeoData::apply(
            title: ($page->meta_title ?: $page->title).' | investsma',
            description: $page->meta_description ?: Str::limit(strip_tags($html), 160, '...'),
            keywords: [$page->title, 'investsma', 'San Miguel de Allende'],
            image: asset('logotipo.png'),
            type: 'article',
            schemaType: 'Article',
        );

        return view('public.page', [
            'page' => $page,
            'html' => $html,
        ]);
    }

    public function static(string $key): View
    {
        $view = StaticPageRegistry::viewForKey($key);
        $page = StaticPageRegistry::find($key);

        abort_if(! $view, 404);

        SeoData::apply(
            title: ($page['title'] ?? 'investsma').' | investsma',
            description: $page['description'] ?? null,
            keywords: [$page['title'] ?? null, 'investsma', 'San Miguel de Allende'],
            image: asset('logotipo.png'),
        );

        return view($view);
    }

    private function fetchOfficeProperties(array $filters = []): array
    {
        $apiKey = config('services.ampi.api_key');

        if (! $apiKey) {
            return [];
        }

        try {
            $allowed = [
                'office_id',
                'neighborhood',
                'category',
                'status',
                'currency',
                'price_min',
                'price_max',
                'bedrooms',
                'bathrooms',
                'floors',
                'construction_meters_min',
                'construction_meters_max',
                'lot_meters_min',
                'lot_meters_max',
                'furnished',
                'parking_type',
                'with_yard',
                'pool',
                'casita',
                'gated_comm',
                'page',
                'per_page',
            ];

            $params = array_filter(
                array_merge(
                    ['office_id' => '32', 'page' => 1, 'per_page' => 12],
                    Arr::only($filters, $allowed)
                ),
                fn ($value) => $value !== null && $value !== ''
            );

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'x-api-key' => $apiKey,
            ])->get('https://ampisanmigueldeallende.com/api/v1/properties/search', $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to fetch office properties', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception fetching office properties', ['message' => $e->getMessage()]);
        }

        return [];
    }

    private function fetchNeighborhoods(): array
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
            Log::error('Failed to fetch neighborhoods on home', ['message' => $e->getMessage()]);
        }

        return [];
    }
}
