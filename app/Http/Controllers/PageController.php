<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\Page;
use App\Support\EditorJsRenderer;
use App\Support\StaticPageRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
        ]);

        return view('public.home', [
            'properties' => $this->fetchOfficeProperties($filters),
            'neighborhoods' => $this->fetchNeighborhoods(),
        ]);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function properties(): View
    {
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
            return view($page->static_view, ['page' => $page]);
        }

        $html = data_get($page->content, 'html')
            ?? EditorJsRenderer::render($page->content ?? []);

        return view('public.page', [
            'page' => $page,
            'html' => $html,
        ]);
    }

    public function static(string $key): View
    {
        $view = StaticPageRegistry::viewForKey($key);

        abort_if(! $view, 404);

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
