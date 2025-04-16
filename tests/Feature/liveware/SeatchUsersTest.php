<?php
use App\Livewire\SearchUsers;
use App\Models\users\Friend;
use App\Models\users\User;
use Livewire\Livewire;

it('shows users matching the search term', function () {
    $user = User::factory()->create();
    $target = User::factory()->create(['name' => 'Juan Pérez']);

    Livewire::actingAs($user)
        ->test(SearchUsers::class)
        ->set('searchTerm', 'Juan')
        ->call('search')
        ->assertSee('Juan Pérez');
});

it('does not search if the search term is too short', function () {
    $user = User::factory()->create();
    User::factory()->create(['name' => 'Pedro']);

    Livewire::actingAs($user)
        ->test(SearchUsers::class)
        ->set('searchTerm', 'P')
        ->call('search')
        ->assertDontSee('Pedro');
});

it('shows message when no users are found', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(SearchUsers::class)
        ->set('searchTerm', 'NoExiste')
        ->call('search')
        ->assertSee('No se encontraron usuarios');
});

it('sends a friend request if not already sent', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    Livewire::actingAs($user)
        ->test(SearchUsers::class)
        ->call('sendFriendRequest', $target->id);

    expect(Friend::where('user_id', $user->id)->where('friend_id', $target->id)->first())->not()->toBeNull();
});


