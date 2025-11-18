<?php

use App\Models\Marketplace;
use App\Models\Transaction;
use Illuminate\Support\Facades\Request;
use Livewire\Volt\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public Transaction $transaction;

    public ?string $session_id = null;

    public bool $verified = false;

    public bool $already_paid = false;

    public bool $error = false;

    public function mount()
    {
        $this->session_id = request('session_id');
        if (! $this->session_id) {
            $this->error = true;

            return;
        }

        // Only mark as paid if not already paid
        if ($this->transaction->status === 'paid' || $this->transaction->status === 'completed') {
            $this->already_paid = true;
            $this->verified = true;

            return;
        }

        try {
            \Stripe\Stripe::setApiKey(config('stripe.secret', config('cashier.secret')));
            $session = \Stripe\Checkout\Session::retrieve($this->session_id);
            if ($session && $session->payment_status === 'paid') {
                $this->transaction->status = 'paid';
                $this->transaction->save();
                $this->transaction->activities()->create([
                    'type' => 'paid',
                    'description' => 'Order paid via Stripe Checkout',
                    'user_id' => $this->transaction->user_id,
                ]);
                $this->verified = true;
            } else {
                $this->error = true;
            }
        } catch (\Throwable $e) {
            $this->error = true;
        }
    }
}; ?>

<flux:container class="[:where(&)]:max-w-2xl!">
    <flux:main>
        <flux:heading size="xl" as="h1">Order Payment</flux:heading>
        <flux:spacer class="my-10" />
        @if ($error)
            <flux:card class="mb-6">
                <div class="text-red-600">There was a problem verifying your payment. Please contact support.</div>
            </flux:card>
        @elseif ($already_paid)
            <flux:card class="mb-6">
                <div class="text-green-600">This order has already been paid.</div>
            </flux:card>
        @elseif ($verified)
            <flux:card class="mb-6">
                <div class="text-green-600">Thank you! Your payment was successful.</div>
            </flux:card>
        @else
            <flux:card class="mb-6">
                <div>Verifying payment...</div>
            </flux:card>
        @endif
        <flux:spacer class="my-10" />
        <flux:button :href="route('marketplaces.orders.show', [$marketplace, $transaction])" color="primary">
            Back to Order
        </flux:button>
    </flux:main>
</flux:container>
