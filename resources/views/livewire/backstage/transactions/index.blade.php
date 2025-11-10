<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    #[Computed]
    public function transactions()
    {
        $user = Auth::user();
        $organization = $user?->currentOrganization;
        $marketplace = $organization?->marketplace;
        return $marketplace
            ? $marketplace->transactions()->latest()->get()
            : collect();
    }
}; ?>

<div>
    @include('partials.backstage-navbar')

    <flux:table>
        <flux:table.columns>
            <flux:table.column>ID</flux:table.column>
            <flux:table.column>Listing</flux:table.column>
            <flux:table.column>Buyer</flux:table.column>
            <flux:table.column>Total</flux:table.column>
            <flux:table.column>Date</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->transactions as $transaction)
                <flux:table.row :key="$transaction->id">
                    <flux:table.cell>{{ $transaction->id }}</flux:table.cell>
                    <flux:table.cell>{{ $transaction->listing->title ?? '-' }}</flux:table.cell>
                    <flux:table.cell>{{ $transaction->user->name ?? '-' }}</flux:table.cell>
                    <flux:table.cell>{{ number_format($transaction->total, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $transaction->created_at->format('Y-m-d') }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">No transactions found</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
