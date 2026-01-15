<?php

use App\Models\Page;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public function deletePage(int $pageId): void
    {
        $page = Page::find($pageId);

        if ($page) {
            $page->delete();
        }
    }

    #[Computed]
    public function pages()
    {
        return Page::query()->latest('updated_at')->get();
    }
}; ?>

    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading class="text-xl">Páginas con Editor</flux:heading>
                <flux:subheading>Gestiona el contenido editable del sitio.</flux:subheading>
            </div>
            <flux:button icon="plus" variant="primary" class="hidden md:inline-flex" :href="route('cms.pages.form')" wire:navigate>Nueva página</flux:button>
        </div>

        <flux:card class="mt-6">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sticky>Título</flux:table.column>
                        <flux:table.column>Slug</flux:table.column>
                        <flux:table.column>Estado</flux:table.column>
                        <flux:table.column>Actualizado</flux:table.column>
                        <flux:table.column align="end">Acciones</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($this->pages as $page)
                            <flux:table.row key="page-{{ $page->id }}">
                                <flux:table.cell variant="strong" sticky>{{ $page->title }}</flux:table.cell>
                                <flux:table.cell>/{{ $page->slug }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm">{{ $page->status }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>{{ optional($page->updated_at)->diffForHumans() }}</flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button size="xs" variant="ghost" :href="route('cms.pages.form', $page->id)" wire:navigate>Editar</flux:button>
                                        <flux:button size="xs" icon="trash" color="danger" variant="ghost" wire:click="deletePage({{ $page->id }})">Eliminar</flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" class="text-center text-sm text-zinc-500">Aún no hay páginas.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
        </flux:card>
    </div>
