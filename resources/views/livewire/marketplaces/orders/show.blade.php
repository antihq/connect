<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;
use App\Models\Transaction;

new class extends Component {
    public Marketplace $marketplace;
    public Transaction $transaction;
    public string $message = '';

    public function mount()
    {
        $this->transaction->load(['listing', 'user', 'activities.user']);
    }

    public function postMessage()
    {
        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Only buyer or provider can post
        $user = auth()->user();
        $isBuyer = $user->id === $this->transaction->user_id;
        $isProvider = $user->id === $this->transaction->listing->user_id;
        if (!($isBuyer || $isProvider)) {
            abort(403);
        }

        $this->transaction->activities()->create([
            'type' => 'message',
            'description' => $this->message,
            'user_id' => $user->id,
        ]);

        $this->message = '';
        $this->transaction->load(['activities.user']);
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
            <div class="space-y-4">
                @foreach ($transaction->activities as $activity)
                    @if ($activity->type === 'message')
                        <div class="flex items-start gap-3 p-3 rounded bg-zinc-50 dark:bg-zinc-900">
                            <div class="font-bold text-sm">
                                @if ($activity->user_id === auth()->id())
                                    You
                                @elseif ($activity->user_id === $transaction->user_id)
                                    Buyer
                                @elseif ($activity->user_id === $transaction->listing->user_id)
                                    Provider
                                @else
                                    User #{{ $activity->user_id }}
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="text-sm">{{ $activity->description }}</div>
                                <div class="text-xs text-zinc-500 mt-1">{{ $activity->created_at?->format('M d, Y H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2 p-2 rounded bg-zinc-100 dark:bg-zinc-800 text-xs">
                            <span class="font-semibold">[{{ ucfirst($activity->type) }}]</span>
                            <span>{{ $activity->description }}</span>
                            @if ($activity->meta)
                                <span class="ml-2"><pre class="inline">{{ json_encode($activity->meta, JSON_UNESCAPED_SLASHES) }}</pre></span>
                            @endif
                            <span class="ml-auto text-zinc-400">{{ $activity->created_at?->format('M d, Y H:i') ?? '-' }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </flux:card>

    <flux:card class="mt-6">
        <form wire:submit="postMessage">
            <flux:heading size="sm" class="mb-2">Send a message</flux:heading>
            <flux:textarea wire:model.defer="message" placeholder="Type your message..." rows="3" class="mb-2" />
            @error('message') <div class="text-red-500 text-xs mb-2">{{ $message }}</div> @enderror
            <flux:button type="submit" color="primary">Send</flux:button>
        </form>
    </flux:card>
</div>

