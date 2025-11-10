<?php

use Livewire\Volt\Component;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public Review $review;
    public int $rating;
    public string $comment;

    public function mount(Review $review)
    {
        $this->review = $review;
        $this->rating = $review->rating ?? 1;
        $this->comment = $review->comment ?? '';

        $this->authorize('update', $review);
    }

    public function updateReview()
    {
        $this->authorize('update', $this->review);

        $validated = validator([
            'rating' => $this->rating,
            'comment' => $this->comment,
        ], [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5'],
        ])->validate();

        $this->review->update($validated);
    }
}; ?>

<div>
    <form wire:submit="updateReview">
        <div>
            <label for="rating">Rating</label>
            <input id="rating" type="number" min="1" max="5" wire:model="rating" />
            @error('rating') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="comment">Comment</label>
            <textarea id="comment" wire:model="comment"></textarea>
            @error('comment') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <button type="submit">Update Review</button>
    </form>
</div>
