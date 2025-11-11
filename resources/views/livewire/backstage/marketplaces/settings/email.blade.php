<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $sender_email_name = '';

    public function mount()
    {
        $this->fill(
            $this->marketplace()->only(['sender_email_name'])
        );
    }

    public function save()
    {
        $this->authorize('update', $this->marketplace());

        $this->validate();

        $this->marketplace()->update([
            'sender_email_name' => $this->sender_email_name,
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
            'sender_email_name' => ['required', 'string', 'max:255'],
        ];
    }
}; ?>

<div>
    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model="sender_email_name" label="Sender Email Name" placeholder="e.g. Acme Marketplace" />
        <flux:button type="submit" variant="primary">Save</flux:button>
    </form>
</div>
