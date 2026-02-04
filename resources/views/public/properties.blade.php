<x-layouts.public :title="($property['name'] ?? __('Property')).' | investsma'">
 @vite(['resources/css/property-gallery.css'])

@php
    $latitude = $property['latitude'] ?? $property['lat'] ?? null;
    $longitude = $property['longitude'] ?? $property['lng'] ?? null;
@endphp

<script>
    window.latitude = {!! $latitude !== null ? json_encode((float) $latitude) : 'null' !!};
    window.longitude = {!! $longitude !== null ? json_encode((float) $longitude) : 'null' !!};
</script>

<!-- Property Gallery Section - Full Width -->
<section class="w-full bg-white">
    @if (isset($property['photos']) && count($property['photos']) > 0)
        <!-- Main Gallery -->
        <div id="property-gallery" data-total="{{ count($property['photos']) }}" class="relative w-full h-96 md:h-[500px] overflow-hidden group">
            <div class="swiper gallery-main w-full h-full">
                <div class="swiper-wrapper">
                    @foreach ($property['photos'] as $index => $photo)
                        <div class="swiper-slide">
                            <div class="swiper-zoom-container">
                                <a href="{{ $photo }}" class="pswp-gallery-item block w-full h-full"
                                    data-pswp-width="2075" data-pswp-height="1380" target="_blank">
                                    @if ($index < 3)
                                        <img src="{{ $photo }}"
                                            alt="{{ $property['name'] }} - Image {{ $index + 1 }}"
                                            class="w-full h-full object-cover cursor-pointer" loading="eager">
                                    @else
                                        <img data-src="{{ $photo }}"
                                            alt="{{ $property['name'] }} - Image {{ $index + 1 }}"
                                            class="w-full h-full object-cover cursor-pointer img-fade-in"
                                            src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIiBmaWxsPSIjRjNGNEY2Ii8+Cjwvc3ZnPgo="
                                            loading="lazy">
                                        <div class="swiper-lazy-preloader"></div>
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Navigation arrows -->
                <div
                    class="main-prev absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-3 cursor-pointer z-10 transition-all duration-300 opacity-0 group-hover:opacity-100 shadow-lg backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </div>
                <div
                    class="main-next absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-3 cursor-pointer z-10 transition-all duration-300 opacity-0 group-hover:opacity-100 shadow-lg backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>

                <!-- Pagination -->
                <div class="main-pagination absolute bottom-4 left-1/2 transform -translate-x-1/2 z-10"></div>
            </div>

            <!-- Image counter -->
            <div class="absolute bottom-6 left-6 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg shadow-lg z-10">
                <span class="image-counter text-sm font-medium text-gray-700">1 /
                    {{ count($property['photos']) }}</span>
            </div>


            <!-- Loading overlay -->
            <div class="absolute inset-0 bg-gray-100 flex items-center justify-center z-20 gallery-loading">
                <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
                    <p class="mt-4 text-gray-600">{{ __('Loading gallery...') }}</p>
                </div>
            </div>
        </div>
    @else
        <!-- No Photos Placeholder -->
        <div class="relative w-full h-96 md:h-[500px] overflow-hidden bg-gray-100">
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
        </div>
    @endif
</section>

