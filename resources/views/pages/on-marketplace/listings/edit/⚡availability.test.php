<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use App\Models\WeeklyScheduleEntry;
use Livewire\Livewire;

it('requires timezone when editing availability', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create();

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.availability', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('timezone', '')
        ->call('update')
        ->assertHasErrors(['timezone' => 'required']);
});

it('requires weekly_schedule when editing availability', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create();

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.availability', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('weekly_schedule', [])
        ->call('update')
        ->assertHasErrors(['weekly_schedule' => 'required']);
});

it('updates listing availability with valid data', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'timezone' => 'UTC',
    ]);
    WeeklyScheduleEntry::factory()
        ->count(7)
        ->sequence(
            ['day' => 'monday', 'available' => false],
            ['day' => 'tuesday', 'available' => false],
            ['day' => 'wednesday', 'available' => false],
            ['day' => 'thursday', 'available' => false],
            ['day' => 'friday', 'available' => false],
            ['day' => 'saturday', 'available' => false],
            ['day' => 'sunday', 'available' => false],
        )
        ->for($listing)
        ->create();

    $newSchedule = ['monday', 'tuesday', 'friday'];
    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.availability', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('timezone', 'America/New_York')
        ->set('weekly_schedule', $newSchedule)
        ->call('update')
        ->assertHasNoErrors();

    $updated = Listing::find($listing->id);
    expect($updated->timezone)->toBe('America/New_York');
    $actualSchedule = $updated->weeklyScheduleEntries->where('available', true)->pluck('day')->toArray();
    expect(sort($actualSchedule))->toBe(sort($newSchedule));
});

it('updates listing availability with exceptions', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create(['timezone' => 'UTC']);
    WeeklyScheduleEntry::factory()->for($listing)->create(['day' => 'monday', 'available' => true]);

    $exceptions = [
        [
            'available' => true,
            'start_date' => '2025-12-01',
            'end_date' => '2025-12-05',
        ],
        [
            'available' => false,
            'start_date' => '2025-12-10',
            'end_date' => '2025-12-12',
        ],
    ];
    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.availability', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('availability_exceptions', $exceptions)
        ->call('update')
        ->assertHasNoErrors();

    $listing->refresh();
    $listingExceptions = $listing->availabilityExceptions()->get()->map(fn ($e) => [
        'available' => $e->available,
        'start_date' => $e->start_date->toDateString(),
        'end_date' => $e->end_date->toDateString(),
    ])->toArray();
    expect($listingExceptions)->toBe($exceptions);
});

it('rejects invalid weekdays in weekly_schedule', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create();

    $invalidSchedule = ['monday', 'funday', 'sunday'];
    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.availability', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('weekly_schedule', $invalidSchedule)
        ->call('update')
        ->assertHasErrors(['weekly_schedule.1' => 'in']);
});
