<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased bg-white">
    <head>
        @include('partials.head', ['title' => 'Launch your marketplace within hours, or even minutes'])
    </head>
    <body>
        <flux:main container>
            {{ $slot }}
        </flux:main>
    </body>
</html>
