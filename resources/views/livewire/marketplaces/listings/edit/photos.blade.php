<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
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
        $photos = $this->listing->photos ?? [];
        foreach ($this->newPhotos as $photo) {
            $path = $photo->store("listings/{$this->listing->id}", 'public');
            $photos[] = 'storage/'.$path;
        }
        $this->listing->update([
            'photos' => $photos,
        ]);
        $this->newPhotos = [];
        $this->listing->refresh();
    }

    public function removePhoto($index)
    {
        $photos = $this->listing->photos ?? [];
        if (isset($photos[$index])) {
            $photoPath = str_replace('storage/', 'public/', $photos[$index]);
            Storage::delete($photoPath);
            Arr::forget($photos, $index);
            $photos = array_values($photos); // reindex
            $this->listing->update([
                'photos' => $photos,
            ]);
            $this->listing->refresh();
        }
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:navbar class="-mb-px">
        <flux:navbar.item :href="route('marketplaces.listings.edit.details', [$marketplace, $listing])">
            Details
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.location', [$marketplace, $listing])">
            Location
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.pricing', [$marketplace, $listing])">
            Pricing
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.availability', [$marketplace, $listing])">
            Availability
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.photos', [$marketplace, $listing])" current>
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <flux:heading level="1" size="xl">Photos</flux:heading>

    <flux:spacer class="my-6" />

    <form class="space-y-6" wire:submit="savePhotos">
        @unless (empty($listing->photos))
            <flux:field>
                <flux:label>Current Photos</flux:label>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    @foreach (($listing->photos ?? []) as $idx => $photo)
                        <div class="group relative">
                            <img src="/{{ $photo }}" class="h-32 w-full rounded object-cover shadow" />
                            <div class="absolute top-2 right-2 flex">
                                <flux:button type="button" wire:click="removePhoto({{ $idx }})" size="xs">
                                    Remove
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </flux:field>
        @endunless

        <flux:field>
            <flux:label badge="Optional">Add Photos</flux:label>
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
                    @foreach ($newPhotos as $idx => $photo)
                        @php
                            $imageUrl = null;
                            if (method_exists($photo, 'getMimeType') && str_starts_with($photo->getMimeType(), 'image/')) {
                                $imageUrl = $photo->temporaryUrl();
                            }
                        @endphp

                        <flux:file-item
                            :heading="$photo->getClientOriginalName()"
                            :size="$photo->getSize()"
                            :image="$imageUrl"
                        >
                            <x-slot name="actions">
                                <flux:file-item.remove
                                    wire:click="removePhoto({{ $idx }})"
                                    aria-label="Remove file: {{ $photo->getClientOriginalName() }}"
                                />
                            </x-slot>
                        </flux:file-item>
                    @endforeach
                </div>
            </flux:card>
        </flux:field>

        <flux:button type="submit" variant="primary">Save</flux:button>
    </form>
</div>
