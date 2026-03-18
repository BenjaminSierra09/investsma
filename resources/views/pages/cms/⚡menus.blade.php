<?php

use App\Models\MenuItem;
use App\Models\Page;
use App\Support\StaticPageRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public string $menu = 'main';

    /** @var array<int, array<string, mixed>> */
    public array $items = [];

    public function mount(): void
    {
        $this->loadItems();
    }

    public function loadItems(): void
    {
        $rows = MenuItem::forMenu($this->menu)->get();
        $tempMap = [];

        foreach ($rows as $row) {
            $tempMap[$row->id] = (string) Str::uuid();
        }

        $this->items = $rows->map(function (MenuItem $item) use ($tempMap) {
            return [
                'id' => $item->id,
                'temp_id' => $tempMap[$item->id],
                'parent_temp_id' => $item->parent_id ? $tempMap[$item->parent_id] ?? null : null,
                'label' => $item->label,
                'type' => $item->type,
                'page_id' => $item->page_id,
                'static_key' => $item->static_key,
                'url' => $item->url,
                'order' => $item->order,
            ];
        })->values()->all();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id' => null,
            'temp_id' => (string) Str::uuid(),
            'parent_temp_id' => null,
            'label' => 'Nuevo',
            'type' => 'page',
            'page_id' => null,
            'static_key' => null,
            'url' => null,
            'order' => count($this->items),
        ];
    }

    public function removeItem(string $tempId): void
    {
        $this->items = collect($this->items)
            ->reject(fn ($item) => $item['temp_id'] === $tempId || $item['parent_temp_id'] === $tempId)
            ->values()
            ->all();
    }

    public function sortItem(string $tempId, int $position): void
    {
        $tempIds = collect($this->items)->pluck('temp_id')->values()->all();
        $currentIndex = array_search($tempId, $tempIds, true);

        if ($currentIndex === false) {
            return;
        }

        $targetPosition = max(0, min($position, count($tempIds) - 1));

        array_splice($tempIds, $currentIndex, 1);
        array_splice($tempIds, $targetPosition, 0, [$tempId]);

        $itemsByTempId = collect($this->items)->keyBy('temp_id');

        $this->items = collect($tempIds)
            ->map(function (string $orderedTempId, int $index) use ($itemsByTempId) {
                $item = $itemsByTempId->get($orderedTempId);

                if (! is_array($item)) {
                    return null;
                }

                $item['order'] = $index;

                return $item;
            })
            ->filter()
            ->values()
            ->all();
    }

    public function updateItemLabel(string $tempId, string $label): void
    {
        $index = collect($this->items)->search(
            fn (array $item): bool => $item['temp_id'] === $tempId
        );

        if ($index === false) {
            return;
        }

        $this->items[$index]['label'] = $label;
    }

    public function save(): void
    {
        $this->validate([
            'items' => 'array',
            'items.*.label' => 'required|string',
            'items.*.type' => 'required|in:page,static,url',
            'items.*.page_id' => 'nullable|exists:pages,id',
            'items.*.static_key' => 'nullable|string',
            'items.*.url' => 'nullable|string',
            'items.*.parent_temp_id' => 'nullable|string',
        ]);

        $ordered = collect($this->items)->sortBy('order')->values();

        DB::transaction(function () use ($ordered) {
            $idMap = [];

            foreach ($ordered as $index => $item) {
                $model = $item['id'] ? MenuItem::find($item['id']) : new MenuItem();

                $model->fill([
                    'menu' => $this->menu,
                    'label' => $item['label'],
                    'type' => $item['type'],
                    'page_id' => $item['type'] === 'page' ? $item['page_id'] : null,
                    'static_key' => $item['type'] === 'static' ? $item['static_key'] : null,
                    'url' => $item['type'] === 'url' ? $item['url'] : null,
                    'order' => $index,
                ]);

                $model->parent_id = null;
                $model->save();

                $idMap[$item['temp_id']] = $model->id;
            }

            foreach ($ordered as $item) {
                $parentId = $item['parent_temp_id'] ? ($idMap[$item['parent_temp_id']] ?? null) : null;
                $model = MenuItem::find($idMap[$item['temp_id']] ?? null);

                if ($model && $model->parent_id !== $parentId) {
                    $model->parent_id = $parentId;
                    $model->save();
                }
            }
        });

        $this->loadItems();
        $this->dispatch('notify', title: 'Menú guardado', body: 'Actualizamos el menú principal.');
    }

    #[Computed]
    public function availablePages()
    {
        return Page::orderBy('title')->get();
    }

    #[Computed]
    public function staticOptions()
    {
        return StaticPageRegistry::options();
    }
}; ?>

