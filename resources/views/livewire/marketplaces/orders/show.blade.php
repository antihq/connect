<?php

use App\Models\Marketplace;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use Livewire\Volt\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public Transaction $transaction;

    public string $message = '';

    public int $review_rating = 0;

    public string $review_comment = '';

    public bool $review_submitted = false;

    public $reviews = [];

    public function mount()
    {
        $this->transaction->load(['listing', 'user', 'activities.user']);
        $this->review_submitted = $this->hasReviewed();
        // Eager load all reviews for this transaction, keyed by reviewer_id
        $this->reviews = Review::where('transaction_id', $this->transaction->id)
            ->get()
            ->keyBy('reviewer_id');
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
        if (! ($isBuyer || $isProvider)) {
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

    public function submitReview()
    {
        $user = auth()->user();
        $providerId = $this->transaction->listing->user_id;
        $customerId = $this->transaction->user_id;
        // Only customer can review provider in orders
        if ($user->id !== $customerId) {
            abort(403);
        }
        if ($this->transaction->status !== 'completed') {
            abort(403);
        }
        if ($this->hasReviewed()) {
            abort(403);
        }
        $this->validate([
            'review_rating' => 'required|integer|min:1|max:5',
            'review_comment' => 'required|string|max:1000',
        ]);
        // Save review
        Review::create([
            'transaction_id' => $this->transaction->id,
            'reviewer_id' => $customerId,
            'reviewee_id' => $providerId,
            'rating' => $this->review_rating,
            'comment' => $this->review_comment,
        ]);
        // Log activity
        $this->transaction->activities()->create([
            'type' => 'review',
            'description' => 'Customer reviewed the provider: '.$this->review_comment,
            'user_id' => $customerId,
        ]);
        $this->review_submitted = true;
        $this->transaction->load(['activities.user']);
        session()->flash('success', 'Review submitted');
    }

    private function hasReviewed(): bool
    {
        $customerId = $this->transaction->user_id;

        return TransactionActivity::where('transaction_id', $this->transaction->id)
            ->where('type', 'review')
            ->where('user_id', $customerId)
            ->exists();
    }
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])

    <flux:card>
        <flux:heading size="lg" class="mb-4">Order Details</flux:heading>
        <flux:text class="mb-2">
            <strong>Listing:</strong>
            <flux:link
                :href="route('on-marketplace.listings.show', [$transaction->listing->marketplace_id, $transaction->listing_id])"
            >
                {{ $transaction->listing->title ?? 'N/A' }}
            </flux:link>
        </flux:text>
        <flux:text class="mb-2">
            <strong>Buyer:</strong>
            {{ $transaction->user?->name ?? 'N/A' }} ({{ $transaction->user?->email ?? '' }})
        </flux:text>
        <flux:text class="mb-2">
            <strong>Dates:</strong>
            {{ $transaction->start_date?->format('M d, Y') ?? '-' }} -
            {{ $transaction->end_date?->format('M d, Y') ?? '-' }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Nights:</strong>
            {{ $transaction->nights ?? '-' }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Price per night:</strong>
            ${{ number_format($transaction->price_per_night, 2) }}
        </flux:text>
        <flux:text class="mb-2">
            <strong>Total:</strong>
            <span class="font-bold">${{ number_format($transaction->total, 2) }}</span>
        </flux:text>
        <flux:text class="mb-2">
            <strong>Status:</strong>
            <flux:badge
                color="{{ $transaction->status === 'paid' ? 'green' : ($transaction->status === 'failed' ? 'red' : 'zinc') }}"
                size="sm"
                inset="top bottom"
            >
                {{ ucfirst($transaction->status ?? 'pending') }}
            </flux:badge>
        </flux:text>
        <flux:text class="mb-2">
            <strong>Created at:</strong>
            {{ $transaction->created_at?->format('M d, Y H:i') ?? '-' }}
        </flux:text>
    </flux:card>

    {{-- Review form for customer reviewing provider --}}
    @if ($transaction->status === 'completed' && $transaction->user_id === auth()->id())
        <flux:card class="mt-6">
            @if (session('success'))
                <div class="mb-2 text-green-600">{{ session('success') }}</div>
            @endif

            @if ($review_submitted)
                <div class="mb-2">Review submitted</div>
                <div class="mb-2">{{ $review_comment }}</div>
            @else
                <form wire:submit="submitReview">
                    <flux:heading size="sm" class="mb-2">Review the provider</flux:heading>
                    <div class="mb-2">
                        <label for="review_rating">Rating</label>
                        <select wire:model.defer="review_rating" id="review_rating" class="block w-full">
                            <option value="">Select rating</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('review_rating')
                            <div class="mb-2 text-xs text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="review_comment">Comment</label>
                        <flux:textarea
                            wire:model.defer="review_comment"
                            id="review_comment"
                            placeholder="Write your review..."
                            rows="3"
                        />
                        @error('review_comment')
                            <div class="mb-2 text-xs text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <flux:button type="submit" color="primary">Submit Review</flux:button>
                </form>
            @endif
        </flux:card>
    @endif

    <flux:card class="mt-8">
        <flux:heading size="md" class="mb-2">Activity Log</flux:heading>
        @if ($transaction->activities->isEmpty())
            <flux:text>No activity recorded for this transaction.</flux:text>
        @else
            <div class="space-y-4">
                @foreach ($transaction->activities as $activity)
                    @if ($activity->type === 'review')
                        @php
                            $review = $reviews[$activity->user_id] ?? null;
                        @endphp

                        @if ($review)
                            <div class="flex items-center gap-2 rounded bg-yellow-50 p-2 text-xs dark:bg-yellow-900">
                                <span class="font-semibold">[Review]</span>
                                <span>
                                    <strong>
                                        @if ($review->reviewer_id === $transaction->listing->user_id)
                                            Provider
                                        @elseif ($review->reviewer_id === $transaction->user_id)
                                            Buyer
                                        @else
                                            User #{{ $review->reviewer_id }}
                                        @endif
                                    </strong>
                                    reviewed
                                    <strong>
                                        @if ($review->reviewee_id === $transaction->listing->user_id)
                                            Provider
                                        @elseif ($review->reviewee_id === $transaction->user_id)
                                            Buyer
                                        @else
                                            User #{{ $review->reviewee_id }}
                                        @endif
                                    </strong>
                                </span>
                                <span class="ml-2">Rating: {{ $review->rating }}★</span>
                                <span class="ml-2">"{{ $review->comment }}"</span>
                                <span class="ml-auto text-zinc-400">
                                    {{ $review->created_at?->format('M d, Y H:i') ?? '-' }}
                                </span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 rounded bg-red-50 p-2 text-xs">
                                <span class="font-semibold">[Review]</span>
                                <span>Review data missing.</span>
                            </div>
                        @endif
                    @elseif ($activity->type === 'message')
                        <div class="flex items-start gap-3 rounded bg-zinc-50 p-3 dark:bg-zinc-900">
                            <div class="text-sm font-bold">
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
                                <div class="mt-1 text-xs text-zinc-500">
                                    {{ $activity->created_at?->format('M d, Y H:i') ?? '-' }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2 rounded bg-zinc-100 p-2 text-xs dark:bg-zinc-800">
                            <span class="font-semibold">[{{ ucfirst($activity->type) }}]</span>
                            <span>{{ $activity->description }}</span>
                            @if ($activity->user_id === $transaction->listing->user_id)
                                <span class="ml-2 text-zinc-500">(Provider)</span>
                            @elseif ($activity->user_id === $transaction->user_id)
                                <span class="ml-2 text-zinc-500">(Buyer)</span>
                            @endif
                            @if ($activity->meta)
                                <span class="ml-2">
                                    <pre class="inline">
{{ json_encode($activity->meta, JSON_UNESCAPED_SLASHES) }}</pre
                                    >
                                </span>
                            @endif

                            <span class="ml-auto text-zinc-400">
                                {{ $activity->created_at?->format('M d, Y H:i') ?? '-' }}
                            </span>
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
            @error('message')
                <div class="mb-2 text-xs text-red-500">{{ $message }}</div>
            @enderror

            <flux:button type="submit" color="primary">Send</flux:button>
        </form>
    </flux:card>
</div>
