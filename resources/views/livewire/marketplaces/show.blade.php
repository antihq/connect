<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;

new class extends Component {
    public Marketplace $marketplace;
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])
    <flux:separator class="mb-6" />
    <flux:heading>Marketplace {{ $marketplace->name }}</flux:heading>
</div>
