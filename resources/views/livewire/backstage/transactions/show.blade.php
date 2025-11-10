<?php

use App\Models\Transaction;
use Livewire\Volt\Component;

new class extends Component {
    public Transaction $transaction;

    public function mount(Transaction $transaction)
    {
        $user = auth()->user();
        $org = $user?->currentOrganization;
        $marketplace = $org?->marketplace;
        // Ensure the transaction belongs to the user's current org's marketplace
        $transactionMarketplaceId = $transaction->listing?->marketplace_id ?? $transaction->marketplace_id;
        if (!$marketplace || $transactionMarketplaceId !== $marketplace->id) {
            abort(404);
        }
        $this->transaction = $transaction;
    }
}; ?>

<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Transaction #{{ $transaction->id }}</h1>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Status:</span>
        <span>{{ $transaction->status ?? 'N/A' }}</span>
    </div>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Total:</span>
        <span>${{ number_format($transaction->total, 2) }}</span>
    </div>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Created at:</span>
        <span>{{ $transaction->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</span>
    </div>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Listing:</span>
        <span>
            @if($transaction->listing)
                <a href="{{ route('backstage.listings.show', $transaction->listing) }}" class="text-blue-600 hover:underline">
                    {{ $transaction->listing->title ?? 'N/A' }}
                </a>
            @else
                N/A
            @endif
        </span>
    </div>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Buyer:</span>
        <span>{{ $transaction->user?->name ?? 'N/A' }}</span>
    </div>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Marketplace:</span>
        <span>{{ $transaction->listing?->marketplace?->name ?? 'N/A' }}</span>
    </div>
    <div class="mt-6 flex items-center gap-4">
        <a href="{{ route('backstage.transactions.index') }}" class="text-blue-600 hover:underline">&larr; Back to Transactions</a>
    </div>
</div>
