<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

it('uploads, validates, and removes listing photos', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'photos' => [],
    ]);

    // Validation: must be image
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.photos', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('newPhotos', [UploadedFile::fake()->create('not-an-image.txt', 10)])
        ->call('savePhotos')
        ->assertHasErrors(['newPhotos.0' => 'image']);

    // Validation: max size (2MB)
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.photos', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('newPhotos', [UploadedFile::fake()->image('large.jpg')->size(3000)])
        ->call('savePhotos')
        ->assertHasErrors(['newPhotos.0' => 'max']);

    // Success: upload valid images
    $photo1 = UploadedFile::fake()->image('photo1.jpg');
    $photo2 = UploadedFile::fake()->image('photo2.png');
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.photos', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('newPhotos', [$photo1, $photo2])
        ->call('savePhotos')
        ->assertHasNoErrors();

    $listing->refresh();
    expect($listing->photos)->toHaveCount(2);
    foreach ($listing->photos as $photoPath) {
        $relativePath = str_replace('storage/', '', $photoPath);
        fwrite(STDERR, "Checking: $relativePath\n");
        Storage::disk('public')->assertExists($relativePath);
    }

    // Remove a photo
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.photos', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->call('removePhoto', 0)
        ->assertHasNoErrors();

    $listing->refresh();
    expect($listing->photos)->toHaveCount(1);
});
