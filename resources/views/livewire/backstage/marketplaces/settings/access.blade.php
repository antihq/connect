<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public bool $is_private = false;
    public bool $require_user_approval = false;
    public string $require_user_approval_action = 'none';
    public ?string $require_user_approval_internal_link = null;
    public ?string $require_user_approval_internal_text = null;
    public ?string $require_user_approval_external_link = null;
    public ?string $require_user_approval_external_text = null;
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
                'require_user_approval_action',
                'require_user_approval_internal_link',
                'require_user_approval_internal_text',
                'require_user_approval_external_link',
                'require_user_approval_external_text',
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

        if (!$this->is_private) {
            $this->restrict_view_listings = false;
        }

        $this->validate();

        $this->marketplace()->update([
            'is_private' => $this->is_private,
            'require_user_approval' => $this->require_user_approval,
            'require_user_approval_action' => $this->require_user_approval_action,
            'require_user_approval_internal_link' => $this->require_user_approval_internal_link,
            'require_user_approval_internal_text' => $this->require_user_approval_internal_text,
            'require_user_approval_external_link' => $this->require_user_approval_external_link,
            'require_user_approval_external_text' => $this->require_user_approval_external_text,
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
            'require_user_approval_action' => ['required_if:require_user_approval,true', 'in:none,internal,external'],
            'require_user_approval_internal_link' => ['nullable', 'string', 'required_if:require_user_approval_action,internal'],
            'require_user_approval_internal_text' => ['nullable', 'string', 'max:255', 'required_if:require_user_approval_action,internal'],
            'require_user_approval_external_link' => ['nullable', 'string', 'required_if:require_user_approval_action,external', 'url'],
            'require_user_approval_external_text' => ['nullable', 'string', 'max:255', 'required_if:require_user_approval_action,external'],
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

        <flux:radio.group wire:model="require_user_approval_action" label="Post-approval call to action" x-bind:disabled="! $wire.require_user_approval">
            <flux:radio value="none" label="No call to action" />
            <flux:radio value="internal" label="Internal link" />
            <flux:radio value="external" label="External link" />
        </flux:radio.group>
        <flux:error name="require_user_approval_action" />

        <flux:field>
            <flux:label>Internal link (route or path)</flux:label>
            <flux:input
                wire:model="require_user_approval_internal_link"
                placeholder="/dashboard or route.name"
                x-bind:disabled="$wire.require_user_approval_action !== 'internal'"
            />
            <flux:error name="require_user_approval_internal_link" />
        </flux:field>
        <flux:field>
            <flux:label>Internal action text</flux:label>
            <flux:input
                wire:model="require_user_approval_internal_text"
                placeholder="Button or link text (e.g. Go to Dashboard)"
                x-bind:disabled="$wire.require_user_approval_action !== 'internal'"
            />
            <flux:error name="require_user_approval_internal_text" />
        </flux:field>

        <flux:field>
            <flux:label>External link (URL)</flux:label>
            <flux:input
                type="url"
                wire:model="require_user_approval_external_link"
                placeholder="https://example.com"
                x-bind:disabled="$wire.require_user_approval_action !== 'external'"
            />
            <flux:error name="require_user_approval_external_link" />
        </flux:field>
        <flux:field>
            <flux:label>External action text</flux:label>
            <flux:input
                wire:model="require_user_approval_external_text"
                placeholder="Button or link text (e.g. Visit Site)"
                x-bind:disabled="$wire.require_user_approval_action !== 'external'"
            />
            <flux:error name="require_user_approval_external_text" />
        </flux:field>
        <flux:field variant="inline">
            <flux:checkbox wire:model="restrict_view_listings" x-bind:disabled="!$wire.is_private" />
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
