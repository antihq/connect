<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('uploads valid images', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create();

    $photo1 = UploadedFile::fake()->image('photo1.jpg');
    $photo2 = UploadedFile::fake()->image('photo2.png');
    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.photos', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('newPhotos', [$photo1, $photo2])
        ->call('savePhotos')
        ->assertHasNoErrors();

    $listing->refresh();
    expect($listing->photos)->toHaveCount(2);
    $listing->photos->each(function ($photo) {
        Storage::disk('public')->assertExists($photo->path);
    });
});

it('removes a photo after upload', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create();

    $photo1 = UploadedFile::fake()->image('photo1.jpg');
    $photo2 = UploadedFile::fake()->image('photo2.png');
    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.photos', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('newPhotos', [$photo1, $photo2])
        ->call('savePhotos')
        ->assertHasNoErrors();

    $listing->refresh();
    expect($listing->photos)->toHaveCount(2);

    $photoId = $listing->photos[0]->id;
    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.photos', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->call('removePhoto', $photoId)
        ->assertHasNoErrors();

    $listing->refresh();
    expect($listing->photos)->toHaveCount(1);
});

it('fails if uploaded file is not an image', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create();

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.photos', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('newPhotos', [UploadedFile::fake()->create('not-an-image.txt', 10)])
        ->call('savePhotos')
        ->assertHasErrors(['newPhotos.0' => 'image']);
});

it('fails if uploaded image exceeds max size', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create();

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.photos', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('newPhotos', [UploadedFile::fake()->image('large.jpg')->size(3000)])
        ->call('savePhotos')
        ->assertHasErrors(['newPhotos.0' => 'max']);
});
