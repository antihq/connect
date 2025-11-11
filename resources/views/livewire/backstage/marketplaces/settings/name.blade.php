<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $slug = '';

    public function mount()
    {
        $this->fill(
            $this->marketplace()->only(['name', 'slug'])
        );
    }

    public function save()
    {
        $this->authorize('update', $this->marketplace());
        $this->validate();
        $this->marketplace()->update([
            'name' => $this->name,
            'slug' => $this->slug,
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
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:marketplaces,slug,' . ($this->marketplace()->id ?? 'NULL')],
        ];
    }
}; ?>

<div>
    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model="name" label="Marketplace Name" />
        <flux:input wire:model="slug" label="Marketplace Slug" />
        <flux:button type="submit" variant="primary">Save</flux:button>
    </form>
</div>
