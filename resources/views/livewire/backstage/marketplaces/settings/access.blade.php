<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public bool $is_private = false;
    public bool $require_user_approval = false;
    public bool $restrict_view_listings = false;
    public bool $restrict_posting = false;
    public bool $restrict_transactions = false;
    public bool $require_listing_approval = false;

    public function mount()
    {
        $this->fill(
            $this->marketplace()->only([
                'is_private',
                'require_user_approval',
                'restrict_view_listings',
                'restrict_posting',
                'restrict_transactions',
                'require_listing_approval',
            ])
        );
    }

    public function save()
    {
        $this->authorize('update', $this->marketplace());

        $this->validate();

        $this->marketplace()->update([
            'is_private' => $this->is_private,
            'require_user_approval' => $this->require_user_approval,
            'restrict_view_listings' => $this->restrict_view_listings,
            'restrict_posting' => $this->restrict_posting,
            'restrict_transactions' => $this->restrict_transactions,
            'require_listing_approval' => $this->require_listing_approval,
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
            'is_private' => ['boolean'],
            'require_user_approval' => ['boolean'],
            'restrict_view_listings' => ['boolean'],
            'restrict_posting' => ['boolean'],
            'restrict_transactions' => ['boolean'],
            'require_listing_approval' => ['boolean'],
        ];
    }
}; ?>

<div>
    <form wire:submit="save" class="space-y-6">
        <flux:field variant="inline">
            <flux:checkbox wire:model="is_private" />
            <flux:label>Make marketplace private</flux:label>
            <flux:error name="is_private" />
        </flux:field>
        <flux:field variant="inline">
            <flux:checkbox wire:model="require_user_approval" />
            <flux:label>Require approval for users to join</flux:label>
            <flux:error name="require_user_approval" />
        </flux:field>
        <flux:field variant="inline">
            <flux:checkbox wire:model="restrict_view_listings" />
            <flux:label>Restrict right to view listings</flux:label>
            <flux:error name="restrict_view_listings" />
        </flux:field>
        <flux:field variant="inline">
            <flux:checkbox wire:model="restrict_posting" />
            <flux:label>Restrict posting rights</flux:label>
            <flux:error name="restrict_posting" />
        </flux:field>
        <flux:field variant="inline">
            <flux:checkbox wire:model="restrict_transactions" />
            <flux:label>Restrict transaction rights</flux:label>
            <flux:error name="restrict_transactions" />
        </flux:field>
        <flux:field variant="inline">
            <flux:checkbox wire:model="require_listing_approval" />
            <flux:label>Require approval for listings before publishing</flux:label>
            <flux:error name="require_listing_approval" />
        </flux:field>
        <flux:button type="submit" variant="primary">Save</flux:button>
    </form>
</div>
