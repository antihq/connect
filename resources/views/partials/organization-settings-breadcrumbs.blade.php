<flux:breadcrumbs>
    <flux:breadcrumbs.item href="{{ route('dashboard') }}" wire:navigate>{{ $organization->name }}</flux:breadcrumbs.item>
    <flux:breadcrumbs.item href="{{ route('organizations.settings.general', $organization) }}" wire:navigate>{{ __('Settings') }}</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>{{ $current }}</flux:breadcrumbs.item>
</flux:breadcrumbs>
