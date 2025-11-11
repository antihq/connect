<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\Marketplace;
use App\Models\Transaction;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user sees reviews for their marketplace', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($organization)->create();
    $transaction = Transaction::factory()->for($marketplace)->create();
    $review = Review::factory()->for($transaction)->create();

    $user->current_organization_id = $organization->id;
    $user->save();

    $this->actingAs($user)
        ->get('/backstage/reviews')
        ->assertStatus(200)
        ->assertSee((string) $review->id)
        ->assertSee($review->comment ?? '')
        ->assertSee((string) $transaction->id);
});

test('user does not see reviews for other marketplaces', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($organization)->create();
    $transaction = Transaction::factory()->for($marketplace)->create();
    $review = Review::factory()->for($transaction)->create();

    $otherMarketplace = Marketplace::factory()->create();
    $otherTransaction = Transaction::factory()->for($otherMarketplace)->create();
    $otherReview = Review::factory()->for($otherTransaction)->create();

    $user->current_organization_id = $organization->id;
    $user->save();

    $this->actingAs($user)
        ->get('/backstage/reviews')
        ->assertStatus(200)
        ->assertSee($review->comment)
        ->assertDontSee($otherReview->comment);
});

test('guest is redirected to login', function () {
    $this->get('/backstage/reviews')
        ->assertRedirect('/login');
});

test('it paginates reviews', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($organization)->create();
    $transaction = Transaction::factory()->for($marketplace)->create();

    $user->current_organization_id = $organization->id;
    $user->save();

    $reviews = Review::factory()->count(25)->for($transaction)->create();

    $this->actingAs($user)
        ->get('/backstage/reviews')
        ->assertStatus(200)
        ->assertSee((string) $reviews[0]->id)
        ->assertSee((string) $reviews[19]->id)
        ->assertDontSeeText($reviews[20]->comment);
});
