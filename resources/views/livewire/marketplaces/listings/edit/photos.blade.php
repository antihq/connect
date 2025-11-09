<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;
use App\Models\Listing;
use Illuminate\Support\Arr;
use Livewire\WithFileUploads;

new class extends Component {
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

    public function upload()
    {
        $this->validate();
        $photos = $this->listing->photos ?? [];
        foreach ($this->newPhotos as $photo) {
            $path = $photo->store("listings/{$this->listing->id}", 'public');
            $photos[] = 'storage/' . $path;
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
            \Storage::delete($photoPath);
            Arr::forget($photos, $index);
            $photos = array_values($photos); // reindex
            $this->listing->update([
                'photos' => $photos,
            ]);
            $this->listing->refresh();
        }
    }
}; ?>

<div>
    <flux:navbar>
        <flux:navbar.item :href="route('marketplaces.show', $marketplace)">Home</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.create', $marketplace)">Post a new listings</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.inbox.orders', $marketplace)">Inbox</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.account.listings', $marketplace)">Profile</flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <flux:navbar class="mb-6">
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
        <flux:navbar.item :href="route('marketplaces.listings.edit.photos', [$marketplace, $listing])" active>
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <form class="space-y-6" wire:submit="upload" enctype="multipart/form-data">
        <flux:input label='Add Photos' wire:model="newPhotos" type="file" multiple accept="image/*" />
        <flux:button type="submit">Upload</flux:button>
    </form>

    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach(($listing->photos ?? []) as $idx => $photo)
            <div class="relative group">
                <img src="/{{ $photo }}" class="rounded shadow w-full h-32 object-cover" />
                <button type="button" wire:click="removePhoto({{ $idx }})" class="absolute top-2 right-2 bg-white bg-opacity-80 rounded-full p-1 text-red-600 hover:bg-opacity-100">Remove</button>
            </div>
        @endforeach
    </div>
</div>
