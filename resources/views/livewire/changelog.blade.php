<?php

use App\Models\UpdateSubscription;
use App\Notifications\UpdateSubscriptionConfirmationNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.changelog'), Title('Changelog')] class extends Component
{
    //
}; ?>

<div>
    <x-changelog.entry date="2025-11-11">
        <x-changelog.img src="/" />
        <h2><a href="#changelog-2023-04-06">Culpa voluptate ea laborum nisi in est nulla.</a></h2>
        <p>
            Deserunt pariatur veniam velit elit voluptate.
        </p>
        <p>
            Ullamco magna magna incididunt voluptate quis eu sit proident.
        </p>
        <h3>
            <flux:icon.sparkles variant="solid" />
            Improvements
        </h3>
        <ul>
            <li>Sunt eu id cupidatat dolor ad dolore elit deserunt occaecat Lorem eiusmod aute.</li>
            <li>Dolor aliqua ut id ullamco duis duis cupidatat mollit commodo pariatur.</li>
            <li>Sunt eu id cupidatat dolor ad dolore elit deserunt occaecat Lorem eiusmod aute.</li>
            <li>Dolor aliqua ut id ullamco duis duis cupidatat mollit commodo pariatur.</li>
            <li>Sunt eu id cupidatat dolor ad dolore elit deserunt occaecat Lorem eiusmod aute.</li>
            <li>Dolor aliqua ut id ullamco duis duis cupidatat mollit commodo pariatur.</li>
        </ul>
    </x-changelog.entry>
</div>
