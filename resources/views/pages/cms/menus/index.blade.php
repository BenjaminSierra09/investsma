<?php

use App\Models\MenuItem;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function items()
    {
        return MenuItem::forMenu('main')->with('page')->get();
    }
}; ?>


<div class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading class="text-xl">Menú principal</flux:heading>
            <flux:subheading>Organiza páginas, vistas fijas o enlaces externos.</flux:subheading>
        </div>
        <flux:button icon="plus" variant="primary" :href="route('cms.menus.form')" wire:navigate>Editar menú</flux:button>
    </div>

    <flux:card class="mt-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sticky>Etiqueta</flux:table.column>
                <flux:table.column>Tipo</flux:table.column>
                <flux:table.column>Destino</flux:table.column>
                <flux:table.column align="end">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->items as $item)
                    <flux:table.row key="menu-{{ $item->id }}">
                        <flux:table.cell variant="strong" sticky>{{ $item->label }}</flux:table.cell>
                        <flux:table.cell>{{ strtoupper($item->type) }}</flux:table.cell>
                        <flux:table.cell>
                            @switch($item->type)
                                @case('page')
                                    {{ $item->page?->title ?? '—' }}
                                    @break
                                @case('static')
                                    {{ $item->static_key ?? '—' }}
                                    @break
                                @default
                                    {{ $item->url ?? '—' }}
                            @endswitch
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button size="xs" variant="ghost" :href="route('cms.menus.form')" wire:navigate>Editar</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center text-sm text-zinc-500">Aún no hay elementos en el menú.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
