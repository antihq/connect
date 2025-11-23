<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
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

        $this->weekly_schedule = $this->listing->weeklyScheduleEntries
            ->where('available', true)
            ->pluck('day')
            ->toArray();

        $this->availability_exceptions = $this->listing->availabilityExceptions->toArray() ?? [];
    }

    #[Computed]
    public function timezones(): array
    {
        return DateTimeZone::listIdentifiers();
    }

    public function rules(): array
    {
        return [
            'timezone' => ['required', 'string'],
            'weekly_schedule' => ['required', 'array'],
            'weekly_schedule.*' => ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'availability_exceptions' => ['array'],
            'availability_exceptions.*.available' => ['required', 'boolean'],
            'availability_exceptions.*.start_date' => ['required', 'date'],
            'availability_exceptions.*.end_date' => ['required', 'date', 'after_or_equal:availability_exceptions.*.start_date'],
        ];
    }

    public function addException()
    {
        $this->new_exception['available'] = (bool) $this->new_exception['available'];

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

    public function hideAddExceptionForm() {}

    public function removeException($index)
    {
        unset($this->availability_exceptions[$index]);

        $this->availability_exceptions = array_values($this->availability_exceptions);
    }

    public function update()
    {
        $this->validate();

        $this->listing->update([
            'timezone' => $this->timezone,
        ]);

        $this->listing->syncWeeklySchedule($this->weekly_schedule);

        $this->listing->syncAvailabilityExceptions($this->availability_exceptions);

        return $this->redirectRoute('on-marketplace.listings.edit.photos', [
            'marketplace' => $this->marketplace,
            'listing' => $this->listing,
        ], navigate: true);
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:navbar class="-mb-px">
        <flux:navbar.item :href="route('on-marketplace.listings.edit.details', [$marketplace, $listing])" wire:navigate>
            Details
        </flux:navbar.item>
        <flux:navbar.item
            :href="route('on-marketplace.listings.edit.location', [$marketplace, $listing])"
            wire:navigate
        >
            Location
        </flux:navbar.item>
        <flux:navbar.item :href="route('on-marketplace.listings.edit.pricing', [$marketplace, $listing])" wire:navigate>
            Pricing
        </flux:navbar.item>
        <flux:navbar.item
            :href="route('on-marketplace.listings.edit.availability', [$marketplace, $listing])"
            current
            wire:navigate
        >
            Availability
        </flux:navbar.item>
        <flux:navbar.item :href="route('on-marketplace.listings.edit.photos', [$marketplace, $listing])" wire:navigate>
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <flux:heading level="1" size="xl">Availability</flux:heading>

    <flux:spacer class="my-6" />

    <form class="space-y-6" wire:submit="update">
        <flux:field>
            <flux:label badge="Required">Time zone</flux:label>
            <flux:select wire:model="timezone" placeholder="Select a time zone">
                <flux:select.option value="">Select a time zone</flux:select.option>
                @foreach ($this->timezones as $tz)
                    <flux:select.option value="{{ $tz }}">{{ $tz }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="timezone" />
        </flux:field>

        <flux:field>
            <flux:label badge="Required">Weekly default schedule</flux:label>
            <div class="grid grid-cols-2 gap-2">
                @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                    <flux:checkbox wire:model="weekly_schedule" value="{{ $day }}" label="{{ ucfirst($day) }}" />
                @endforeach
            </div>
            <flux:error name="weekly_schedule" />
        </flux:field>

        <div class="space-y-3">
            @unless (empty($availability_exceptions))
                <div>
                    <flux:heading>Current availability exceptions</flux:heading>

                    <flux:separator variant="subtle" class="mt-3" />

                    <flux:table>
                        <flux:table.rows>
                            @foreach ($availability_exceptions as $i => $exception)
                                <flux:table.row :key="$i">
                                    <flux:table.cell variant="strong" class="w-full tabular-nums">
                                        {{ Carbon::parse($exception['start_date'])->format('M d, Y') }} →
                                        {{ Carbon::parse($exception['end_date'])->format('M d, Y') }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge
                                            color="{{ $exception['available'] ? 'green' : 'red' }}"
                                            inset="top bottom"
                                        >
                                            {{ $exception['available'] ? 'Available' : 'Not available' }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell align="end">
                                        <flux:button
                                            type="button"
                                            size="sm"
                                            variant="subtle"
                                            wire:click="removeException({{ $i }})"
                                            inset="top bottom"
                                        >
                                            Remove
                                        </flux:button>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>

                    <flux:error name="availability_exceptions" />
                </div>
            @endunless

            <div x-data="{ showAddException: false }">
                <flux:button type="button" @click="showAddException = true" x-show="!showAddException" size="sm">
                    Add availability exception
                </flux:button>
                <div x-show="showAddException" class="mt-4" x-cloak>
                    <flux:card class="space-y-6">
                        <div>
                            <flux:heading>Add an availability exception</flux:heading>
                            <flux:text class="mt-2">
                                Use this form to set specific dates when this listing is available or unavailable,
                                overriding the weekly default schedule.
                            </flux:text>
                        </div>
                        <flux:select
                            wire:model="new_exception.available"
                            label="Availability status"
                            placeholder="Availability status"
                        >
                            <flux:select.option value="1">Available</flux:select.option>
                            <flux:select.option value="0">Not available</flux:select.option>
                        </flux:select>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:date-picker wire:model="new_exception.start_date" label="Start date">
                                <x-slot name="badge">
                                    <flux:badge color="red">Required</flux:badge>
                                </x-slot>
                            </flux:date-picker>
                            <flux:date-picker wire:model="new_exception.end_date" label="End date">
                                <x-slot name="badge">
                                    <flux:badge color="red">Required</flux:badge>
                                </x-slot>
                            </flux:date-picker>
                        </div>
                        <div class="flex gap-2">
                            <flux:button type="button" wire:click="addException">Add exception</flux:button>
                            <flux:button type="button" variant="subtle" @click="showAddException = false">
                                Cancel
                            </flux:button>
                        </div>
                    </flux:card>
                </div>
            </div>
        </div>

        <flux:button type="submit" variant="primary">Next</flux:button>
    </form>
</div>
