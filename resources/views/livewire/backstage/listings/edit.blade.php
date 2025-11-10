<?php

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public int $listing;
public string $title = '';
public string $description = '';
public ?float $price = null;

protected ?Listing $listingModel = null;

public function mount($listing)
{
    $user = Auth::user();
    try {
        $model = Listing::findOrFail($listing);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404);
    }

    $org = $user->current_organization_id
        ? $user->organizations->find($user->current_organization_id)
        : null;

    if (!$org || !$org->marketplace || $model->marketplace_id !== $org->marketplace->id) {
        abort(404);
    }

    $this->listingModel = $model;
    $this->title = $model->title;
    $this->description = $model->description;
    $this->price = $model->price;
}

public function save()
{
    $this->validate([
        'title' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'price' => ['required', 'numeric', 'min:0'],
    ]);

    $model = Listing::findOrFail($this->listing);
    $model->update([
        'title' => $this->title,
        'description' => $this->description,
        'price' => $this->price,
    ]);

}

}; ?>

<div>
    <form wire:submit="save" class="space-y-6 max-w-lg mx-auto mt-8">
        <div>
            <label for="title" class="block font-medium">Title</label>
            <input id="title" type="text" wire:model="title" class="w-full border rounded p-2" />
            @error('title') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="description" class="block font-medium">Description</label>
            <textarea id="description" wire:model="description" class="w-full border rounded p-2"></textarea>
            @error('description') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="price" class="block font-medium">Price</label>
            <input id="price" type="number" step="0.01" wire:model="price" class="w-full border rounded p-2" />
            @error('price') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </div>
    </form>
</div>
