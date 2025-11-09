<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;
use App\Models\Listing;

new class extends Component {
    public Marketplace $marketplace;
    public Listing $listing;
    public string $timezone = '';
    public array $weekly_schedule = [];

    public array $availability_exceptions = [];
    public array $new_exception = [
        'available' => true,
        'start_date' => '',
        'end_date' => '',
    ];

    public function mount()
    {
        $this->timezone = $this->listing->timezone ?? config('app.timezone', 'UTC');
        $this->weekly_schedule = $this->listing->weekly_schedule ?? [
            'monday' => false,
            'tuesday' => false,
            'wednesday' => false,
            'thursday' => false,
            'friday' => false,
            'saturday' => false,
            'sunday' => false,
        ];
        $this->availability_exceptions = $this->listing->availability_exceptions ?? [];
    }

    public function addException()
    {
        $this->validate([
            'new_exception.available' => ['required', 'boolean'],
            'new_exception.start_date' => ['required', 'date'],
            'new_exception.end_date' => ['required', 'date', 'after_or_equal:new_exception.start_date'],
        ]);
        $this->availability_exceptions[] = $this->new_exception;
        $this->new_exception = [
            'available' => true,
            'start_date' => '',
            'end_date' => '',
        ];
    }

    public function removeException($index)
    {
        unset($this->availability_exceptions[$index]);
        $this->availability_exceptions = array_values($this->availability_exceptions);
    }

    public function rules(): array
    {
        return [
            'timezone' => ['required', 'string'],
            'weekly_schedule' => ['required', 'array'],
            'availability_exceptions' => ['array'],
            'availability_exceptions.*.available' => ['required', 'boolean'],
            'availability_exceptions.*.start_date' => ['required', 'date'],
            'availability_exceptions.*.end_date' => ['required', 'date', 'after_or_equal:availability_exceptions.*.start_date'],
        ];
    }

    public function update()
    {
        $this->validate();
        $this->listing->update([
            'timezone' => $this->timezone,
            'weekly_schedule' => $this->weekly_schedule,
            'availability_exceptions' => $this->availability_exceptions,
        ]);
        // Optionally, emit event or redirect
    }

    public function getTimezonesProperty(): array
    {
        return \DateTimeZone::listIdentifiers();
    }
}; ?>

<div>
    <flux:navbar>
        <flux:navbar.item :href="route('marketplaces.show', $marketplace)">Home</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.create', $marketplace)">Post a new listings</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.inbox.orders', $marketplace)">Inbox</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.account.listings', $marketplace)">Profile</flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <flux:navbar class="mb-6">
        <flux:navbar.item :href="route('marketplaces.listings.edit.details', [$marketplace, $listing])">
            Details
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.location', [$marketplace, $listing])">
            Location
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.pricing', [$marketplace, $listing])">
            Pricing
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.availability', [$marketplace, $listing])" active>
            Availability
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.photos', [$marketplace, $listing])">
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <form class="space-y-6" wire:submit="update">
        <flux:select label="Time zone" wire:model="timezone">
            <option value="">Select a time zone</option>
            @foreach ($this->timezones as $tz)
                <option value="{{ $tz }}">{{ $tz }}</option>
            @endforeach
        </flux:select>
        <flux:text>Weekly default schedule:</flux:text>
        <div class="grid grid-cols-2 gap-2">
            @foreach (['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                <label>
                    <input type="checkbox" wire:model="weekly_schedule.{{ $day }}">
                    {{ ucfirst($day) }}
                </label>
            @endforeach
        </div>
        <flux:text class="mt-8">Availability exceptions:</flux:text>
        <div class="space-y-2">
            @foreach ($availability_exceptions as $i => $exception)
                <div class="flex items-center gap-2">
                    <span>{{ $exception['available'] ? 'Available' : 'Not available' }}</span>
                    <span>{{ $exception['start_date'] }} → {{ $exception['end_date'] }}</span>
                    <button type="button" wire:click="removeException({{ $i }})">Remove</button>
                </div>
            @endforeach
        </div>
        <div class="flex items-center gap-2 mt-4">
            <flux:select wire:model="new_exception.available" label="Available?">
                <option :value="true">Available</option>
                <option :value="false">Not available</option>
            </flux:select>
            <flux:input type="date" wire:model="new_exception.start_date" label="Start date" />
            <flux:input type="date" wire:model="new_exception.end_date" label="End date" />
            <flux:button type="button" wire:click="addException">Add exception</flux:button>
        </div>
        <flux:button type="submit">save</flux:button>
    </form>
</div>
