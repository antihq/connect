<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public ?string $domain = null;

    public function mount()
    {
        $this->fill(
            $this->marketplace()->only(['domain'])
        );
    }

    public function save()
    {
        $this->authorize('update', $this->marketplace());

        $this->validate();

        $this->marketplace()->update([
            'domain' => $this->domain,
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
        return $this->user()->currentOrganization->marketplace;
    }

    protected function rules(): array
    {
        return [
            'domain' => [
                'nullable',
                'string',
                'max:255',
                'unique:marketplaces,domain,' . ($this->marketplace()->id ?? 'NULL'),
                'regex:/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
            ],
        ];
    }
}; ?>

<div>
    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model="domain" label="Custom Domain" placeholder="e.g. mymarket.com" />
        <flux:button type="submit" variant="primary">Save</flux:button>
    </form>
</div>

