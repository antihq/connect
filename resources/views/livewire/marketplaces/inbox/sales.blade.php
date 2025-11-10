<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Marketplace;

new class extends Component {
    public Marketplace $marketplace;
    public $transactions;

    public function mount()
    {
        $this->transactions = Auth::user()
            ->listings()
            ->where('marketplace_id', $this->marketplace->id)
            ->with(['transactions.user'])
            ->get()
            ->flatMap(fn($listing) => $listing->transactions)
            ->sortByDesc('created_at');
    }
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])
    @include('partials.marketplace-inbox-navbar', ['marketplace' => $marketplace])

    <flux:heading size="lg" class="mb-4">Your Sales</flux:heading>
    @if ($transactions->isEmpty())
        <flux:text>You have not made any sales yet.</flux:text>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Buyer</flux:table.column>
                <flux:table.column>Listing</flux:table.column>
                <flux:table.column>Start Date</flux:table.column>
                <flux:table.column>End Date</flux:table.column>
                <flux:table.column>Nights</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($transactions as $transaction)
                    <flux:table.row :key="$transaction->id">
                        <flux:table.cell>
                            {{ $transaction->user->name ?? 'N/A' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:link :href="route('marketplaces.orders.show', [$transaction->listing->marketplace_id, $transaction->id])">
                                {{ $transaction->listing->title ?? 'N/A' }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $transaction->start_date?->format('Y-m-d') ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $transaction->end_date?->format('Y-m-d') ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $transaction->nights ?? '-' }}</flux:table.cell>
                        <flux:table.cell variant="strong">${{ number_format($transaction->total, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $transaction->status === 'paid' ? 'green' : ($transaction->status === 'failed' ? 'red' : 'zinc') }}" size="sm" inset="top bottom">
                                {{ ucfirst($transaction->status ?? 'pending') }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</div>
