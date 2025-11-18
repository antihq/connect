<?php

use App\Models\Marketplace;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    \Stripe\Stripe::setApiKey('sk_test_fake');
});

it('marks transaction paid on successful stripe session', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $transaction = Transaction::factory()->for($marketplace)->for($user)->create(['status' => 'pending']);

    actingAs($user);

    $mockSession = (object) [
        'id' => 'cs_test_123',
        'payment_status' => 'paid',
    ];
    $stripeMock = Mockery::mock('overload:\\Stripe\\Checkout\\Session');
    $stripeMock->shouldReceive('retrieve')->with('cs_test_123')->andReturn($mockSession);

    $response = get(route('marketplaces.orders.success', [$marketplace, $transaction]).'?session_id=cs_test_123');
    $response->assertStatus(200);
    $response->assertSee('Thank you! Your payment was successful');
    expect($transaction->fresh()->status)->toBe('paid');
    expect($transaction->activities()->where('type', 'paid')->exists())->toBeTrue();
});

it('shows already paid if transaction already paid', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $transaction = Transaction::factory()->for($marketplace)->for($user)->create(['status' => 'paid']);

    actingAs($user);
    $response = get(route('marketplaces.orders.success', [$marketplace, $transaction]).'?session_id=cs_test_123');
    $response->assertStatus(200);
    $response->assertSee('This order has already been paid');
});

it('shows error if stripe session not paid', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $transaction = Transaction::factory()->for($marketplace)->for($user)->create(['status' => 'pending']);

    actingAs($user);
    $mockSession = (object) [
        'id' => 'cs_test_123',
        'payment_status' => 'unpaid',
    ];
    $stripeMock = Mockery::mock('overload:\\Stripe\\Checkout\\Session');
    $stripeMock->shouldReceive('retrieve')->with('cs_test_123')->andReturn($mockSession);

    $response = get(route('marketplaces.orders.success', [$marketplace, $transaction]).'?session_id=cs_test_123');
    $response->assertStatus(200);
    $response->assertSee('There was a problem verifying your payment');
    expect($transaction->fresh()->status)->toBe('pending');
});

it('shows error if no session id', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $transaction = Transaction::factory()->for($marketplace)->for($user)->create(['status' => 'pending']);

    actingAs($user);
    $response = get(route('marketplaces.orders.success', [$marketplace, $transaction]));
    $response->assertStatus(200);
    $response->assertSee('There was a problem verifying your payment');
});
