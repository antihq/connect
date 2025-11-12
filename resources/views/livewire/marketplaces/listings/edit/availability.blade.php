<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Livewire\Volt\Component;

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
        // Ensure available is boolean
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

        return $this->redirectRoute('marketplaces.listings.edit.photos', [
            'marketplace' => $this->marketplace,
            'listing' => $this->listing,
        ], navigate: true);
    }

    public function getTimezonesProperty(): array
    {
        return \DateTimeZone::listIdentifiers();
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:navbar class="-mb-px">
        <flux:navbar.item :href="route('marketplaces.listings.edit.details', [$marketplace, $listing])">
            Details
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.location', [$marketplace, $listing])">
            Location
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.pricing', [$marketplace, $listing])">
            Pricing
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.availability', [$marketplace, $listing])" current>
            Availability
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.photos', [$marketplace, $listing])">
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <flux:heading level="1" size="xl">Availability</flux:heading>

    <flux:spacer class="my-6" />

    <form class="space-y-6" wire:submit="update">
        <flux:field>
            <flux:label badge="Required">Time zone</flux:label>
            <flux:select wire:model="timezone">
                <option value="">Select a time zone</option>
                @foreach ($this->timezones as $tz)
                    <option value="{{ $tz }}">{{ $tz }}</option>
                @endforeach
            </flux:select>
            <flux:error name="timezone" />
        </flux:field>

        <flux:field>
            <flux:label badge="Required">Weekly default schedule</flux:label>
            <div class="grid grid-cols-2 gap-2">
                @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                    <flux:checkbox wire:model="weekly_schedule.{{ $day }}" label="{{ ucfirst($day) }}" />
                @endforeach
            </div>
            <flux:error name="weekly_schedule" />
        </flux:field>

        <flux:field>
            <flux:label badge="Optional">Availability exceptions</flux:label>

            @unless (empty($availability_exceptions))
                <flux:separator variant="subtle" />

                <flux:table>
                    <flux:table.rows>
                        @foreach ($availability_exceptions as $i => $exception)
                            <flux:table.row :key="$i">
                                <flux:table.cell variant="strong" class="w-full tabular-nums">
                                    {{ $exception['start_date'] }} → {{ $exception['end_date'] }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge
                                        color="{{ $exception['available'] ? 'green' : 'red' }}"
                                        size="sm"
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
                                    >
                                        Remove
                                    </flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endunless

            <flux:spacer class="mb-3" />

            <flux:card class="space-y-6">
                <flux:select wire:model="new_exception.available" label="Availability status">
                    <option value="1">Available</option>
                    <option value="0">Not available</option>
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
                <flux:button type="button" wire:click="addException">Add exception</flux:button>
            </flux:card>
            <flux:error name="availability_exceptions" />
        </flux:field>

        <flux:button type="submit" variant="primary">Next</flux:button>
    </form>
</div>
