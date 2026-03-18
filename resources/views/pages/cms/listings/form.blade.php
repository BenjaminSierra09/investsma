<?php

use App\Models\Listing;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public ?int $listingId = null;

    public ?Listing $editing = null;

    public string $title = '';

    public string $slug = '';

    public string $status = 'draft';

    public bool $featured = false;

    public string $currency = 'USD';

    public ?string $price = null;

    public ?string $location = null;

    public ?string $summary = null;

    public ?string $description = null;

    public ?int $bedrooms = null;

    public ?int $bathrooms = null;

    public ?int $construction_m2 = null;

    public ?int $lot_m2 = null;

    public ?string $cover_image = null;

    public ?string $contact_email = 'info@investsma.com';

    public ?string $contact_phone = '+52 415 125 5042';

    public ?string $contact_whatsapp = null;

    public ?string $meta_title = null;

    public ?string $meta_description = null;

    /** @var array<int, string> */
    public array $gallery = [];

    /** @var array<int, mixed> */
    public array $photoUploads = [];

    public function mount(?int $listingId = null): void
    {
        $this->loadListing($listingId);
    }

    public function loadListing(?int $listingId = null): void
    {
        $this->listingId = $listingId;
        $this->editing = $listingId ? Listing::find($listingId) : null;

        if ($this->editing) {
            $listing = $this->editing;

            $this->title = $listing->title;
            $this->slug = $listing->slug;
            $this->status = $listing->status;
            $this->featured = $listing->featured;
            $this->currency = $listing->currency;
            $this->price = $listing->price ? (string) $listing->price : null;
            $this->location = $listing->location;
            $this->summary = $listing->summary;
            $this->description = $listing->description;
            $this->bedrooms = $listing->bedrooms;
            $this->bathrooms = $listing->bathrooms;
            $this->construction_m2 = $listing->construction_m2;
            $this->lot_m2 = $listing->lot_m2;
            $this->cover_image = $listing->cover_image;
            $this->contact_email = $listing->contact_email;
            $this->contact_phone = $listing->contact_phone;
            $this->contact_whatsapp = $listing->contact_whatsapp;
            $this->meta_title = $listing->meta_title;
            $this->meta_description = $listing->meta_description;
            $this->gallery = collect($listing->gallery)->filter()->values()->all();
            $this->photoUploads = [];
        } else {
            $this->reset([
                'title',
                'slug',
                'status',
                'featured',
                'currency',
                'price',
                'location',
                'summary',
                'description',
                'bedrooms',
                'bathrooms',
                'construction_m2',
                'lot_m2',
                'cover_image',
                'contact_email',
                'contact_phone',
                'contact_whatsapp',
                'meta_title',
                'meta_description',
                'gallery',
                'photoUploads',
            ]);

            $this->status = 'draft';
            $this->currency = 'USD';
            $this->contact_email = 'info@investsma.com';
            $this->contact_phone = '+52 415 125 5042';
            $this->gallery = [];
            $this->photoUploads = [];
        }
    }

    public function updatedTitle(): void
    {
        if (! $this->editing) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function updatedPhotoUploads(): void
    {
        $this->validate([
            'photoUploads' => ['array', 'max:20'],
            'photoUploads.*' => ['image', 'max:5120'],
        ]);
    }

    public function removeGalleryImage(int $index): void
    {
        unset($this->gallery[$index]);
        $this->gallery = array_values($this->gallery);
    }

    public function promoteGalleryImage(int $index): void
    {
        if (! isset($this->gallery[$index])) {
            return;
        }

        $selectedImage = $this->gallery[$index];
        unset($this->gallery[$index]);

        array_unshift($this->gallery, $selectedImage);
        $this->gallery = array_values(array_unique($this->gallery));
        $this->cover_image = $this->gallery[0] ?? null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'slug' => ['required', 'string', 'min:3', 'max:255', Rule::unique('listings', 'slug')->ignore($this->editing?->id)],
            'status' => ['required', 'in:draft,published'],
            'featured' => ['required', 'boolean'],
            'currency' => ['required', 'string', 'max:10'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:600'],
            'description' => ['nullable', 'string'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:99'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:99'],
            'construction_m2' => ['nullable', 'integer', 'min:0'],
            'lot_m2' => ['nullable', 'integer', 'min:0'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_whatsapp' => ['nullable', 'string', 'max:50'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'gallery' => ['array'],
            'gallery.*' => ['string', 'max:2048'],
            'photoUploads' => ['array', 'max:20'],
            'photoUploads.*' => ['image', 'max:5120'],
        ]);

        $storedPhotos = collect($this->photoUploads)
            ->map(function ($photo): string {
                return \Illuminate\Support\Facades\Storage::disk('public')->url(
                    $photo->store('listings', 'public')
                );
            })
            ->all();

        $gallery = collect($validated['gallery'] ?? [])
            ->merge($storedPhotos)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $data = array_merge($validated, [
            'gallery' => $gallery,
            'cover_image' => $validated['cover_image'] ?: ($gallery[0] ?? null),
            'published_at' => $validated['status'] === 'published' ? ($this->editing?->published_at ?: now()) : null,
        ]);

        if ($this->editing) {
            $this->editing->update($data);
        } else {
            $this->editing = Listing::create($data);
            $this->listingId = $this->editing->id;
        }

        $this->photoUploads = [];
        $this->dispatch('notify', title: 'Listado guardado', body: 'Actualizamos la propiedad con éxito.');
        $this->redirectRoute('cms.listings', navigate: true);
    }

    public function delete(): void
    {
        if ($this->editing) {
            $this->editing->delete();
        }

        $this->redirectRoute('cms.listings', navigate: true);
    }
}; ?>

