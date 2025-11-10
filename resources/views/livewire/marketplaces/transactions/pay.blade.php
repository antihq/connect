<?php

use Livewire\Volt\Component;

new class extends Component
{
    public \App\Models\Marketplace $marketplace;
    public \App\Models\Transaction $transaction;

    public function mount()
    {
        if ($this->transaction->user_id !== auth()->id()) {
            abort(403);
        }
        if ($this->transaction->listing->marketplace_id !== $this->marketplace->id) {
            abort(404);
        }
    }

    public function pay()
    {
        if ($this->transaction->status === 'paid') {
            return;
        }
        $this->transaction->update(['status' => 'paid']);
        session()->flash('success', 'Payment successful!');
        $marketplace = $this->transaction->listing->marketplace;
        return $this->redirectRoute('marketplaces.transactions.pay.confirmation', [
            'marketplace' => $marketplace->id,
            'transaction' => $this->transaction->id,
        ]);
    }
}; ?>

<div>
    <flux:card>
        <flux:heading size="lg" class="mb-2">Pay for Booking</flux:heading>
        <flux:text class="mb-2">
            <strong>Listing:</strong> {{ $transaction->listing->title }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Dates:</strong> {{ $transaction->start_date->format('M d, Y') }} - {{ $transaction->end_date->format('M d, Y') }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Nights:</strong> {{ $transaction->nights }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Price per night:</strong> ${{ number_format($transaction->price_per_night, 2) }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Total:</strong> ${{ number_format($transaction->total, 2) }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Status:</strong> {{ ucfirst($transaction->status) }}
        </flux:text>
        @if ($transaction->status !== 'paid')
            <form wire:submit="pay">
                <flux:button type="submit" color="primary" class="mt-4 w-full">Pay Now</flux:button>
            </form>
        @else
            <flux:text class="mt-4 text-green-600">Already paid.</flux:text>
        @endif
    </flux:card>
</div>
