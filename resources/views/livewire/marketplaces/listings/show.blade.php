<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Livewire\Volt\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public Listing $listing;

    public array $range = [
        'start' => null,
        'end' => null,
    ];

    public ?array $bookingBreakdown = null;

    public function updated($property)
    {
        if (in_array($property, ['range.start', 'range.end'])) {
            $this->calculateBookingBreakdown();
        }
    }

    protected function calculateBookingBreakdown(): void
    {
        $this->bookingBreakdown = null;
        if (! $this->range['start'] || ! $this->range['end']) {
            return;
        }
        $start = \Carbon\Carbon::parse($this->range['start']);
        $end = \Carbon\Carbon::parse($this->range['end']);
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

        if (! \Illuminate\Support\Facades\Auth::check()) {
            $this->bookingError = 'You must be logged in to book.';

            return;
        }
        if (! $this->range['start'] || ! $this->range['end']) {
            $this->bookingError = 'Please select both start and end dates.';

            return;
        }
        $start = \Carbon\Carbon::parse($this->range['start']);
        $end = \Carbon\Carbon::parse($this->range['end']);
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
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($q2) use ($start, $end) {
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
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'start_date' => $start,
            'end_date' => $end,
            'nights' => $nights,
            'price_per_night' => $pricePerNight,
            'total' => $total,
            'status' => 'pending',
        ]);
        $transaction->activities()->create([
            'type' => 'created',
            'description' => 'Transaction created by user',
            'meta' => [
'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'ip' => request()->ip(),
            ],
        ]);
        $marketplace = $this->listing->marketplace;

        return $this->redirectRoute('marketplaces.transactions.pay', [
            'marketplace' => $marketplace->id,
            'transaction' => $transaction->id,
        ]);
    }
}; ?>
<flux:container class="[:where(&)]:max-w-5xl!">
    <flux:main>
        @if (is_array($listing->photos) && count($listing->photos) > 0)
            <img src="/{{ $listing->photos[0] }}" class="w-full rounded object-fill aspect-3/2 shadow" />

            <flux:spacer class="my-6" />

            <div class="grid grid-cols-6 gap-4">
                @foreach ($listing->photos as $photo)
                    <img src="/{{ $photo }}" class="w-full rounded object-fill aspect-3/2 shadow" />
                @endforeach
            </div>
        @endif

        <flux:spacer class="my-10" />

        <flux:text variant="strong" class="text-base">{{ $listing->description }}</flux:text>

        <flux:spacer class="my-10" />

        <flux:text class="text-base">
            {{ $listing->address }}{{ $listing->apt_suite ? ', '.$listing->apt_suite : '' }}
        </flux:text>
    </flux:main>

    <flux:aside class="py-12 lg:pl-20 w-96 hidden lg:block" sticky>
        <flux:heading level="1" size="lg" class="mb-2">{{ $listing->title }}</flux:heading>

        <flux:spacer class="my-2" />

        <flux:heading size="xl" level="2">
            ${{ number_format($listing->price, 2) }} per day
        </flux:heading>

        <flux:spacer class="my-6" />

        <div class="flex items-center gap-2">
            <flux:avatar :src="'https://unavatar.io/'. $listing->user->email" />
            <flux:heading>{{ $listing->user->name }}</flux:heading>
        </div>

        <flux:spacer class="my-12" />

        <form wire:submit="requestToBook">
            <div class="flex flex-col gap-4 md:flex-row">
                <flux:date-picker mode="range" wire:model.live="range">
                    <x-slot name="trigger">
                        <div class="flex flex-col sm:flex-row gap-6 sm:gap-4">
                            <flux:date-picker.input label="Start" />
                            <flux:date-picker.input label="End" />
                        </div>
                    </x-slot>
                </flux:date-picker>
            </div>
            @if ($bookingBreakdown)
                <flux:card class="mt-4 p-0">
                    <div class="px-6 pt-6 pb-2">
                        <flux:text variant="strong" class="uppercase text-xs tracking-wide mb-2">Booking Breakdown</flux:text>
                    </div>
                    <hr class="border-t" />
                    <div class="grid grid-cols-2 gap-6 px-6 py-4">
                        <div>
                            <flux:text class="text-xs mb-1">Booking start</flux:text>
                            <flux:text variant="strong" class="text-lg">{{ \Carbon\Carbon::parse($range['start'])->format('l') }}</flux:text>
                            <flux:text class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($range['start'])->format('M d') }}</flux:text>
                        </div>
                        <div class="text-right">
                            <flux:text class="text-xs mb-1">Booking end</flux:text>
                            <flux:text variant="strong" class="text-lg">{{ \Carbon\Carbon::parse($range['end'])->format('l') }}</flux:text>
                            <flux:text class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($range['end'])->format('M d') }}</flux:text>
                        </div>
                    </div>
                    <hr class="border-t" />
                    <div class="grid grid-cols-2 gap-6 px-6 py-4 items-center">
                        <div>
                            <flux:text class="text-base">${{ number_format($bookingBreakdown['price_per_night'], 2) }} x {{ $bookingBreakdown['nights'] }} days</flux:text>
                        </div>
                        <div class="text-right">
                            <flux:text class="text-base">${{ number_format($bookingBreakdown['total'], 2) }}</flux:text>
                        </div>
                    </div>
                    <hr class="border-t" />
                    <div class="grid grid-cols-2 gap-6 px-6 py-4 items-center">
                        <div>
                            <flux:text class="text-base">Total price</flux:text>
                        </div>
                        <div class="text-right">
                            <flux:text variant="strong" class="text-lg">${{ number_format($bookingBreakdown['total'], 2) }}</flux:text>
                        </div>
                    </div>
                </flux:card>
                <flux:button type="submit" variant="primary" class="mt-4 w-full">Request to Book</flux:button>
            @endif
         @if ($bookingMessage)
                <flux:text class="mt-4 text-green-600">{{ $bookingMessage }}</flux:text>
            @endif
         @if ($bookingError)
                <flux:text class="mt-4 text-red-600">{{ $bookingError }}</flux:text>
            @endif
        </form>
    </flux:aside>
</flux:container>