<div class="p-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-50">{{ $editing ? 'Editar listado' : 'Nuevo listado' }}</div>
            <p class="text-xs text-zinc-500">Crea una propiedad independiente de la API con su propia URL.</p>
        </div>
        <flux:badge color="amber" size="sm">{{ $status === 'published' ? 'Publicado' : 'Borrador' }}</flux:badge>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
            <flux:card>
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input wire:model.live.debounce.500ms="title" label="Título" placeholder="Casa colonial en Guadiana" />
                    <flux:input wire:model.live="slug" label="Slug" placeholder="casa-colonial-en-guadiana" />
                    <flux:select wire:model.live="status" label="Estado">
                        <option value="draft">Borrador</option>
                        <option value="published">Publicado</option>
                    </flux:select>
                    <flux:select wire:model.live="currency" label="Moneda">
                        <option value="USD">USD</option>
                        <option value="MXN">MXN</option>
                    </flux:select>
                    <flux:input wire:model.live="price" label="Precio" type="number" min="0" step="0.01" />
                    <flux:input wire:model.live="location" label="Ubicación" placeholder="Guadiana, San Miguel de Allende" />
                    <flux:input wire:model.live="bedrooms" label="Recámaras" type="number" min="0" />
                    <flux:input wire:model.live="bathrooms" label="Baños" type="number" min="0" />
                    <flux:input wire:model.live="construction_m2" label="Construcción m2" type="number" min="0" />
                    <flux:input wire:model.live="lot_m2" label="Terreno m2" type="number" min="0" />
                </div>

                <div class="mt-4">
                    <flux:checkbox wire:model.live="featured" label="Mostrar como destacado" />
                </div>

                <div class="mt-4 grid gap-4">
                    <flux:textarea wire:model.live="summary" label="Resumen" placeholder="Texto corto para tarjetas y cabecera." />
                    <flux:textarea wire:model.live="description" label="Descripción" rows="10" placeholder="Describe la propiedad, amenidades, ubicación y contexto." />
                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-50">Galería</div>
                        <p class="text-xs text-zinc-500">Arrastra fotos o selecciónalas. La primera imagen será la portada.</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div
                        x-data="{ dragging: false }"
                        class="rounded-[28px] border border-dashed border-amber-200 bg-amber-50/40 p-5"
                    >
                        <input
                            id="listing-photo-input"
                            type="file"
                            multiple
                            accept="image/*"
                            wire:model="photoUploads"
                            class="hidden"
                        >

                        <button
                            type="button"
                            id="listing-dropzone"
                            x-on:dragover.prevent="dragging = true"
                            x-on:dragleave.prevent="dragging = false"
                            x-on:drop.prevent="dragging = false"
                            x-bind:class="dragging ? 'border-amber-400 bg-amber-100/70' : 'border-amber-200 bg-white/70'"
                            class="flex w-full flex-col items-center justify-center gap-3 rounded-[24px] border border-dashed px-6 py-10 text-center transition"
                        >
                            <div class="rounded-full bg-amber-100 p-3 text-amber-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0-4 4m4-4 4 4M4 16.5A2.5 2.5 0 0 0 6.5 19h11a2.5 2.5 0 0 0 2.5-2.5" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-zinc-900">Suelta tus fotos aquí</div>
                                <div class="mt-1 text-xs text-zinc-500">o haz clic para elegir varias imágenes</div>
                            </div>
                            <div class="text-[11px] uppercase tracking-[0.2em] text-zinc-400">JPG, PNG, WEBP · máx. 5 MB cada una</div>
                        </button>

                        @error('photoUploads.*')
                            <p class="mt-3 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($photoUploads !== [])
                        <div>
                            <div class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Por subir</div>
                            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                @foreach ($photoUploads as $upload)
                                    <div class="overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-sm">
                                        <img src="{{ $upload->temporaryUrl() }}" alt="Vista previa" class="aspect-[4/3] w-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($gallery !== [])
                        <div>
                            <div class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Fotos actuales</div>
                            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                @foreach ($gallery as $index => $image)
                                    <div class="overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-sm" wire:key="gallery-image-{{ $index }}">
                                        <img src="{{ $image }}" alt="Foto {{ $index + 1 }}" class="aspect-[4/3] w-full object-cover">
                                        <div class="flex items-center justify-between gap-2 px-3 py-3">
                                            <div class="text-xs text-zinc-500">
                                                {{ $index === 0 ? 'Portada' : 'Galería' }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if ($index !== 0)
                                                    <button type="button" wire:click="promoteGalleryImage({{ $index }})" class="text-xs font-semibold text-amber-700">
                                                        Portada
                                                    </button>
                                                @endif
                                                <button type="button" wire:click="removeGalleryImage({{ $index }})" class="text-xs font-semibold text-rose-600">
                                                    Quitar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>

        <div class="space-y-6">
            <flux:card>
                <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-50">Contacto del listado</div>
                <div class="mt-4 grid gap-4">
                    <flux:input wire:model.live="contact_email" label="Correo" placeholder="info@investsma.com" />
                    <flux:input wire:model.live="contact_phone" label="Teléfono" placeholder="+52 415..." />
                    <flux:input wire:model.live="contact_whatsapp" label="WhatsApp" placeholder="524151255042" />
                </div>
            </flux:card>

            <flux:card>
                <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-50">SEO</div>
                <div class="mt-4 grid gap-4">
                    <flux:input wire:model.live="meta_title" label="Meta título" placeholder="Casa colonial en Guadiana | investsma" />
                    <flux:textarea wire:model.live="meta_description" label="Meta descripción" placeholder="Resumen para buscadores" />
                </div>
            </flux:card>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <flux:button variant="primary" wire:click="save">Guardar listado</flux:button>
        <flux:button variant="ghost" :href="route('cms.listings')" wire:navigate>Volver</flux:button>
        @if ($editing)
            <flux:button variant="ghost" color="danger" icon="trash" wire:click="delete">Eliminar</flux:button>
        @endif
    </div>

    @script
        <script>
            const dropzone = document.getElementById('listing-dropzone');
            const input = document.getElementById('listing-photo-input');

            if (dropzone && input) {
                dropzone.addEventListener('click', () => {
                    input.click();
                });

                dropzone.addEventListener('drop', (event) => {
                    const files = Array.from(event.dataTransfer?.files ?? []);

                    if (files.length === 0) {
                        return;
                    }

                    const transfer = new DataTransfer();

                    files.forEach((file) => transfer.items.add(file));

                    input.files = transfer.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }
        </script>
    @endscript
</div>
