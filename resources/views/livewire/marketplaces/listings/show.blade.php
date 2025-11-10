
<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Livewire\Volt\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public Listing $listing;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public ?array $bookingBreakdown = null;

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate'])) {
            $this->calculateBookingBreakdown();
        }
    }

    protected function calculateBookingBreakdown(): void
    {
        $this->bookingBreakdown = null;
        if (! $this->startDate || ! $this->endDate) {
            return;
        }
        $start = \Carbon\Carbon::parse($this->startDate);
        $end = \Carbon\Carbon::parse($this->endDate);
        if ($end->lessThanOrEqualTo($start)) {
            return;
        }
        $nights = $start->diffInDays($end);
        $pricePerNight = $this->listing->price ?? 0;
        $total = $nights * $pricePerNight;
        $this->bookingBreakdown = [
            'nights' => $nights,
            'price_per_night' => $pricePerNight,
            'total' => $total,
        ];
    }

    public function mount()
    {
        if ($this->listing->marketplace_id !== $this->marketplace->id) {
            abort(404);
        }
    }

    public ?string $bookingMessage = null;
    public ?string $bookingError = null;

    public function requestToBook()
    {
        $this->bookingMessage = null;
        $this->bookingError = null;

        if (!auth()->check()) {
            $this->bookingError = 'You must be logged in to book.';
            return;
        }
        if (!$this->startDate || !$this->endDate) {
            $this->bookingError = 'Please select both start and end dates.';
            return;
        }
        $start = \Carbon\Carbon::parse($this->startDate);
        $end = \Carbon\Carbon::parse($this->endDate);
        if ($end->lessThanOrEqualTo($start)) {
            $this->bookingError = 'End date must be after start date.';
            return;
        }
        $nights = $start->diffInDays($end);
        if ($nights < 1) {
            $this->bookingError = 'Booking must be at least one night.';
            return;
        }
        // Check for overlapping transactions
        $overlap = $this->listing->transactions()
            ->where(function($q) use ($start, $end) {
                $q->where(function($q2) use ($start, $end) {
                    $q2->where('start_date', '<', $end)
                        ->where('end_date', '>', $start);
                });
            })
            ->exists();
        if ($overlap) {
            $this->bookingError = 'Selected dates are not available.';
            return;
        }
        $pricePerNight = $this->listing->price ?? 0;
        $total = $nights * $pricePerNight;
        $transaction = $this->listing->transactions()->create([
            'marketplace_id' => $this->marketplace->id,
            'user_id' => auth()->id(),
            'start_date' => $start,
            'end_date' => $end,
            'nights' => $nights,
            'price_per_night' => $pricePerNight,
            'total' => $total,
            'status' => 'pending',
        ]);
        $marketplace = $this->listing->marketplace;
        return $this->redirectRoute('marketplaces.transactions.pay', [
            'marketplace' => $marketplace->id,
            'transaction' => $transaction->id,
        ]);
    }
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])

    <flux:card>
        <flux:heading size="lg" class="mb-2">{{ $listing->title }}</flux:heading>
        <flux:text class="mb-4">{{ $listing->description }}</flux:text>
        @if ($listing->price)
            <flux:text class="mb-2">
                <strong>Price:</strong>
                ${{ number_format($listing->price, 2) }}
            </flux:text>
        @endif

        @if ($listing->address)
            <flux:text class="mb-2">
                <strong>Address:</strong>
                {{ $listing->address }}{{ $listing->apt_suite ? ', '.$listing->apt_suite : '' }}
            </flux:text>
        @endif

        <flux:text class="mb-2">Posted {{ $listing->created_at->diffForHumans() }}</flux:text>
        @if (is_array($listing->photos) && count($listing->photos) > 0)
            <div class="mt-4 grid grid-cols-2 gap-4">
                @foreach ($listing->photos as $photo)
                    <img src="/{{ $photo }}" class="h-40 w-full rounded object-cover shadow" />
                @endforeach
            </div>
        @endif

        <div class="mt-8">
            <flux:card>
                <form wire:submit="requestToBook">
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div class="flex-1">
                            <flux:input type="date" label="Start Date" wire:model.live="startDate" />
                        </div>
                        <div class="flex-1">
                            <flux:input type="date" label="End Date" wire:model.live="endDate" />
                        </div>
                    </div>
                    @if ($bookingBreakdown)
                        <flux:card class="mt-4">
                            <flux:text>
                                <strong>Nights:</strong>
                                {{ $bookingBreakdown['nights'] }}
                            </flux:text>
                            <flux:text>
                                <strong>Price per night:</strong>
                                ${{ number_format($bookingBreakdown['price_per_night'], 2) }}
                            </flux:text>
                            <flux:text>
                                <strong>Total:</strong>
                                ${{ number_format($bookingBreakdown['total'], 2) }}
                            </flux:text>
                        </flux:card>
                        <flux:button type="submit" color="primary" class="mt-4 w-full">Request to Book</flux:button>
                    @endif
                    @if ($bookingMessage)
                        <flux:text class="mt-4 text-green-600">{{ $bookingMessage }}</flux:text>
                    @endif
                    @if ($bookingError)
                        <flux:text class="mt-4 text-red-600">{{ $bookingError }}</flux:text>
                    @endif
                </form>
            </flux:card>
        </div>
    </flux:card>
</div>
