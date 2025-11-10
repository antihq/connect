<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;
use App\Models\Transaction;
use App\Models\Review;
use App\Models\TransactionActivity;

new class extends Component {
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

    public function acceptRequest()
    {
        $user = auth()->user();
        if ($user->id !== $this->transaction->listing->user_id || $this->transaction->status !== 'paid') {
            abort(403);
        }
        $this->transaction->status = 'accepted';
        $this->transaction->save();
        $this->transaction->activities()->create([
            'type' => 'status_change',
            'description' => 'Provider accepted the request.',
            'user_id' => $user->id,
        ]);
        $this->transaction->load(['activities.user']);
    }

    public function rejectRequest()
    {
        $user = auth()->user();
        if ($user->id !== $this->transaction->listing->user_id || $this->transaction->status !== 'paid') {
            abort(403);
        }
        $this->transaction->status = 'rejected';
        $this->transaction->save();
        $this->transaction->activities()->create([
            'type' => 'status_change',
            'description' => 'Provider rejected the request.',
            'user_id' => $user->id,
        ]);
        $this->transaction->load(['activities.user']);
    }

    public function markAsComplete()
    {
        $user = auth()->user();
        if ($user->id !== $this->transaction->listing->user_id || $this->transaction->status !== 'accepted') {
            abort(403);
        }
        $this->transaction->status = 'completed';
        $this->transaction->save();
        $this->transaction->activities()->create([
            'type' => 'status_change',
            'description' => 'Provider marked the transaction as completed.',
            'user_id' => $user->id,
        ]);
        $this->transaction->load(['activities.user']);
    }

    public function submitReview()
    {
        $user = auth()->user();
        $providerId = $this->transaction->listing->user_id;
        $customerId = $this->transaction->user_id;
        // Only provider can review customer in sales
        if ($user->id !== $providerId) {
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
            'reviewer_id' => $providerId,
            'reviewee_id' => $customerId,
            'rating' => $this->review_rating,
            'comment' => $this->review_comment,
        ]);
        // Log activity
        $this->transaction->activities()->create([
            'type' => 'review',
            'description' => 'Provider reviewed the customer: ' . $this->review_comment,
            'user_id' => $providerId,
        ]);
        $this->review_submitted = true;
        // Ensure review_comment is set for display after submission
        $this->review_comment = $this->review_comment;
        $this->transaction->load(['activities.user']);
        session()->flash('success', 'Review submitted');
    }

    private function hasReviewed(): bool
    {
        $providerId = $this->transaction->listing->user_id;
        return TransactionActivity::where('transaction_id', $this->transaction->id)
            ->where('type', 'review')
            ->where('user_id', $providerId)
            ->exists();
    }
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])
    @include('partials.marketplace-inbox-navbar', ['marketplace' => $marketplace])

    <flux:card>
        <flux:heading size="lg" class="mb-4">Sale Details</flux:heading>
        <flux:text class="mb-2">
            <strong>Listing:</strong>
            <flux:link :href="route('marketplaces.listings.show', [$transaction->listing->marketplace_id, $transaction->listing_id])">
                {{ $transaction->listing->title ?? 'N/A' }}
            </flux:link>
        </flux:text>
         <flux:text class="mb-2">
             <strong>Buyer:</strong> {{ $transaction->user?->name ?? 'N/A' }} ({{ $transaction->user?->email ?? '' }})
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

    @if (
        $transaction->status === 'paid' &&
        $transaction->listing->user_id === auth()->id()
    )
        <div class="flex gap-2 mb-4">
            <form wire:submit="acceptRequest">
                <flux:button type="submit" color="success">Accept</flux:button>
            </form>
            <form wire:submit="rejectRequest">
                <flux:button type="submit" color="danger">Reject</flux:button>
            </form>
        </div>
    @endif

    @if (
        $transaction->status === 'accepted' &&
        $transaction->listing->user_id === auth()->id()
    )
        <div class="flex gap-2 mb-4">
            <form wire:submit="markAsComplete">
                <flux:button type="submit" color="primary">Mark as Complete</flux:button>
            </form>
        </div>
    @endif

    {{-- Review form for provider reviewing customer --}}
    @if ($transaction->status === 'completed' && $transaction->listing->user_id === auth()->id())
        <flux:card class="mt-6">
            @if (session('success'))
                <div class="text-green-600 mb-2">{{ session('success') }}</div>
            @endif
            @if ($review_submitted)
                <div class="mb-2">Review submitted</div>
                <div class="mb-2">{{ $review_comment }}</div>
            @else
                <form wire:submit="submitReview">
                    <flux:heading size="sm" class="mb-2">Review the customer</flux:heading>
                    <div class="mb-2">
                        <label for="review_rating">Rating</label>
                        <select wire:model.defer="review_rating" id="review_rating" class="block w-full">
                            <option value="">Select rating</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('review_rating') <div class="text-red-500 text-xs mb-2">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-2">
                        <label for="review_comment">Comment</label>
                        <flux:textarea wire:model.defer="review_comment" id="review_comment" placeholder="Write your review..." rows="3" />
                        @error('review_comment') <div class="text-red-500 text-xs mb-2">{{ $message }}</div> @enderror
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
            <div class="flex items-center gap-2 p-2 rounded bg-yellow-50 dark:bg-yellow-900 text-xs">
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
                <span class="ml-auto text-zinc-400">{{ $review->created_at?->format('M d, Y H:i') ?? '-' }}</span>
            </div>
        @else
            <div class="flex items-center gap-2 p-2 rounded bg-red-50 text-xs">
                <span class="font-semibold">[Review]</span>
                <span>Review data missing.</span>
            </div>
        @endif
    @elseif ($activity->type === 'message')
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
            @if ($activity->user_id === $transaction->listing->user_id)
                <span class="ml-2 text-zinc-500">(Provider)</span>
            @elseif ($activity->user_id === $transaction->user_id)
                <span class="ml-2 text-zinc-500">(Buyer)</span>
            @endif
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
