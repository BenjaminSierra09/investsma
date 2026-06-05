<?php

use App\Models\Listing;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function listings(): Collection
    {
        return Listing::query()->with('agent')->latest('id')->get();
    }
}; ?>

<div class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading class="text-xl">Listados</flux:heading>
            <flux:subheading>Administra propiedades propias con su enlace público y formulario de contacto.</flux:subheading>
        </div>
        <flux:button icon="plus" variant="primary" :href="route('cms.listings.form')" wire:navigate>Nuevo listado</flux:button>
    </div>

    <flux:card class="mt-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sticky>Título</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Agente</flux:table.column>
                <flux:table.column>Precio</flux:table.column>
                <flux:table.column>Slug</flux:table.column>
                <flux:table.column align="end">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->listings as $listing)
                    <flux:table.row key="listing-{{ $listing->id }}">
                        <flux:table.cell variant="strong" sticky>{{ $listing->title }}</flux:table.cell>
                        <flux:table.cell>{{ $listing->status === 'published' ? 'Publicado' : 'Borrador' }}</flux:table.cell>
                        <flux:table.cell>{{ $listing->agent?->name ?: '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $listing->price ? $listing->currency.' $'.number_format((float) $listing->price, 0) : '—' }}</flux:table.cell>
                        <flux:table.cell>/listados/{{ $listing->slug }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-2">
                                @if ($listing->status === 'published')
                                    <flux:button size="xs" variant="ghost" :href="route('listings.show', $listing)" target="_blank">Ver</flux:button>
                                @endif
                                <flux:button size="xs" variant="ghost" :href="route('cms.listings.form', $listing->id)" wire:navigate>Editar</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-sm text-zinc-500">Aún no hay listados.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
