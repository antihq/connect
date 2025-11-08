<x-layouts.outline :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    <flux:toast />
</x-layouts.outline>
