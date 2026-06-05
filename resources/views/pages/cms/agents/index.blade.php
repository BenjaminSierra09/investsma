<?php

use App\Models\Agent;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function agents(): Collection
    {
        return Agent::query()->latest('id')->get();
    }
}; ?>

<div class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading class="text-xl">Agentes</flux:heading>
            <flux:subheading>Administra los perfiles que puedes asignar a los listados propios del dashboard.</flux:subheading>
        </div>
        <flux:button icon="plus" variant="primary" :href="route('cms.agents.form')" wire:navigate>Nuevo agente</flux:button>
    </div>

    <flux:card class="mt-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sticky>Nombre</flux:table.column>
                <flux:table.column>Cargo</flux:table.column>
                <flux:table.column>Contacto</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column align="end">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->agents as $agent)
                    <flux:table.row key="agent-{{ $agent->id }}">
                        <flux:table.cell variant="strong" sticky>{{ $agent->name }}</flux:table.cell>
                        <flux:table.cell>{{ $agent->title ?: '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $agent->email ?: ($agent->phone ?: '—') }}</flux:table.cell>
                        <flux:table.cell>{{ $agent->is_active ? 'Activo' : 'Inactivo' }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button size="xs" variant="ghost" :href="route('cms.agents.form', $agent->id)" wire:navigate>Editar</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center text-sm text-zinc-500">Aún no hay agentes.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
