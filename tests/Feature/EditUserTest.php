<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('shows the edit form with current user data', function () {
    $user = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'bio' => 'Hello world!',
    ]);

    Volt::actingAs($user)
        ->test('backstage.users.edit', ['user' => $user])
        ->assertSet('first_name', 'Jane')
        ->assertSet('last_name', 'Doe')
        ->assertSet('email', 'jane@example.com')
        ->assertSet('bio', 'Hello world!');
});

it('updates the user with valid data', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('backstage.users.edit', ['user' => $user])
        ->set('first_name', 'John')
        ->set('last_name', 'Smith')
        ->set('email', 'john.smith@example.com')
        ->set('bio', 'Updated bio')
        ->call('save')
        ->assertRedirect(route('backstage.users.show', $user));

    $user->refresh();
    expect($user->first_name)->toBe('John');
    expect($user->last_name)->toBe('Smith');
    expect($user->email)->toBe('john.smith@example.com');
    expect($user->bio)->toBe('Updated bio');
});

it('shows validation errors for invalid data', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('backstage.users.edit', ['user' => $user])
        ->set('first_name', '')
        ->set('last_name', '')
        ->set('email', 'not-an-email')
        ->set('bio', str_repeat('a', 1001))
        ->call('save')
        ->assertHasErrors(['first_name', 'last_name', 'email', 'bio']);
});
