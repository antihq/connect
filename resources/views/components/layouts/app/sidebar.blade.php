<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <livewire:organizations-dropdown />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="home" :href="route('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:sidebar.item>
            </flux:sidebar.nav>

            @can('update', auth()->user()->currentOrganization)
                <flux:sidebar.nav>
                    <flux:sidebar.group :heading="__('Organization')">
                        <flux:sidebar.item :href="route('organizations.settings.general', auth()->user()->currentOrganization)" icon="cog-6-tooth" wire:navigate>{{ __('Settings') }}</flux:sidebar.item>
                    </flux:sidebar.group>
                </flux:sidebar.nav>
            @endcan

            <flux:sidebar.spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="min-h-13! items-end">
            <div class="flex gap-2 items-center">
                <flux:sidebar.collapse inset="left" />
                @if($breadcrumbs)
                    <flux:separator vertical class="h-3 my-auto mr-2" />
                    {{ $breadcrumbs }}
                @endif
            </div>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
