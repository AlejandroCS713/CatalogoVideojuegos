<?php

namespace Tests\Feature\Livewire\Foros;

use App\Livewire\Foros\ForumIndex;
use App\Models\Foro\Foro;
use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Access\AuthorizationException;

beforeEach(function () {
    $this->userRole = Role::firstOrCreate(['name' => 'user']);
    $this->adminRole = Role::firstOrCreate(['name' => 'admin']);

    Permission::firstOrCreate(['name' => 'create foro']);
    Permission::firstOrCreate(['name' => 'update foro']);
    Permission::firstOrCreate(['name' => 'delete foro']);
    Permission::firstOrCreate(['name' => 'view foro']);

    $this->user = User::factory()->create(['email_verified_at' => null]);
    $this->user->assignRole($this->userRole);

    $this->verifiedUser = User::factory()->create(['email_verified_at' => now()]);
    $this->verifiedUser->assignRole($this->userRole);

    $this->admin = User::factory()->create(['email_verified_at' => now()]);
    $this->admin->assignRole($this->adminRole);
    $this->admin->givePermissionTo(['create foro', 'update foro', 'delete foro']);

    Livewire::actingAs($this->admin);
});

it('renders successfully with default state when no forum is selected', function () {
    Livewire::test(ForumIndex::class)
        ->assertStatus(200)
        ->assertSet('foroId', null)
        ->assertSet('currentForo', null)
        ->assertSet('modalOpen', false)
        ->assertSet('confirmingDeletion', false)
        ->assertSet('editMode', false)
        ->assertSet('selectedId', null)
        ->assertSet('foroIdToDelete', null)
        ->assertSet('titulo', '')
        ->assertSet('descripcion', '')
        ->assertSet('videojuegosConRoles', []);
});

it('renders successfully and loads a specific forum when foroId is provided', function () {
    $foro = Foro::factory()->create();
    Livewire::test(ForumIndex::class, ['foroId' => $foro->id])
        ->assertStatus(200)
        ->assertSet('foroId', $foro->id)
        ->assertSet('currentForo.id', $foro->id);
});

it('opens create modal and resets fields', function () {
    Livewire::actingAs($this->admin)
        ->test(ForumIndex::class)
        ->call('openCreateModal')
        ->assertSet('modalOpen', true)
        ->assertSet('editMode', false)
        ->assertSet('titulo', '')
        ->assertSet('descripcion', '')
        ->assertSet('videojuegosConRoles', [])
        ->assertDispatched('inicializarJuegosConRol', []);
});

it('prevents unverified users from opening create modal (expects 403)', function () {
    Livewire::actingAs($this->user)
        ->test(ForumIndex::class)
        ->call('openCreateModal')
        ->assertForbidden();
});

it('prevents non-user roles from opening create modal (expects 403)', function () {
    $noRoleUser = User::factory()->create(['email_verified_at' => now()]);
    Livewire::actingAs($noRoleUser)
        ->test(ForumIndex::class)
        ->call('openCreateModal')
        ->assertForbidden();
});

it('opens edit modal with existing forum data', function () {
    $foro = Foro::factory()->create(['titulo' => 'Old Title', 'descripcion' => 'Old Description', 'usuario_id' => $this->admin->id]);
    $videojuego1 = Videojuego::factory()->create();
    $videojuego2 = Videojuego::factory()->create();
    $foro->videojuegos()->sync([
        $videojuego1->id => ['rol_videojuego' => 'principal'],
        $videojuego2->id => ['rol_videojuego' => 'secundario']
    ]);

    Livewire::actingAs($this->admin)
        ->test(ForumIndex::class)
        ->call('openEditModal', $foro->id)
        ->assertSet('modalOpen', true)
        ->assertSet('editMode', true)
        ->assertSet('selectedId', $foro->id)
        ->assertSet('titulo', 'Old Title')
        ->assertSet('descripcion', 'Old Description')
        ->assertSet('videojuegosConRoles', [
            $videojuego1->id => 'principal',
            $videojuego2->id => 'secundario'
        ])
        ->assertDispatched('inicializarJuegosConRol', [
            $videojuego1->id => 'principal',
            $videojuego2->id => 'secundario'
        ]);
});

it('prevents unauthorized users from opening edit modal (expects 403)', function () {
    $foro = Foro::factory()->create(['usuario_id' => $this->admin->id]);
    Livewire::actingAs($this->verifiedUser)
        ->test(ForumIndex::class)
        ->call('openEditModal', $foro->id)
        ->assertForbidden();
});

