<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased bg-white">
    <head>
        @include('partials.head')
    </head>
    <body>
        <flux:header container class="border-b border-zinc-200">
            <flux:navbar class="-mb-px">
                <flux:navbar.item :href="route('home')">Home</flux:navbar.item>
                <flux:navbar.item :href="route('updates')">Updates</flux:navbar.item>
            </flux:navbar>
        </flux:header>

        <flux:main container>
            {{ $slot }}
        </flux:main>
    </body>
</html>
