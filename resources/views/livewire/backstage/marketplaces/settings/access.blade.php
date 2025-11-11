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
    public string $restrict_view_listings_action = 'none';
    public ?string $restrict_view_listings_internal_link = null;
    public ?string $restrict_view_listings_internal_text = null;
    public ?string $restrict_view_listings_external_link = null;
    public ?string $restrict_view_listings_external_text = null;

    public bool $restrict_posting = false;
    public string $restrict_posting_action = 'none';
    public ?string $restrict_posting_internal_link = null;
    public ?string $restrict_posting_internal_text = null;
    public ?string $restrict_posting_external_link = null;
    public ?string $restrict_posting_external_text = null;

    public bool $restrict_transactions = false;
    public string $restrict_transactions_action = 'none';
    public ?string $restrict_transactions_internal_link = null;
    public ?string $restrict_transactions_internal_text = null;
    public ?string $restrict_transactions_external_link = null;
    public ?string $restrict_transactions_external_text = null;

    public bool $require_listing_approval = false;
    public string $require_listing_approval_action = 'none';
    public ?string $require_listing_approval_internal_link = null;
    public ?string $require_listing_approval_internal_text = null;
    public ?string $require_listing_approval_external_link = null;
    public ?string $require_listing_approval_external_text = null;

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
                    'restrict_view_listings_action',
                    'restrict_view_listings_internal_link',
                    'restrict_view_listings_internal_text',
                    'restrict_view_listings_external_link',
                    'restrict_view_listings_external_text',

                    'restrict_posting',
                    'restrict_posting_action',
                    'restrict_posting_internal_link',
                    'restrict_posting_internal_text',
                    'restrict_posting_external_link',
                    'restrict_posting_external_text',

                    'restrict_transactions',
                    'restrict_transactions_action',
                    'restrict_transactions_internal_link',
                    'restrict_transactions_internal_text',
                    'restrict_transactions_external_link',
                    'restrict_transactions_external_text',

                    'require_listing_approval',
                    'require_listing_approval_action',
                    'require_listing_approval_internal_link',
                    'require_listing_approval_internal_text',
                    'require_listing_approval_external_link',
                    'require_listing_approval_external_text',
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
            'restrict_view_listings_action' => $this->restrict_view_listings_action,
            'restrict_view_listings_internal_link' => $this->restrict_view_listings_internal_link,
            'restrict_view_listings_internal_text' => $this->restrict_view_listings_internal_text,
            'restrict_view_listings_external_link' => $this->restrict_view_listings_external_link,
            'restrict_view_listings_external_text' => $this->restrict_view_listings_external_text,

            'restrict_posting' => $this->restrict_posting,
            'restrict_posting_action' => $this->restrict_posting_action,
            'restrict_posting_internal_link' => $this->restrict_posting_internal_link,
            'restrict_posting_internal_text' => $this->restrict_posting_internal_text,
            'restrict_posting_external_link' => $this->restrict_posting_external_link,
            'restrict_posting_external_text' => $this->restrict_posting_external_text,

            'restrict_transactions' => $this->restrict_transactions,
            'restrict_transactions_action' => $this->restrict_transactions_action,
            'restrict_transactions_internal_link' => $this->restrict_transactions_internal_link,
            'restrict_transactions_internal_text' => $this->restrict_transactions_internal_text,
            'restrict_transactions_external_link' => $this->restrict_transactions_external_link,
            'restrict_transactions_external_text' => $this->restrict_transactions_external_text,

            'require_listing_approval' => $this->require_listing_approval,
            'require_listing_approval_action' => $this->require_listing_approval_action,
            'require_listing_approval_internal_link' => $this->require_listing_approval_internal_link,
            'require_listing_approval_internal_text' => $this->require_listing_approval_internal_text,
            'require_listing_approval_external_link' => $this->require_listing_approval_external_link,
            'require_listing_approval_external_text' => $this->require_listing_approval_external_text,
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
            'restrict_view_listings_action' => ['required_if:restrict_view_listings,true', 'in:none,internal,external'],
            'restrict_view_listings_internal_link' => ['nullable', 'string', 'required_if:restrict_view_listings_action,internal'],
            'restrict_view_listings_internal_text' => ['nullable', 'string', 'max:255', 'required_if:restrict_view_listings_action,internal'],
            'restrict_view_listings_external_link' => ['nullable', 'string', 'required_if:restrict_view_listings_action,external', 'url'],
            'restrict_view_listings_external_text' => ['nullable', 'string', 'max:255', 'required_if:restrict_view_listings_action,external'],

            'restrict_posting' => ['boolean'],
            'restrict_posting_action' => ['required_if:restrict_posting,true', 'in:none,internal,external'],
            'restrict_posting_internal_link' => ['nullable', 'string', 'required_if:restrict_posting_action,internal'],
            'restrict_posting_internal_text' => ['nullable', 'string', 'max:255', 'required_if:restrict_posting_action,internal'],
            'restrict_posting_external_link' => ['nullable', 'string', 'required_if:restrict_posting_action,external', 'url'],
            'restrict_posting_external_text' => ['nullable', 'string', 'max:255', 'required_if:restrict_posting_action,external'],

            'restrict_transactions' => ['boolean'],
            'restrict_transactions_action' => ['required_if:restrict_transactions,true', 'in:none,internal,external'],
            'restrict_transactions_internal_link' => ['nullable', 'string', 'required_if:restrict_transactions_action,internal'],
            'restrict_transactions_internal_text' => ['nullable', 'string', 'max:255', 'required_if:restrict_transactions_action,internal'],
            'restrict_transactions_external_link' => ['nullable', 'string', 'required_if:restrict_transactions_action,external', 'url'],
            'restrict_transactions_external_text' => ['nullable', 'string', 'max:255', 'required_if:restrict_transactions_action,external'],

            'require_listing_approval' => ['boolean'],
            'require_listing_approval_action' => ['required_if:require_listing_approval,true', 'in:none,internal,external'],
            'require_listing_approval_internal_link' => ['nullable', 'string', 'required_if:require_listing_approval_action,internal'],
            'require_listing_approval_internal_text' => ['nullable', 'string', 'max:255', 'required_if:require_listing_approval_action,internal'],
            'require_listing_approval_external_link' => ['nullable', 'string', 'required_if:require_listing_approval_action,external', 'url'],
            'require_listing_approval_external_text' => ['nullable', 'string', 'max:255', 'required_if:require_listing_approval_action,external'],
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
        <flux:radio.group wire:model="restrict_view_listings_action" label="Post-restriction call to action" x-bind:disabled="!$wire.restrict_view_listings">
            <flux:radio value="none" label="No call to action" />
            <flux:radio value="internal" label="Internal link" />
            <flux:radio value="external" label="External link" />
        </flux:radio.group>
        <flux:error name="restrict_view_listings_action" />
        <flux:field>
            <flux:label>Internal link (route or path)</flux:label>
            <flux:input
                wire:model="restrict_view_listings_internal_link"
                placeholder="/dashboard or route.name"
                x-bind:disabled="$wire.restrict_view_listings_action !== 'internal'"
            />
            <flux:error name="restrict_view_listings_internal_link" />
        </flux:field>
        <flux:field>
            <flux:label>Internal action text</flux:label>
            <flux:input
                wire:model="restrict_view_listings_internal_text"
                placeholder="Button or link text (e.g. Go to Dashboard)"
                x-bind:disabled="$wire.restrict_view_listings_action !== 'internal'"
            />
            <flux:error name="restrict_view_listings_internal_text" />
        </flux:field>
        <flux:field>
            <flux:label>External link (URL)</flux:label>
            <flux:input
                type="url"
                wire:model="restrict_view_listings_external_link"
                placeholder="https://example.com"
                x-bind:disabled="$wire.restrict_view_listings_action !== 'external'"
            />
            <flux:error name="restrict_view_listings_external_link" />
        </flux:field>
        <flux:field>
            <flux:label>External action text</flux:label>
            <flux:input
                wire:model="restrict_view_listings_external_text"
                placeholder="Button or link text (e.g. Visit Site)"
                x-bind:disabled="$wire.restrict_view_listings_action !== 'external'"
            />
            <flux:error name="restrict_view_listings_external_text" />
        </flux:field>

        <flux:field variant="inline">
            <flux:checkbox wire:model="restrict_posting" />
            <flux:label>Restrict posting rights</flux:label>
            <flux:error name="restrict_posting" />
        </flux:field>
        <flux:radio.group wire:model="restrict_posting_action" label="Post-restriction call to action" x-bind:disabled="!$wire.restrict_posting">
            <flux:radio value="none" label="No call to action" />
            <flux:radio value="internal" label="Internal link" />
            <flux:radio value="external" label="External link" />
        </flux:radio.group>
        <flux:error name="restrict_posting_action" />
        <flux:field>
            <flux:label>Internal link (route or path)</flux:label>
            <flux:input
                wire:model="restrict_posting_internal_link"
                placeholder="/dashboard or route.name"
                x-bind:disabled="$wire.restrict_posting_action !== 'internal'"
            />
            <flux:error name="restrict_posting_internal_link" />
        </flux:field>
        <flux:field>
            <flux:label>Internal action text</flux:label>
            <flux:input
                wire:model="restrict_posting_internal_text"
                placeholder="Button or link text (e.g. Go to Dashboard)"
                x-bind:disabled="$wire.restrict_posting_action !== 'internal'"
            />
            <flux:error name="restrict_posting_internal_text" />
        </flux:field>
        <flux:field>
            <flux:label>External link (URL)</flux:label>
            <flux:input
                type="url"
                wire:model="restrict_posting_external_link"
                placeholder="https://example.com"
                x-bind:disabled="$wire.restrict_posting_action !== 'external'"
            />
            <flux:error name="restrict_posting_external_link" />
        </flux:field>
        <flux:field>
            <flux:label>External action text</flux:label>
            <flux:input
                wire:model="restrict_posting_external_text"
                placeholder="Button or link text (e.g. Visit Site)"
                x-bind:disabled="$wire.restrict_posting_action !== 'external'"
            />
            <flux:error name="restrict_posting_external_text" />
        </flux:field>

        <flux:field variant="inline">
            <flux:checkbox wire:model="restrict_transactions" />
            <flux:label>Restrict transaction rights</flux:label>
            <flux:error name="restrict_transactions" />
        </flux:field>
        <flux:radio.group wire:model="restrict_transactions_action" label="Post-restriction call to action" x-bind:disabled="!$wire.restrict_transactions">
            <flux:radio value="none" label="No call to action" />
            <flux:radio value="internal" label="Internal link" />
            <flux:radio value="external" label="External link" />
        </flux:radio.group>
        <flux:error name="restrict_transactions_action" />
        <flux:field>
            <flux:label>Internal link (route or path)</flux:label>
            <flux:input
                wire:model="restrict_transactions_internal_link"
                placeholder="/dashboard or route.name"
                x-bind:disabled="$wire.restrict_transactions_action !== 'internal'"
            />
            <flux:error name="restrict_transactions_internal_link" />
        </flux:field>
        <flux:field>
            <flux:label>Internal action text</flux:label>
            <flux:input
                wire:model="restrict_transactions_internal_text"
                placeholder="Button or link text (e.g. Go to Dashboard)"
                x-bind:disabled="$wire.restrict_transactions_action !== 'internal'"
            />
            <flux:error name="restrict_transactions_internal_text" />
        </flux:field>
        <flux:field>
            <flux:label>External link (URL)</flux:label>
            <flux:input
                type="url"
                wire:model="restrict_transactions_external_link"
                placeholder="https://example.com"
                x-bind:disabled="$wire.restrict_transactions_action !== 'external'"
            />
            <flux:error name="restrict_transactions_external_link" />
        </flux:field>
        <flux:field>
            <flux:label>External action text</flux:label>
            <flux:input
                wire:model="restrict_transactions_external_text"
                placeholder="Button or link text (e.g. Visit Site)"
                x-bind:disabled="$wire.restrict_transactions_action !== 'external'"
            />
            <flux:error name="restrict_transactions_external_text" />
        </flux:field>

        <flux:field variant="inline">
            <flux:checkbox wire:model="require_listing_approval" />
            <flux:label>Require approval for listings before publishing</flux:label>
            <flux:error name="require_listing_approval" />
        </flux:field>
        <flux:radio.group wire:model="require_listing_approval_action" label="Post-approval call to action" x-bind:disabled="!$wire.require_listing_approval">
            <flux:radio value="none" label="No call to action" />
            <flux:radio value="internal" label="Internal link" />
            <flux:radio value="external" label="External link" />
        </flux:radio.group>
        <flux:error name="require_listing_approval_action" />
        <flux:field>
            <flux:label>Internal link (route or path)</flux:label>
            <flux:input
                wire:model="require_listing_approval_internal_link"
                placeholder="/dashboard or route.name"
                x-bind:disabled="$wire.require_listing_approval_action !== 'internal'"
            />
            <flux:error name="require_listing_approval_internal_link" />
        </flux:field>
        <flux:field>
            <flux:label>Internal action text</flux:label>
            <flux:input
                wire:model="require_listing_approval_internal_text"
                placeholder="Button or link text (e.g. Go to Dashboard)"
                x-bind:disabled="$wire.require_listing_approval_action !== 'internal'"
            />
            <flux:error name="require_listing_approval_internal_text" />
        </flux:field>
        <flux:field>
            <flux:label>External link (URL)</flux:label>
            <flux:input
                type="url"
                wire:model="require_listing_approval_external_link"
                placeholder="https://example.com"
                x-bind:disabled="$wire.require_listing_approval_action !== 'external'"
            />
            <flux:error name="require_listing_approval_external_link" />
        </flux:field>
        <flux:field>
            <flux:label>External action text</flux:label>
            <flux:input
                wire:model="require_listing_approval_external_text"
                placeholder="Button or link text (e.g. Visit Site)"
                x-bind:disabled="$wire.require_listing_approval_action !== 'external'"
            />
            <flux:error name="require_listing_approval_external_text" />
        </flux:field>
        <flux:button type="submit" variant="primary">Save</flux:button>
    </form>
</div>
