<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $sender_email_name = '';
    public $marketplace;

    public function mount()
    {
        $org = \Illuminate\Support\Facades\Auth::user()?->currentOrganization;
        abort_unless($org && $org->marketplace, 404);
        $this->marketplace = $org->marketplace;
        $this->fill(
            $this->marketplace->only(['sender_email_name'])
        );
    }

    public function save()
    {
        $this->authorize('update', $this->marketplace);
        $this->validate();
        $this->marketplace->update([
            'sender_email_name' => $this->sender_email_name,
        ]);
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
