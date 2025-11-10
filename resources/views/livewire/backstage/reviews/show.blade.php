<?php

use Livewire\Volt\Component;

use App\Models\Review;

new class extends Component {
    public Review $review;

    public function mount()
    {
        $owner = $this->review->transaction?->marketplace?->organization?->user;
        if (!$owner || !\Illuminate\Support\Facades\Auth::check() || \Illuminate\Support\Facades\Auth::id() !== $owner->id) {
            abort(403);
        }
    }
}; ?>

<div>
    <h2>Review Details</h2>
    <p><strong>Rating:</strong> {{ $review->rating }}</p>
    <p><strong>Comment:</strong> {{ $review->comment }}</p>
    <p><strong>Reviewed At:</strong> {{ $review->created_at->format('Y-m-d H:i') }}</p>

    <h3>Reviewer</h3>
    <p>{{ $review->user?->name ?? 'N/A' }} ({{ $review->user?->email ?? 'N/A' }})</p>

    <h3>Marketplace</h3>
    <p>{{ $review->transaction?->marketplace?->name ?? 'N/A' }}</p>

    <h3>Marketplace Owner</h3>
    @php
        $owner = $review->transaction?->marketplace?->organization?->user;
    @endphp
    <p>{{ $owner?->name ?? 'N/A' }}{{ $owner ? ' (' . $owner->email . ')' : '' }}</p>

    <a href="{{ route('backstage.reviews.edit', $review) }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit Review</a>
</div>
