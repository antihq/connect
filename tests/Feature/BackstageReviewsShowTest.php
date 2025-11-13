<?php

use App\Models\Marketplace;
use App\Models\Organization;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('marketplace owner can see review details', function () {
    $owner = User::factory()->create();
    $organization = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->for($organization)->create();
    $reviewer = User::factory()->create();
    $transaction = \App\Models\Transaction::factory()->for($marketplace)->for($reviewer)->create();
    $review = Review::factory()
        ->for($transaction)
        ->for($reviewer, 'reviewer')
        ->create([
            'rating' => 5,
            'comment' => 'Excellent service!',
        ]);

    $this->actingAs($owner)
        ->get(route('backstage.reviews.show', $review))
        ->assertOk()
        ->assertSee('Review Details')
        ->assertSee('5')
        ->assertSee('Excellent service!')
        ->assertSee($reviewer->name)
        ->assertSee($marketplace->name)
        ->assertSee($owner->name);
});

test('non-owner cannot see review details', function () {
    $owner = User::factory()->create();
    $organization = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->for($organization)->create();
    $reviewer = User::factory()->create();
    $transaction = \App\Models\Transaction::factory()->for($marketplace)->for($reviewer)->create();
    $review = Review::factory()
        ->for($transaction)
        ->for($reviewer, 'reviewer')
        ->create();

    $nonOwner = User::factory()->create();

    $this->actingAs($nonOwner)
        ->get(route('backstage.reviews.show', $review))
        ->assertForbidden();
});
