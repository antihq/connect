<?php

use App\Models\Vote;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

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
            'email' => ['required', 'email'],
        ]);

        $vote = Vote::where('email', $this->email)->first();
        if ($vote) {
            $vote->option = $this->option;
            $vote->save();

            return;
        }

        Vote::create([
            'option' => $this->option,
            'email' => $this->email,
        ]);
    }
} ?>

<div x-init="$wire.vote" class="flex justify-center">
    <flux:heading size="xl">Thank you for voting!</flux:heading>
</div>
