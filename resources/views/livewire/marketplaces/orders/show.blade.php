<?php

use App\Models\Marketplace;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
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
        $user = Auth::user();
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

<flux:container class="[:where(&)]:max-w-5xl!">
    <flux:main>
        <flux:heading size="xl" as="h1">
            @if ($transaction->status === 'completed')
                Order Completed
            @elseif ($transaction->status === 'paid')
                Order Paid
            @elseif ($transaction->status === 'failed')
                Order Failed
            @else
                Order Pending
            @endif
        </flux:heading>

        <flux:spacer class="my-10" />

        <flux:heading size="md">Activity Log</flux:heading>

        <flux:spacer class="my-10" />

        @if ($transaction->activities->isEmpty())
            <flux:text>No activity recorded for this transaction.</flux:text>
        @else
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach ($transaction->activities as $activity)
                        <li>
                            <div class="relative pb-8">
                                @if (!$loop->last)
                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-zinc-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex items-start space-x-3">
                                    @if ($activity->type === 'message')
                                        <div class="relative">
                                            <img class="flex size-10 items-center justify-center rounded-full bg-zinc-400 ring-8 ring-white outline -outline-offset-1 outline-white/10" src="https://unavatar.io/{{ $activity->user->email ?? 'unknown' }}" alt="" />
                                            <span class="absolute -right-1 -bottom-0.5 rounded-tl bg-white px-0.5 py-px">
                                                <flux:icon.chat-bubble-left-ellipsis class="size-5 text-zinc-500" />
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <flux:text variant="strong" class="font-medium">
                                                    {{ $activity->user->name ?? 'User #'.$activity->user_id }}
                                                </flux:text>
                                                <flux:text class="mt-0.5">Messaged {{ $activity->created_at?->diffForHumans(['parts' => 1, 'short' => true]) ?? '-' }}</flux:text>
                                            </div>
                                            <div class="mt-2">
                                                <flux:text variant="strong">{{ $activity->description }}</flux:text>
                                            </div>
                                        </div>
                                    @elseif ($activity->type === 'review')
                                        @php $review = $reviews[$activity->user_id] ?? null; @endphp
                                        <div>
                                            <div class="relative px-1">
                                                <div class="flex size-8 items-center justify-center rounded-full bg-yellow-800 ring-8 ring-zinc-900">
                                                    <!-- Star icon -->
                                                    <svg class="size-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 0 0 .95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.388 2.46a1 1 0 0 0-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.388-2.46a1 1 0 0 0-1.175 0l-3.388 2.46c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 0 0-.364-1.118l-3.388-2.46c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 0 0 .95-.69l1.286-3.967z"/></svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1 py-1.5">
                                            <div class="text-sm text-zinc-400">
                                                <span class="font-medium text-white">{{ $activity->user->name ?? 'User #'.$activity->user_id }}</span>
                                                reviewed
                                                <span class="font-medium text-white">
                                                    @if ($review)
                                                        {{ $review->reviewee_id === $transaction->listing->user_id ? 'Provider' : ($review->reviewee_id === $transaction->user_id ? 'Buyer' : 'User #'.$review->reviewee_id) }}
                                                    @else
                                                        Unknown
                                                    @endif
                                                </span>
                                                <span class="whitespace-nowrap">{{ $activity->created_at?->diffForHumans(['parts' => 1, 'short' => true]) ?? '-' }}</span>
                                            </div>
                                            @if ($review)
                                                <div class="mt-2 text-sm text-zinc-200">
                                                    <p>Rating: {{ $review->rating }}★<br>{{ $review->comment }}</p>
                                                </div>
                                            @else
                                                <div class="mt-2 text-sm text-red-400">Review data missing.</div>
                                            @endif
                                        </div>
                                    @else
                                        <div>
                                            <div class="relative px-1">
                                                <div class="flex size-8 items-center justify-center rounded-full bg-zinc-100 ring-8 ring-white">
                                                    <flux:icon.exclamation-triangle class="size-5 text-zinc-500" variant="mini" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1 py-0">
                                            <flux:text class="text-sm/8">
                                                <flux:text variant="strong" class="font-medium" inline>
                                                    {{ $activity->user->name ?? 'User #'.$activity->user_id }}
                                                </flux:text>
                                                <span class="mr-0.5">did {{ ucfirst($activity->type) }}</span>
                                                <span class="whitespace-nowrap">{{ $activity->created_at?->diffForHumans(['parts' => 1, 'short' => true]) ?? '-' }}</span>
                                            </flux:text>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

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

        <flux:spacer class="my-10" />

        <form wire:submit="postMessage" class="space-y-6">
            <flux:textarea wire:model="message" label="Send a message" placeholder="Type your message..." rows="3" />
            <flux:button type="submit" color="primary">Send</flux:button>
        </form>
    </flux:main>

    <flux:aside class="hidden w-96 py-12 lg:block lg:pl-20" sticky>
        @if (is_array($transaction->listing->photos) && count($transaction->listing->photos) > 0)
            <img
                src="/{{ $transaction->listing->photos[0] }}"
                class="mb-6 aspect-3/2 w-full rounded object-fill shadow"
            />
        @endif

        <div class="flex items-center gap-2">
            <flux:avatar :src="'https://unavatar.io/'. $transaction->listing->user->email" />
            <flux:heading>{{ $transaction->listing->user->name ?? 'N/A' }}</flux:heading>
        </div>

        <flux:spacer class="my-6" />

        <flux:heading class="text-xl" :accent="false">
            <flux:link
                :href="route('on-marketplace.listings.show', [$transaction->listing->marketplace_id, $transaction->listing_id])"
                :accent="false"
            >
                {{ $transaction->listing->title }}
            </flux:link>
        </flux:heading>

        <flux:spacer class="my-6" />

        <flux:card class="my-4 p-0">
            <div class="px-4 py-4">
                <flux:heading class="text-xs tracking-wide uppercase">Booking Breakdown</flux:heading>
            </div>

            <flux:separator variant="subtle" class="-mt-px" />

            <div class="grid grid-cols-2 gap-6 px-4 py-4">
                <div>
                    <flux:text class="text-xs">Booking start</flux:text>
                    <flux:text variant="strong" class="text-base font-medium">
                        {{ $transaction->start_date?->format('l') ?? '-' }}
                    </flux:text>
                    <flux:text variant="strong" class="text-sm">
                        {{ $transaction->start_date?->format('M d') ?? '-' }}
                    </flux:text>
                </div>
                <div class="text-right">
                    <flux:text class="text-xs">Booking end</flux:text>
                    <flux:text variant="strong" class="text-base font-medium">
                        {{ $transaction->end_date?->format('l') ?? '-' }}
                    </flux:text>
                    <flux:text variant="strong" class="text-sm">
                        {{ $transaction->end_date?->format('M d') ?? '-' }}
                    </flux:text>
                </div>
            </div>

            <flux:separator variant="subtle" />

            <div class="grid grid-cols-2 items-center gap-6 px-4 py-4">
                <div>
                    <flux:text variant="strong">
                        ${{ number_format($transaction->price_per_night, 2) }} x {{ $transaction->nights ?? '-' }}
                        nights
                    </flux:text>
                </div>
                <div class="text-right">
                    <flux:text variant="strong">${{ number_format($transaction->total, 2) }}</flux:text>
                </div>
            </div>

            <flux:separator variant="subtle" />

            <div class="grid grid-cols-2 items-center gap-6 px-4 py-4">
                <div>
                    <flux:text>Total price</flux:text>
                </div>
                <div class="text-right">
                    <flux:text variant="strong" class="text-base font-medium">
                        ${{ number_format($transaction->total, 2) }}
                    </flux:text>
                </div>
            </div>
        </flux:card>
    </flux:aside>
</flux:container>
