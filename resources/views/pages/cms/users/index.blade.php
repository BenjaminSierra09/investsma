<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function users(): Collection
    {
        return User::query()
            ->latest('id')
            ->get();
    }
}; ?>

<div class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading class="text-xl">Usuarios</flux:heading>
            <flux:subheading>Administra los accesos al panel interno.</flux:subheading>
        </div>
    </div>

    <flux:card class="mt-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sticky>Nombre</flux:table.column>
                <flux:table.column>Correo</flux:table.column>
                <flux:table.column>Verificado</flux:table.column>
                <flux:table.column>Registro</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->users as $user)
                    <flux:table.row key="user-{{ $user->id }}">
                        <flux:table.cell variant="strong" sticky>{{ $user->name }}</flux:table.cell>
                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                        <flux:table.cell>{{ $user->email_verified_at?->format('Y-m-d H:i') ?? 'Pendiente' }}</flux:table.cell>
                        <flux:table.cell>{{ $user->created_at?->format('Y-m-d H:i') ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center text-sm text-zinc-500">Aún no hay usuarios registrados.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
