<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use App\Models\User;

new class extends Component {
    #[Computed]
    public function users()
    {
        return User::query()->latest()->get();
    }
}; ?>

<div>
    @include('partials.backstage-navbar')

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Created</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($this->users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>{{ $user->created_at->format('Y-m-d') }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
