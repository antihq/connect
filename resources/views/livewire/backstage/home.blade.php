<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    #[Computed]
    public function marketplace(): \App\Models\Marketplace
    {
        return Auth::user()->currentOrganization->marketplace;
    }
}; ?>

<div>
    Marketplace: {{ $this->marketplace->id }}
</div>