<div class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading class="text-xl">Menú principal</flux:heading>
            <flux:subheading>Organiza páginas, vistas fijas o enlaces externos.</flux:subheading>
        </div>
        <flux:button icon="plus" variant="primary" wire:click="addItem">Agregar elemento</flux:button>
    </div>

    <div class="mt-6 rounded-xl border border-zinc-200 bg-white/80 p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-zinc-500">
                    <tr>
                        <th class="px-3 py-2">Etiqueta</th>
                        <th class="px-3 py-2">Tipo</th>
                        <th class="px-3 py-2">Destino</th>
                        <th class="px-3 py-2">Padre</th>
                        <th class="px-3 py-2">Orden</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100" wire:sort="sortItem">
                    @forelse ($items as $index => $item)
                        <tr class="align-top" wire:key="menu-item-{{ $item['temp_id'] }}" wire:sort:item="{{ $item['temp_id'] }}">
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    @if ($item['parent_temp_id'])
                                        <span class="text-xs text-zinc-400">↳</span>
                                    @endif
                                    <flux:input
                                        :value="$item['label']"
                                        size="sm"
                                        wire:input.debounce.300ms="updateItemLabel('{{ $item['temp_id'] }}', $event.target.value)"
                                    />
                                </div>
                            </td>
                            <td class="px-3 py-3">
                                <flux:select wire:model.live="items.{{ $index }}.type" size="sm">
                                    <option value="page">Página (Editor)</option>
                                    <option value="static">Página fija</option>
                                    <option value="url">Enlace externo</option>
                                </flux:select>
                            </td>
                            <td class="px-3 py-3 space-y-2">
                                @if ($item['type'] === 'page')
                                    <flux:select wire:model.live="items.{{ $index }}.page_id" size="sm">
                                        <option value="">Selecciona una página</option>
                                        @foreach ($this->availablePages as $page)
                                            <option value="{{ $page->id }}">{{ $page->title }}</option>
                                        @endforeach
                                    </flux:select>
                                @elseif ($item['type'] === 'static')
                                    <flux:select wire:model.live="items.{{ $index }}.static_key" size="sm">
                                        <option value="">Selecciona</option>
                                        @foreach ($this->staticOptions as $option)
                                            <option value="{{ $option['key'] }}">{{ $option['label'] }}</option>
                                        @endforeach
                                    </flux:select>
                                @else
                                    <flux:input wire:model.live="items.{{ $index }}.url" size="sm" placeholder="https://" />
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <flux:select wire:model.live="items.{{ $index }}.parent_temp_id" size="sm">
                                    <option value="">Sin padre</option>
                                    @foreach ($items as $maybeParent)
                                        @if ($maybeParent['temp_id'] !== $item['temp_id'])
                                            <option value="{{ $maybeParent['temp_id'] }}">{{ $maybeParent['label'] }}</option>
                                        @endif
                                    @endforeach
                                </flux:select>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <flux:button type="button" size="xs" variant="ghost" wire:sort:handle>Arrastrar</flux:button>
                                    <span class="text-xs text-zinc-500">{{ $index + 1 }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-right">
                                <flux:button icon="trash" size="xs" variant="ghost" wire:click="removeItem('{{ $item['temp_id'] }}')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-sm text-zinc-500">Agrega elementos para construir el menú.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <flux:button variant="primary" wire:click="save">Guardar menú</flux:button>
            <flux:button variant="ghost" wire:click="loadItems">Descartar cambios</flux:button>
        </div>
    </div>
</div>
