<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use App\Models\Vote;

new #[Layout('components.layouts.site')] class extends Component
{
    #[Url]
    public ?int $option = null;

    #[Url]
    public ?string $email = null;

    public function vote()
    {
        $this->validate([
            'option' => ['required', 'integer', 'between:1,10'],
            'email' => ['required', 'email', 'unique:votes,email'],
        ]);

        Vote::create([
            'option' => $this->option,
            'email' => $this->email,
        ]);
    }
} ?>

<div x-init="$wire.vote" class="flex justify-center">
    <flux:heading size="xl">
        Thank you for voting!
    </flux:heading>
</div>
