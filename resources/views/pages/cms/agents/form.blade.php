<?php

use App\Models\Agent;
use Livewire\Component;

new class extends Component
{
    public ?int $agentId = null;

    public ?Agent $editing = null;

    public string $name = '';

    public ?string $title = null;

    public ?string $email = null;

    public ?string $phone = null;

    public ?string $whatsapp = null;

    public ?string $photo_url = null;

    public ?string $bio = null;

    public bool $is_active = true;

    public function mount(?int $agentId = null): void
    {
        $this->loadAgent($agentId);
    }

    public function loadAgent(?int $agentId = null): void
    {
        $this->agentId = $agentId;
        $this->editing = $agentId ? Agent::find($agentId) : null;

        if ($this->editing) {
            $agent = $this->editing;

            $this->name = $agent->name;
            $this->title = $agent->title;
            $this->email = $agent->email;
            $this->phone = $agent->phone;
            $this->whatsapp = $agent->whatsapp;
            $this->photo_url = $agent->photo_url;
            $this->bio = $agent->bio;
            $this->is_active = $agent->is_active;

            return;
        }

        $this->reset([
            'name',
            'title',
            'email',
            'phone',
            'whatsapp',
            'photo_url',
            'bio',
        ]);

        $this->is_active = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'photo_url' => ['nullable', 'url', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:2500'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($this->editing) {
            $this->editing->update($validated);
        } else {
            $this->editing = Agent::create($validated);
            $this->agentId = $this->editing->id;
        }

        $this->dispatch('notify', title: 'Agente guardado', body: 'Actualizamos el perfil con éxito.');
        $this->redirectRoute('cms.agents', navigate: true);
    }

    public function delete(): void
    {
        if ($this->editing) {
            $this->editing->delete();
        }

        $this->redirectRoute('cms.agents', navigate: true);
    }
}; ?>

<div class="p-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-50">{{ $editing ? 'Editar agente' : 'Nuevo agente' }}</div>
            <p class="text-xs text-zinc-500">Este perfil se podrá asignar a propiedades creadas desde el dashboard.</p>
        </div>
        <flux:badge :color="$is_active ? 'emerald' : 'zinc'" size="sm">{{ $is_active ? 'Activo' : 'Inactivo' }}</flux:badge>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <flux:card>
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.live="name" label="Nombre" placeholder="María González" />
                <flux:input wire:model.live="title" label="Cargo" placeholder="Asesora inmobiliaria" />
                <flux:input wire:model.live="email" label="Correo" placeholder="maria@investsma.com" />
                <flux:input wire:model.live="phone" label="Teléfono" placeholder="+52 415 123 4567" />
                <flux:input wire:model.live="whatsapp" label="WhatsApp" placeholder="524151234567" />
                <flux:input wire:model.live="photo_url" label="Foto (URL)" placeholder="https://..." />
            </div>

            <div class="mt-4">
                <flux:textarea wire:model.live="bio" label="Bio" rows="8" placeholder="Describe la experiencia, especialidad y enfoque comercial del agente." />
            </div>

            <div class="mt-4">
                <flux:checkbox wire:model.live="is_active" label="Disponible para asignar a listados" />
            </div>
        </flux:card>

        <div class="space-y-6">
            <flux:card>
                <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-50">Vista previa</div>

                <div class="mt-4 rounded-[28px] bg-zinc-900 p-6 text-white">
                    @if ($photo_url)
                        <img src="{{ $photo_url }}" alt="{{ $name ?: 'Foto del agente' }}" class="h-20 w-20 rounded-full object-cover ring-4 ring-white/10">
                    @else
                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-white/10 text-2xl font-semibold text-white/80">
                            {{ str($name)->trim()->explode(' ')->filter()->take(2)->map(fn ($part) => str($part)->substr(0, 1))->join('') ?: 'AG' }}
                        </div>
                    @endif

                    <div class="mt-4">
                        <div class="text-xl font-semibold">{{ $name ?: 'Nombre del agente' }}</div>
                        @if ($title)
                            <div class="mt-1 text-sm text-amber-300">{{ $title }}</div>
                        @endif
                    </div>

                    @if ($bio)
                        <p class="mt-4 text-sm leading-relaxed text-zinc-300">{{ $bio }}</p>
                    @endif

                    <div class="mt-4 space-y-1 text-sm text-zinc-300">
                        @if ($phone)
                            <div>{{ $phone }}</div>
                        @endif
                        @if ($email)
                            <div>{{ $email }}</div>
                        @endif
                        @if ($whatsapp)
                            <div>WhatsApp: {{ $whatsapp }}</div>
                        @endif
                    </div>
                </div>
            </flux:card>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <flux:button variant="primary" wire:click="save">Guardar agente</flux:button>
        <flux:button variant="ghost" :href="route('cms.agents')" wire:navigate>Volver</flux:button>
        @if ($editing)
            <flux:button variant="ghost" color="danger" icon="trash" wire:click="delete">Eliminar</flux:button>
        @endif
    </div>
</div>
