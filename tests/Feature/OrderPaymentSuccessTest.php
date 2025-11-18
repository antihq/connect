<?php

namespace Tests\Feature;

use App\Models\Marketplace;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderPaymentSuccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Prevent actual Stripe API calls
        \Stripe\Stripe::setApiKey('sk_test_fake');
    }

    public function test_marks_transaction_paid_on_successful_stripe_session()
    {
        $user = User::factory()->create();
        $marketplace = Marketplace::factory()->create();
        $transaction = Transaction::factory()->for($marketplace)->for($user)->create(['status' => 'pending']);

        $this->actingAs($user);

        // Mock Stripe Checkout Session
        $mockSession = (object) [
            'id' => 'cs_test_123',
            'payment_status' => 'paid',
        ];
        $stripeMock = Mockery::mock('overload:\\Stripe\\Checkout\\Session');
        $stripeMock->shouldReceive('retrieve')->with('cs_test_123')->andReturn($mockSession);

        $response = $this->get(route('marketplaces.orders.success', [$marketplace, $transaction]).'?session_id=cs_test_123');
        $response->assertStatus(200);
        $response->assertSee('Thank you! Your payment was successful');
        $this->assertEquals('paid', $transaction->fresh()->status);
    }

    public function test_shows_already_paid_if_transaction_already_paid()
    {
        $user = User::factory()->create();
        $marketplace = Marketplace::factory()->create();
        $transaction = Transaction::factory()->for($marketplace)->for($user)->create(['status' => 'paid']);

        $this->actingAs($user);
        $response = $this->get(route('marketplaces.orders.success', [$marketplace, $transaction]).'?session_id=cs_test_123');
        $response->assertStatus(200);
        $response->assertSee('This order has already been paid');
    }

    public function test_shows_error_if_stripe_session_not_paid()
    {
        $user = User::factory()->create();
        $marketplace = Marketplace::factory()->create();
        $transaction = Transaction::factory()->for($marketplace)->for($user)->create(['status' => 'pending']);

        $this->actingAs($user);
        $mockSession = (object) [
            'id' => 'cs_test_123',
            'payment_status' => 'unpaid',
        ];
        $stripeMock = Mockery::mock('overload:\\Stripe\\Checkout\\Session');
        $stripeMock->shouldReceive('retrieve')->with('cs_test_123')->andReturn($mockSession);

        $response = $this->get(route('marketplaces.orders.success', [$marketplace, $transaction]).'?session_id=cs_test_123');
        $response->assertStatus(200);
        $response->assertSee('There was a problem verifying your payment');
        $this->assertEquals('pending', $transaction->fresh()->status);
    }

    public function test_shows_error_if_no_session_id()
    {
        $user = User::factory()->create();
        $marketplace = Marketplace::factory()->create();
        $transaction = Transaction::factory()->for($marketplace)->for($user)->create(['status' => 'pending']);

        $this->actingAs($user);
        $response = $this->get(route('marketplaces.orders.success', [$marketplace, $transaction]));
        $response->assertStatus(200);
        $response->assertSee('There was a problem verifying your payment');
    }
}
