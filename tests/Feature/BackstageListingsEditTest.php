<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\Marketplace;
use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);


    it('shows the edit form for user\'s own listing', function () {
        $user = User::factory()->create();
        $org = Organization::factory()->for($user)->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'price' => 100.00,
        ]);
        $user->current_organization_id = $org->id;
        $user->save();

        Volt::actingAs($user)
            ->test('backstage.listings.edit', ['listing' => $listing->id])
            ->assertSet('title', 'Original Title')
            ->assertSet('description', 'Original Description')
            ->assertSet('price', 100.00);
    });

    it('updates the listing with valid data', function () {
        $user = User::factory()->create();
        $org = Organization::factory()->for($user)->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create([
            'title' => 'Old Title',
            'description' => 'Old Description',
            'price' => 50.00,
        ]);
        $user->current_organization_id = $org->id;
        $user->save();

        Volt::actingAs($user)
            ->test('backstage.listings.edit', ['listing' => $listing->id])
            ->set('title', 'New Title')
            ->set('description', 'New Description')
            ->set('price', 200.00)
            ->call('save')
            ->assertHasNoErrors();

        $listing->refresh();
        expect($listing->title)->toBe('New Title');
        expect($listing->description)->toBe('New Description');
        expect($listing->price)->toBe(200.00);
    });

    it('shows validation errors for invalid input', function () {
        $user = User::factory()->create();
        $org = Organization::factory()->for($user)->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create();
        $user->current_organization_id = $org->id;
        $user->save();

        Volt::actingAs($user)
            ->test('backstage.listings.edit', ['listing' => $listing->id])
            ->set('title', '')
            ->set('price', -10)
            ->call('save')
            ->assertHasErrors(['title' => 'required', 'price' => 'min']);
    });

    it('returns 404 for listing from another marketplace', function () {
        $user = User::factory()->create();
        $org = Organization::factory()->for($user)->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create();
        $otherOrg = Organization::factory()->create();
        $otherMarketplace = Marketplace::factory()->for($otherOrg)->create();
        $otherListing = Listing::factory()->for($otherMarketplace)->create();
        $user->current_organization_id = $org->id;
        $user->save();

        Volt::actingAs($user)
            ->test('backstage.listings.edit', ['listing' => $otherListing->id])
            ->assertNotFound();
    });

    it('redirects guest to login', function () {
        $org = Organization::factory()->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create();

        $response = get(route('backstage.listings.edit', $listing));
        $response->assertRedirect(route('login'));
    });

    it('returns 404 for non-existent listing', function () {
        $user = User::factory()->create();
        Volt::actingAs($user)
            ->test('backstage.listings.edit', ['listing' => 999999])
            ->assertNotFound();
    });
