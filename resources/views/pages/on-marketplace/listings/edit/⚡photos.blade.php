<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Photo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public Marketplace $marketplace;

    public Listing $listing;

    public array $newPhotos = [];

    public function rules(): array
    {
        return [
            'newPhotos.*' => ['image', 'max:2048'], // 2MB per image
        ];
    }

    public function savePhotos()
    {
        $this->validate();

        foreach ($this->newPhotos as $photo) {
            $this->listing->photos()->create([
                'path' => $photo->storePublicly("listings/{$this->listing->id}", 'public'),
            ]);
        }

        $this->newPhotos = [];

        $this->listing->refresh();

        if ($this->listing->status === 'draft' && $this->listing->isPublishable()) {
            $this->listing->publish();
        }
    }

    public function removePhoto(Photo $photo)
    {
        abort_unless($photo->listing->is($this->listing), 403);

        Storage::delete($photo->path);

        $photo->delete();

        $this->listing->refresh();
    }

    public function removeNewPhoto($index)
    {
        $photo = $this->newPhotos[$index];

        $photo->delete();

        unset($this->newPhotos[$index]);

        $this->newPhotos = array_values($this->newPhotos);
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:navbar class="-mb-px">
        <flux:navbar.item :href="route('on-marketplace.listings.edit.details', [$marketplace, $listing])" wire:navigate>
            Details
        </flux:navbar.item>
        <flux:navbar.item
            :href="route('on-marketplace.listings.edit.location', [$marketplace, $listing])"
            wire:navigate
        >
            Location
        </flux:navbar.item>
        <flux:navbar.item :href="route('on-marketplace.listings.edit.pricing', [$marketplace, $listing])" wire:navigate>
            Pricing
        </flux:navbar.item>
        <flux:navbar.item
            :href="route('on-marketplace.listings.edit.availability', [$marketplace, $listing])"
            wire:navigate
        >
            Availability
        </flux:navbar.item>
        <flux:navbar.item
            :href="route('on-marketplace.listings.edit.photos', [$marketplace, $listing])"
            current
            wire:navigate
        >
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <flux:heading level="1" size="xl">Photos</flux:heading>

    <flux:spacer class="my-6" />

    <form class="space-y-6" wire:submit="savePhotos">
        @unless ($listing->photos->isEmpty())
            <flux:field>
                <flux:label>Current Photos</flux:label>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    @foreach ($listing->photos as $photo)
                        <div class="group relative">
                            <img src="/{{ $photo->path }}" class="h-32 w-full rounded object-cover shadow" />
                            <div class="absolute top-2 right-2 flex">
                                <flux:button type="button" wire:click="removePhoto({{ $photo->id }})" size="xs">
                                    Remove
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </flux:field>
        @endunless

        <flux:field>
            <flux:label>Add Photos</flux:label>
            <flux:card class="space-y-6">
                <flux:file-upload wire:model="newPhotos" label="Upload photos" multiple>
                    <x-slot name="badge">
                        <flux:badge color="gray">Optional</flux:badge>
                    </x-slot>
                    <flux:file-upload.dropzone
                        heading="Drop photos here or click to browse"
                        text="JPG, PNG, GIF up to 2MB"
                    />
                </flux:file-upload>
                <div class="flex flex-col gap-2">
                    @foreach ($newPhotos as $index => $photo)
                        <flux:file-item :heading="$photo->getClientOriginalName()" :size="$photo->getSize()">
                            <x-slot name="actions">
                                <flux:file-item.remove
                                    wire:click="removeNewPhoto({{ $index }})"
                                    aria-label="Remove file: {{ $photo->getClientOriginalName() }}"
                                />
                            </x-slot>
                        </flux:file-item>
                    @endforeach
                </div>
            </flux:card>
        </flux:field>

        <flux:button type="submit" variant="primary">
            {{ $listing->isPublishable() ? 'Save & Publish' : 'Save' }}
        </flux:button>
    </form>
</div>
