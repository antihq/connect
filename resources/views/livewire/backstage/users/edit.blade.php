<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
    public User $user;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $bio = '';

    public function mount()
    {
        $this->first_name = $this->user->first_name ?? '';
        $this->last_name = $this->user->last_name ?? '';
        $this->email = $this->user->email ?? '';
        $this->bio = $this->user->bio ?? '';
    }

    public function save()
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'bio' => $this->bio,
        ]);

        session()->flash('success', 'User updated successfully.');
        return redirect()->route('backstage.users.show', $this->user);
    }
}; ?>

<div>
    @include('partials.backstage-navbar')
    <flux:card>
        <h2 class="text-xl font-bold mb-2">Edit User</h2>
        <form wire:submit="save">
            <flux:input label="First Name" wire:model.defer="first_name" />
            <flux:input label="Last Name" wire:model.defer="last_name" />
            <flux:input label="Email" type="email" wire:model.defer="email" />
            <flux:textarea label="Bio" wire:model.defer="bio" />
            <div class="mt-4">
                <flux:button type="submit">Save</flux:button>
                <flux:link :href="route('backstage.users.show', $user)" class="ml-2">Cancel</flux:link>
            </div>
        </form>
    </flux:card>
</div>
