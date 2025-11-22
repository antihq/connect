<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

it('requires timezone when editing availability', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'timezone' => 'UTC',
    ]);

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
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'weekly_schedule' => [
            'monday' => false,
            'tuesday' => false,
            'wednesday' => false,
            'thursday' => false,
            'friday' => false,
            'saturday' => false,
            'sunday' => false,
        ],
    ]);

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
        'weekly_schedule' => [
            'monday' => false,
            'tuesday' => false,
            'wednesday' => false,
            'thursday' => false,
            'friday' => false,
            'saturday' => false,
            'sunday' => false,
        ],
    ]);

    $newSchedule = [
        'monday' => true,
        'tuesday' => true,
        'wednesday' => false,
        'thursday' => false,
        'friday' => true,
        'saturday' => false,
        'sunday' => false,
    ];
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
    expect($updated->weekly_schedule)->toBe($newSchedule);
});

it('updates listing availability with exceptions', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'timezone' => 'UTC',
        'weekly_schedule' => [
            'monday' => false,
            'tuesday' => false,
            'wednesday' => false,
            'thursday' => false,
            'friday' => false,
            'saturday' => false,
            'sunday' => false,
        ],
    ]);

    $newSchedule = [
        'monday' => true,
        'tuesday' => true,
        'wednesday' => false,
        'thursday' => false,
        'friday' => true,
        'saturday' => false,
        'sunday' => false,
    ];
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
        ->set('timezone', 'Europe/London')
        ->set('weekly_schedule', $newSchedule)
        ->set('availability_exceptions', $exceptions)
        ->call('update')
        ->assertHasNoErrors();

    $listing->refresh();
    expect($listing->timezone)->toBe('Europe/London');
    expect($listing->weekly_schedule)->toBe($newSchedule);
    $listingExceptions = $listing->availabilityExceptions()->get()->map(fn ($e) => [
        'available' => $e->available,
        'start_date' => $e->start_date->toDateString(),
        'end_date' => $e->end_date->toDateString(),
    ])->toArray();
    expect($listingExceptions)->toBe($exceptions);
});
