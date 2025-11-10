<?php

use Livewire\Volt\Component;
use App\Models\Review;

new class extends Component {
    public function getReviewsProperty()
    {
        $user = auth()->user();
        if (!$user) {
            return \App\Models\Review::query()->whereRaw('1=0')->paginate(20); // empty paginator for guests
        }
        $organization = $user->currentOrganization()->first();
        $marketplace = $organization?->marketplace()->first();

        if (!$marketplace) {
            abort(404);
        }

        return Review::with(['transaction', 'reviewer', 'reviewee'])
            ->whereHas('transaction', function ($q) use ($marketplace) {
                $q->where('marketplace_id', $marketplace->id);
            })
            ->latest()
            ->paginate(20);
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold mb-4">Marketplace Reviews</h1>
    <table class="min-w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
        <thead>
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Reviewer</th>
                <th class="px-4 py-2">Reviewee</th>
                <th class="px-4 py-2">Transaction</th>
                <th class="px-4 py-2">Rating</th>
                <th class="px-4 py-2">Comment</th>
                <th class="px-4 py-2">Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->reviews as $review)
                <tr>
                    <td class="border px-4 py-2">{{ $review->id }}</td>
                    <td class="border px-4 py-2">{{ $review->reviewer->name ?? 'N/A' }}</td>
                    <td class="border px-4 py-2">{{ $review->reviewee->name ?? 'N/A' }}</td>
                    <td class="border px-4 py-2">
    @if (!$review->transaction)
        N/A
    @else
        {{ $review->transaction->id }}
    @endif
</td>
                    <td class="border px-4 py-2">{{ $review->rating }}</td>
                    <td class="border px-4 py-2">{{ $review->comment }}</td>
                    <td class="border px-4 py-2">{{ $review->created_at->diffForHumans() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">
        {{ $this->reviews->links() }}
    </div>
</div>
