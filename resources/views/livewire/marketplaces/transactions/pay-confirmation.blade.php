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
}; ?>

<div>
    <flux:card>
        <flux:heading size="lg" class="mb-2">Payment Successful</flux:heading>
        <flux:text class="mb-2">
            Thank you for your payment! Your booking is now confirmed.
        </flux:text>
        <flux:text class="mb-2">
            <strong>Listing:</strong> {{ $transaction->listing->title }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Dates:</strong> {{ $transaction->start_date->format('M d, Y') }} - {{ $transaction->end_date->format('M d, Y') }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Total Paid:</strong> ${{ number_format($transaction->total, 2) }}
        </flux:text>
        <flux:button href="/" color="primary" class="mt-4 w-full">Back to Home</flux:button>
    </flux:card>
</div>
