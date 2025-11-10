<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';

    public function mount()
    {
        $this->name = $this->marketplace()->name ?? '';
    }

    public function save()
    {
        $this->authorize('update', $this->marketplace());
        $this->validate();
        $this->marketplace()->update([
            'name' => $this->name,
        ]);
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function marketplace()
    {
        return $this->user()
            ->currentOrganization
            ->marketplace;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}; ?>

<div>
    <form wire:submit="save">
        <label for="marketplace-name">Marketplace Name</label>
        <input id="marketplace-name" type="text" wire:model="name" />
        @error('name')
            <div class="text-red-500">{{ $message }}</div>
        @enderror
        <button type="submit">Save</button>
    </form>
</div>
