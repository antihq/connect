<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
    public User $user;
};
?>

<div>
    @include('partials.backstage-navbar')

        <flux:card>
            <h2 class="text-xl font-bold mb-2">{{ $user->name }}</h2>
            <p class="mb-4">{{ $user->email }}</p>
            <flux:description>
                <strong>ID:</strong> {{ $user->id }}<br>
                <strong>Joined:</strong> {{ $user->created_at->format('Y-m-d') }}<br>
                <strong>Email Verified:</strong> {{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d') : 'No' }}
            </flux:description>
            <div class="mt-4">
                <flux:link :href="route('backstage.users.edit', $user)">Edit User</flux:link>
            </div>
        </flux:card>
</div>
