<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;
use App\Models\Transaction;

new class extends Component {
    public Marketplace $marketplace;
    public Transaction $transaction;

    public function mount()
    {
        $this->transaction->load(['listing', 'user', 'activities']);
    }
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])

    <flux:card>
        <flux:heading size="lg" class="mb-4">Order Details</flux:heading>
        <flux:text class="mb-2">
            <strong>Listing:</strong>
            <flux:link :href="route('marketplaces.listings.show', [$transaction->listing->marketplace_id, $transaction->listing_id])">
                {{ $transaction->listing->title ?? 'N/A' }}
            </flux:link>
        </flux:text>
        <flux:text class="mb-2">
            <strong>Buyer:</strong> {{ $transaction->user->name ?? 'N/A' }} ({{ $transaction->user->email ?? '' }})
        </flux:text>
        <flux:text class="mb-2">
            <strong>Dates:</strong> {{ $transaction->start_date?->format('M d, Y') ?? '-' }} - {{ $transaction->end_date?->format('M d, Y') ?? '-' }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Nights:</strong> {{ $transaction->nights ?? '-' }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Price per night:</strong> ${{ number_format($transaction->price_per_night, 2) }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Total:</strong> <span class="font-bold">${{ number_format($transaction->total, 2) }}</span>
        </flux:text>
        <flux:text class="mb-2">
            <strong>Status:</strong>
            <flux:badge color="{{ $transaction->status === 'paid' ? 'green' : ($transaction->status === 'failed' ? 'red' : 'zinc') }}" size="sm" inset="top bottom">
                {{ ucfirst($transaction->status ?? 'pending') }}
            </flux:badge>
        </flux:text>
        <flux:text class="mb-2">
            <strong>Created at:</strong> {{ $transaction->created_at?->format('M d, Y H:i') ?? '-' }}
        </flux:text>
    </flux:card>

    <flux:card class="mt-8">
        <flux:heading size="md" class="mb-2">Activity Log</flux:heading>
        @if ($transaction->activities->isEmpty())
            <flux:text>No activity recorded for this transaction.</flux:text>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Description</flux:table.column>
                    <flux:table.column>Meta</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($transaction->activities as $activity)
                        <flux:table.row :key="$activity->id">
                            <flux:table.cell>{{ $activity->type }}</flux:table.cell>
                            <flux:table.cell>{{ $activity->description }}</flux:table.cell>
                            <flux:table.cell>
                                <pre class="text-xs bg-zinc-50 dark:bg-zinc-900 p-2 rounded">{{ json_encode($activity->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </flux:table.cell>
                            <flux:table.cell>{{ $activity->created_at?->format('M d, Y H:i') ?? '-' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:card>
</div>

