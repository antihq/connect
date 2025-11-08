<?php

use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

describe('Voting', function () {
    it('creates a vote for a new email', function () {
        $email = 'test1@example.com';
        $option = 3;

        Volt::test('vote')
            ->set('email', $email)
            ->set('option', $option)
            ->call('vote');

        $this->assertDatabaseHas('votes', [
            'email' => $email,
            'option' => $option,
        ]);
    });

    it('updates the vote option for an existing email', function () {
        $email = 'test2@example.com';
        $initialOption = 2;
        $updatedOption = 7;

        Vote::create([
            'email' => $email,
            'option' => $initialOption,
        ]);

        Volt::test('vote')
            ->set('email', $email)
            ->set('option', $updatedOption)
            ->call('vote');

        $this->assertDatabaseHas('votes', [
            'email' => $email,
            'option' => $updatedOption,
        ]);
    });
});
