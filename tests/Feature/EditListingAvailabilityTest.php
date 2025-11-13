<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\assertDatabaseHas;

it('edits a listing availability, requires timezone and weekly_schedule, and updates the record', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'title' => 'Test Listing',
        'description' => 'Test description',
        'address' => '123 Main St',
        'apt_suite' => 'Apt 1',
        'price' => 100.00,
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

    // Validation: timezone required
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.availability', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('timezone', '')
        ->set('weekly_schedule', [
            'monday' => true,
            'tuesday' => false,
            'wednesday' => false,
            'thursday' => false,
            'friday' => false,
            'saturday' => false,
            'sunday' => false,
        ])
        ->call('update')
        ->assertHasErrors(['timezone' => 'required']);

    // Validation: weekly_schedule required
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.availability', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('timezone', 'America/New_York')
        ->set('weekly_schedule', [])
        ->call('update')
        ->assertHasErrors(['weekly_schedule' => 'required']);

    // Success: valid data
    $newSchedule = [
        'monday' => true,
        'tuesday' => true,
        'wednesday' => false,
        'thursday' => false,
        'friday' => true,
        'saturday' => false,
        'sunday' => false,
    ];
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.availability', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('timezone', 'America/New_York')
        ->set('weekly_schedule', $newSchedule)
        ->call('update')
        ->assertHasNoErrors();

    assertDatabaseHas('listings', [
        'id' => $listing->id,
        'timezone' => 'America/New_York',
    ]);
    expect(Listing::find($listing->id)->weekly_schedule)->toBe($newSchedule);
    // Success: valid data with availability exceptions
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
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.availability', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('timezone', 'Europe/London')
        ->set('weekly_schedule', $newSchedule)
        ->set('availability_exceptions', $exceptions)
        ->call('update')
        ->assertHasNoErrors();

    $saved = Listing::find($listing->id);
    expect($saved->availability_exceptions)->toBe($exceptions);
});
