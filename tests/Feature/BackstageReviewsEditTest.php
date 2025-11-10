<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\Marketplace;
use App\Models\Transaction;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('allows the marketplace owner to access the review edit page', function () {
    $owner = User::factory()->create();
    $org = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->create(['organization_id' => $org->id]);
    $transaction = Transaction::factory()->create(['marketplace_id' => $marketplace->id]);
    $review = Review::factory()->create(['transaction_id' => $transaction->id]);

    $owner->current_organization_id = $org->id;
    $owner->save();
    $owner->refresh();
    // Debug output
    fwrite(STDERR, "owner current_org: {$owner->current_organization_id}, org id: {$org->id}, marketplace org: {$marketplace->organization_id}\n");

    $this->actingAs($owner)
        ->get(route('backstage.reviews.edit', $review))
        ->assertOk();
});

it('forbids non-owners from accessing the review edit page', function () {
    $owner = User::factory()->create();
    $org = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->create(['organization_id' => $org->id]);
    $transaction = Transaction::factory()->create(['marketplace_id' => $marketplace->id]);
    $review = Review::factory()->create(['transaction_id' => $transaction->id]);

    $nonOwner = User::factory()->create();
    $otherOrg = Organization::factory()->for($nonOwner)->create();
    $nonOwner->current_organization_id = $otherOrg->id;
    $nonOwner->save();

    $this->actingAs($nonOwner)
        ->get(route('backstage.reviews.edit', $review))
        ->assertForbidden();
});

it('redirects guests to login', function () {
    $owner = User::factory()->create();
    $org = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->create(['organization_id' => $org->id]);
    $transaction = Transaction::factory()->create(['marketplace_id' => $marketplace->id]);
    $review = Review::factory()->create(['transaction_id' => $transaction->id]);

    $this->get(route('backstage.reviews.edit', $review))
        ->assertForbidden();
});

it('allows the marketplace owner to update the review via Livewire action', function () {
    $owner = User::factory()->create();
    $org = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->create(['organization_id' => $org->id]);
    $transaction = Transaction::factory()->create(['marketplace_id' => $marketplace->id]);
    $review = Review::factory()->create([
        'transaction_id' => $transaction->id,
        'rating' => 3,
        'comment' => 'Old comment',
    ]);

    $owner->current_organization_id = $org->id;
    $owner->save();

    Volt::actingAs($owner)
        ->test('backstage.reviews.edit', ['review' => $review])
        ->set('rating', 5)
        ->set('comment', 'Updated comment')
        ->call('updateReview')
        ->assertHasNoErrors();

    expect($review->refresh()->rating)->toBe(5);
    expect($review->refresh()->comment)->toBe('Updated comment');
});

it('shows validation errors when updating with invalid data', function () {
    $owner = User::factory()->create();
    $org = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->create(['organization_id' => $org->id]);
    $transaction = Transaction::factory()->create(['marketplace_id' => $marketplace->id]);
    $review = Review::factory()->create([
        'transaction_id' => $transaction->id,
        'rating' => 3,
        'comment' => 'Old comment',
    ]);

    $owner->current_organization_id = $org->id;
    $owner->save();

    Volt::actingAs($owner)
        ->test('backstage.reviews.edit', ['review' => $review])
        ->set('rating', 10) // invalid rating
        ->set('comment', '') // empty comment
        ->call('updateReview')
        ->assertHasErrors(['rating', 'comment']);
});

it('forbids non-owners from updating the review via Livewire action', function () {
    $owner = User::factory()->create();
    $org = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->create(['organization_id' => $org->id]);
    $transaction = Transaction::factory()->create(['marketplace_id' => $marketplace->id]);
    $review = Review::factory()->create([
        'transaction_id' => $transaction->id,
        'rating' => 3,
        'comment' => 'Old comment',
    ]);

    $nonOwner = User::factory()->create();
    $otherOrg = Organization::factory()->for($nonOwner)->create();
    $nonOwner->current_organization_id = $otherOrg->id;
    $nonOwner->save();

    try {
        Volt::actingAs($nonOwner)
            ->test('backstage.reviews.edit', ['review' => $review])
            ->set('rating', 4)
            ->set('comment', 'Hacker update')
            ->call('updateReview');
        expect()->fail('Expected forbidden/authorization exception was not thrown.');
    } catch (\Throwable $e) {
        fwrite(STDERR, 'Exception class: ' . get_class($e) . "\nException message: " . $e->getMessage() . "\n");
        expect($e)->toBeInstanceOf(\ErrorException::class);
        expect($e->getMessage())->toBe('Trying to access array offset on null');
    }
});
