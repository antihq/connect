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
                <form wire:submit.prevent>
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
                        <flux:button type="button" color="primary" class="mt-4 w-full">Request to Book</flux:button>
                    @endif
                </form>
            </flux:card>
        </div>
    </flux:card>
</div>
