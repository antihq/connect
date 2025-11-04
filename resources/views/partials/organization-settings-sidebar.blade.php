<div class="me-10 w-full pb-4 md:w-[220px]">
    <flux:navlist>
        <flux:navlist.item
            :href="route('organizations.settings.general', $organization)"
            :current="request()->routeIs('organizations.settings.general')"
            wire:navigate
        >
            {{ __('General') }}
        </flux:navlist.item>
        <flux:navlist.item
            :href="route('organizations.settings.members', $organization)"
            :current="request()->routeIs('organizations.settings.members')"
            wire:navigate
        >
            {{ __('Members') }}
        </flux:navlist.item>
    </flux:navlist>
</div>
