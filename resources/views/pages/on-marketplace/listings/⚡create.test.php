<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('forbids non-members from accessing the create listing page', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    actingAs($user)
        ->get(route('on-marketplace.listings.create', $marketplace))
        ->assertRedirect(route('on-marketplace.sign-in', $marketplace));
});

it('validates required fields when creating a listing as a member', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $marketplace->addMember($user);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.create', ['marketplace' => $marketplace])
        ->set('title', '')
        ->set('description', '')
        ->call('create')
        ->assertHasErrors(['title' => 'required', 'description' => 'required']);
});

it('allows members to create a listing', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $marketplace->addMember($user);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.create', ['marketplace' => $marketplace])
        ->set('title', 'Test Listing')
        ->set('description', 'Test description')
        ->call('create')
        ->assertHasNoErrors();

    $listing = Listing::where('title', 'Test Listing')->where('marketplace_id', $marketplace->id)->first();

    expect($listing)->not->toBeNull();
    expect($listing->description)->toBe('Test description');
    expect($listing->status)->toBe('draft');
    expect($listing->user_id)->toBe($user->id);
    expect($listing->marketplace_id)->toBe($marketplace->id);
});