<!-- Property Information -->
<section class="py-8 md:py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Title and Price -->
                <div class="mb-6">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                                {{ $property['name'] }}
                            </h1>
                            <p class="text-lg text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $property['neighborhood'] }}, {{ $property['city'] }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl md:text-4xl font-bold text-orange-600">
                                @if ($property['price'] > 0)
                                    {{ $property['currency'] }} ${{ number_format($property['price'], 0) }}
                                @else
                                    {{ __('Price Upon Request') }}
                                @endif
                            </div>
                            @if (isset($property['status']))
                                <span
                                    class="inline-block mt-2 px-3 py-1 bg-orange-600 text-white text-sm font-semibold rounded">
                                    {{ __($property['status']) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Property Features Summary -->
                    <div class="flex flex-wrap gap-6 p-4 bg-gray-50 rounded-lg">
                        @if (isset($property['bedrooms']) && $property['bedrooms'] > 0)
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M7,12.5a3,3,0,1,0-3-3A3,3,0,0,0,7,12.5Zm0-4a1,1,0,1,1-1,1A1,1,0,0,1,7,8.5Zm13-2H12a1,1,0,0,0-1,1v6H3v-8a1,1,0,0,0-2,0v13a1,1,0,0,0,2,0v-3H21v3a1,1,0,0,0,2,0v-9A3,3,0,0,0,20,6.5Zm1,7H13v-5h7a1,1,0,0,1,1,1Z" />
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-500">{{ __('Bedrooms') }}</div>
                                    <div class="font-semibold">{{ $property['bedrooms'] }}</div>
                                </div>
                            </div>
                        @endif

                        @if (isset($property['bathrooms']) && $property['bathrooms'] > 0)
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22,12H5V6.41016A1.97474,1.97474,0,0,1,6.04,4.65137a1.99474,1.99474,0,0,1,1.14764-.2312,3.49114,3.49114,0,0,0,.83771,3.55444L9.08594,9.03516a.99965.99965,0,0,0,1.41406,0L14.03516,5.5a.99964.99964,0,0,0,0-1.41406L12.97461,3.02539a3.494,3.494,0,0,0-4.52972-.34253A3.99247,3.99247,0,0,0,3,6.41016V12H2a1,1,0,0,0,0,2H3v3a2.995,2.995,0,0,0,2,2.81567V21a1,1,0,0,0,2,0V20H17v1a1,1,0,0,0,2,0V19.81573A2.99507,2.99507,0,0,0,21,17V14h1a1,1,0,0,0,0-2ZM9.43945,4.43945a1.50184,1.50184,0,0,1,2.1211,0l.35351.35352L9.793,6.91406l-.35352-.35351A1.50123,1.50123,0,0,1,9.43945,4.43945ZM19,17a1.00067,1.00067,0,0,1-1,1H6a1.00067,1.00067,0,0,1-1-1V14H19Z" />
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-500">{{ __('Bathrooms') }}</div>
                                    <div class="font-semibold">{{ $property['bathrooms'] }}
                                        @if (isset($property['half_bathrooms']) && $property['half_bathrooms'] > 0)
                                            + {{ $property['half_bathrooms'] }} {{ __('Half') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (isset($property['construction_meters']) && $property['construction_meters'] > 0)
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-2 text-gray-600" fill="currentColor"
                                    viewBox="0 0 241.041 241.041">
                                    <path
                                        d="M207.503,113.624L126.39,43.848c-3.375-2.902-8.363-2.902-11.738,0l-81.113,69.776c-1.987,1.71-3.131,4.201-3.131,6.823v111.594c0,4.971,4.029,9,9,9h162.227c4.971,0,9-4.029,9-9V120.447C210.634,117.825,209.49,115.334,207.503,113.624z" />
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-500">{{ __('Construction') }}</div>
                                    <div class="font-semibold">
                                        {{ number_format($property['construction_meters'], 0) }}
                                        {{ __('M2') }}</div>
                                </div>
                            </div>
                        @endif

                        @if (isset($property['lot_meters']) && $property['lot_meters'] > 0)
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 15 15">
                                    <path
                                        d="M13.5 0.5H1.5C0.947715 0.5 0.5 0.947716 0.5 1.5V13.5C0.5 14.0523 0.947716 14.5 1.5 14.5H13.5C14.0523 14.5 14.5 14.0523 14.5 13.5V1.5C14.5 0.947715 14.0523 0.5 13.5 0.5Z" />
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-500">{{ __('Lot Size') }}</div>
                                    <div class="font-semibold">{{ number_format($property['lot_meters'], 0) }}
                                        {{ __('M2') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Description') }}</h2>
                    <div class="prose max-w-none text-gray-700">
                        @php
                            $locale = app()->getLocale();
                            $description =
                                $locale === 'es' && !empty($property['description_full_es'])
                                    ? $property['description_full_es']
                                    : $property['description_full_en'];
                            $cleanDescription = $description ? strip_tags($description, '<br><br/>') : null;
                        @endphp
                        @if (! empty($cleanDescription))
                            {!! $cleanDescription !!}
                        @else
                            <p>{{ __('Check more details in the listing.') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Property Details') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">{{ __('Property Type') }}</div>
                            <div class="font-semibold">{{ __($property['category']) }}</div>
                        </div>

                        @if (isset($property['floors']) && $property['floors'] > 0)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-500 mb-1">{{ __('Floors') }}</div>
                                <div class="font-semibold">{{ $property['floors'] }}</div>
                            </div>
                        @endif

                        @if (isset($property['year_built']) && $property['year_built'] > 2000)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-500 mb-1">{{ __('Year Built') }}</div>
                                <div class="font-semibold">{{ $property['year_built'] }}</div>
                            </div>
                        @endif

                        @if (!empty($property['furnished']) && $property['furnished'] !== 'Any')
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-500 mb-1">{{ __('Furnished') }}</div>
                                <div class="font-semibold">{{ __($property['furnished']) }}</div>
                            </div>
                        @endif

                        @if (!empty($property['parking_type']) && $property['parking_type'] !== 'Any')
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-500 mb-1">{{ __('Parking') }}</div>
                                <div class="font-semibold">{{ __($property['parking_type']) }}</div>
                            </div>
                        @endif

                        @if (!empty($property['pool']) && $property['pool'] !== 'no')
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-500 mb-1">{{ __('Pool') }}</div>
                                <div class="font-semibold">{{ __('Yes') }}</div>
                            </div>
                        @endif

                        @if (!empty($property['casita']) && $property['casita'] !== 'no')
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-500 mb-1">{{ __('With Casita') }}</div>
                                <div class="font-semibold">{{ __('Yes') }}</div>
                            </div>
                        @endif

                        @if (!empty($property['gated_comm']) && $property['gated_comm'] !== 'Any')
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-500 mb-1">{{ __('Gated Community') }}</div>
                                <div class="font-semibold">{{ __($property['gated_comm']) }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Location') }}</h2>
                        <div id="property-map" class="h-80 w-full rounded-xl border border-gray-200 shadow-sm"></div>
                    </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Contact Card -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-lg sticky top-4">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('Interested in this property?') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('Contact us for more information') }}</p>

                    <div class="space-y-4">
                        <a href="tel:+524151255042"
                            class="block w-full px-6 py-3 bg-orange-600 text-white text-center font-semibold rounded-lg hover:bg-orange-700 transition-colors">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                </path>
                            </svg>
                            {{ __('Call Us') }}
                        </a>

                        <a href="mailto:info@investsma.com"
                            class="block w-full px-6 py-3 bg-gray-100 text-gray-900 text-center font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="inline-block w-5 h-5 mr-2 -mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            {{ __('Email Us') }}
                        </a>
                    </div>

                    <!-- Property ID -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="text-sm text-gray-500 mb-1">{{ __('AMPI MLS ID') }}</div>
                        <div class="font-semibold text-gray-900">#{{ $property['mls_id'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@vite(['resources/js/single-property.js'])
</x-layouts.public>
