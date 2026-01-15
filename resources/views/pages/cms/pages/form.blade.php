<?php

use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public ?int $pageId = null;
    public ?Page $editing = null;

    #[Validate('required|string|min:3')]
    public string $title = '';

    #[Validate('required|string|min:3')]
    public string $slug = '';

    #[Validate('required|in:draft,published')]
    public string $status = 'draft';

    #[Validate('required|in:editor,static')]
    public string $type = 'editor';

    public array $content = ['blocks' => []];
    public ?string $rich_content = null;
    public ?string $static_view = null;
    public ?string $meta_title = null;
    public ?string $meta_description = null;

    public function mount(?int $pageId = null): void
    {
        $this->loadPage($pageId);
    }

    public function updatedPageId($pageId): void
    {
        $this->loadPage($pageId);
    }

    public function createNew(): void
    {
        $this->loadPage(null);
    }

    protected function loadPage(?int $pageId): void
    {
        $this->pageId = $pageId;
        $this->editing = $pageId ? Page::find($pageId) : null;

        if ($this->editing) {
            $page = $this->editing;
            $this->title = $page->title;
            $this->slug = $page->slug;
            $this->status = $page->status;
            $this->type = $page->type;
            $this->content = $page->content ?? ['blocks' => []];
            $this->rich_content = $page->content['html'] ?? null;
            $this->static_view = $page->static_view;
            $this->meta_title = $page->meta_title;
            $this->meta_description = $page->meta_description;
        } else {
            $this->reset(['title', 'slug', 'status', 'type', 'content', 'rich_content', 'static_view', 'meta_title', 'meta_description']);
            $this->status = 'draft';
            $this->type = 'editor';
            $this->content = ['blocks' => []];
            $this->rich_content = null;
        }

        $this->dispatch('page-editor-refreshed', content: $this->content, componentId: $this->getId());
    }

    public function updatedTitle(): void
    {
        if (! $this->editing) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function saveContent(array $payload): void
    {
        $this->content = $payload;
        $this->save();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'min:3'],
            'slug' => ['required', 'string', 'min:3', Rule::unique('pages', 'slug')->ignore($this->editing?->id)],
            'status' => ['required', 'in:draft,published'],
            'type' => ['required', 'in:editor,static'],
            'static_view' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
        ]);

        $data = array_merge($validated, [
            'content' => $this->type === 'editor'
                ? $this->content
                : (filled($this->rich_content) ? ['html' => $this->rich_content] : null),
            'published_at' => $this->status === 'published' ? now() : null,
        ]);

        if ($this->editing) {
            $this->editing->update($data);
        } else {
            $this->editing = Page::create($data);
            $this->pageId = $this->editing->id;
        }

        $this->dispatch('notify', title: 'Página guardada', body: 'Actualizamos la página con éxito.');
        $this->dispatch('page-saved');

        $this->redirectRoute('cms.pages', navigate: true);
    }

    public function delete(): void
    {
        if ($this->editing) {
            $this->editing->delete();
        }

        $this->redirectRoute('cms.pages', navigate: true);
    }
}; ?>

<div class="p-6">
    @vite('resources/css/editorjs.css')
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-50">{{ $editing ? 'Editar página' : 'Nueva página' }}</div>
            <p class="text-xs text-zinc-500">Completa los campos y guarda.</p>
        </div>
        <flux:badge color="amber" size="sm">{{ $status === 'published' ? 'Publicada' : 'Borrador' }}</flux:badge>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-1">
        <flux:input wire:model.live.debounce.500ms="title" label="Título" placeholder="Ej. Guía para invertir en SMA" />
        <flux:textarea wire:model.live="meta_description" label="Meta descripción" placeholder="Resumen breve" />
        <flux:select wire:model.live="status" label="Estado">
            <option value="draft">Borrador</option>
            <option value="published">Publicado</option>
        </flux:select>
    </div>

    <div class="mt-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-50">Contenido</div>
                <p class="text-xs text-zinc-500">Usa EditorJS o editor enriquecido.</p>
            </div>
            @if ($type === 'editor')
                <div class="flex items-center gap-2">
                    <flux:button size="sm" variant="ghost" wire:click="createNew">Limpiar</flux:button>
                    <button id="save-editor" type="button" class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white shadow hover:-translate-y-0.5 transition">Guardar contenido</button>
                </div>
            @endif
        </div>

            <div class="mt-3">
                <div
                    id="editorjs"
                    data-editor-content='@json($content ?? ['blocks' => []])'
                    data-editor-component-id="{{ $this->getId() }}"
                    class="min-h-[320px] rounded-xl border border-zinc-200 bg-white shadow-inner"
                    wire:ignore
                ></div>
            </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <flux:button variant="primary" wire:click="save">Guardar</flux:button>
        <flux:button variant="ghost" :href="route('cms.pages')" wire:navigate>Volver</flux:button>
        @if ($editing)
            <flux:button variant="ghost" color="danger" icon="trash" wire:click="delete">Eliminar</flux:button>
        @endif
    </div>
</div>