it('closes modal and resets fields', function () {
    Livewire::test(ForumIndex::class)
        ->set('modalOpen', true)
        ->set('titulo', 'Some title')
        ->call('closeModal')
        ->assertSet('modalOpen', false)
        ->assertSet('titulo', '')
        ->assertSet('descripcion', '')
        ->assertSet('videojuegosConRoles', [])
        ->assertDispatched('inicializarJuegosConRol', []);
});

it('confirms deletion and sets correct forum ID', function () {
    $foro = Foro::factory()->create(['usuario_id' => $this->admin->id]);
    Livewire::actingAs($this->admin)
        ->test(ForumIndex::class)
        ->call('confirmDeleteAttempt', $foro->id)
        ->assertSet('confirmingDeletion', true)
        ->assertSet('foroIdToDelete', $foro->id)
        ->assertSet('modalOpen', false);
});

it('fails validation when creating a forum with missing fields', function () {
    Livewire::actingAs($this->verifiedUser)
        ->test(ForumIndex::class)
        ->call('openCreateModal')
        ->set('titulo', '')
        ->set('descripcion', '')
        ->call('save')
        ->assertHasErrors(['titulo', 'descripcion']);
});

it('ignores non-existent videojuegos in videojuegosConRoles when saving', function () {
    Livewire::actingAs($this->verifiedUser)
        ->test(ForumIndex::class)
        ->call('openCreateModal')
        ->set('titulo', 'Test Foro')
        ->set('descripcion', 'Test Desc')
        ->set('videojuegosConRoles', [99999 => 'principal'])
        ->call('save');

    $foro = Foro::where('titulo', 'Test Foro')->first();
    expect($foro)->not->toBeNull();
    expect($foro->videojuegos)->toHaveCount(0);
});

it('refreshData reloads current forum if still exists', function () {
    $foro = Foro::factory()->create();
    $component = Livewire::actingAs($this->admin)
        ->test(ForumIndex::class, ['foroId' => $foro->id]);

    $component->call('refreshData');
    $component->assertSet('currentForo.id', $foro->id);
});

it('refreshData redirects if forum no longer exists', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test(ForumIndex::class);

    $this->expectException(ModelNotFoundException::class);

    $component->call('openEditModal', 1);
});

it('creates a forum successfully with videojuegos and roles', function () {
    $videojuego1 = Videojuego::factory()->create();
    $videojuego2 = Videojuego::factory()->create();

    Livewire::actingAs($this->verifiedUser)
        ->test(ForumIndex::class)
        ->call('openCreateModal')
        ->set('titulo', 'Nuevo Foro')
        ->set('descripcion', 'Descripción del foro')
        ->set('videojuegosConRoles', [
            $videojuego1->id => 'principal',
            $videojuego2->id => 'secundario',
        ])
        ->call('save');

    $foro = Foro::where('titulo', 'Nuevo Foro')->first();
    expect($foro)->not->toBeNull();
    expect($foro->videojuegos()->count())->toBe(2);
});

it('updates a forum successfully and syncs videojuegos', function () {
    $foro = Foro::factory()->create(['usuario_id' => $this->admin->id]);
    $videojuego1 = Videojuego::factory()->create();
    $videojuego2 = Videojuego::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(ForumIndex::class)
        ->call('openEditModal', $foro->id)
        ->set('titulo', 'Foro Actualizado')
        ->set('descripcion', 'Descripción actualizada')
        ->set('videojuegosConRoles', [
            $videojuego1->id => 'principal',
            $videojuego2->id => 'secundario',
        ])
        ->call('save');

    $foro->refresh();
    expect($foro->titulo)->toBe('Foro Actualizado');
    expect($foro->videojuegos()->count())->toBe(2);
});

it('deletes a forum successfully when confirmed', function () {
    $foro = Foro::factory()->create(['usuario_id' => $this->admin->id]);

    Livewire::actingAs($this->admin)
        ->test(ForumIndex::class)
        ->call('confirmDeleteAttempt', $foro->id)
        ->call('deleteConfirmed');

    expect(Foro::find($foro->id))->toBeNull();
});

it('fails validation if titulo is too long', function () {
    Livewire::actingAs($this->verifiedUser)
        ->test(ForumIndex::class)
        ->call('openCreateModal')
        ->set('titulo', str_repeat('a', 256))
        ->set('descripcion', 'Desc válida')
        ->call('save')
        ->assertHasErrors(['titulo']);
});
