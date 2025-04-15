<?php

use App\Livewire\Videojuegos\ManageGameAdminComponent;
use App\Models\users\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\games\Videojuego;
use App\Models\games\Plataforma;
use App\Models\games\Genero;
use App\Models\games\Multimedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function createAdminUserWithPermissions(array $permissions = [
    'Crear Videojuegos',
    'Actualizar Videojuegos',
    'Eliminar Videojuegos'
]): array {
    $createdPermissionIds = [];
    foreach ($permissions as $permission) {
        $p = Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $createdPermissionIds[] = $p->id;
    }

    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $role->givePermissionTo($permissions);

    $user = User::factory()->create();
    $user->assignRole($role);

    return ['user' => $user, 'role' => $role, 'permissionIds' => $createdPermissionIds];
}

function cleanupTestData(array $data): void
{
    if (!empty($data['filePaths'])) {
        foreach ($data['filePaths'] as $path) {
            Storage::disk('public')->delete($path);
        }
    }
    if (!empty($data['mediaIds'])) {
        Multimedia::whereIn('id', $data['mediaIds'])->delete();
    }
    if (!empty($data['gameIds'])) {
        Videojuego::whereIn('id', $data['gameIds'])->delete();
    }
    if (!empty($data['plataformaIds'])) {
        Plataforma::whereIn('id', $data['plataformaIds'])->delete();
    }
    if (!empty($data['generoIds'])) {
        Genero::whereIn('id', $data['generoIds'])->delete();
    }
    if (!empty($data['adminData'])) {
        $adminData = $data['adminData'];
        if (isset($adminData['user'])) {
            $adminData['user']->removeRole($adminData['role']->name);
            $adminData['user']->delete();
        }
        if (isset($adminData['role'])) {
            $adminData['role']->permissions()->detach();
        }
        if (isset($adminData['permissionIds'])) {
            Permission::whereIn('id', $adminData['permissionIds'])->delete();
        }
    }
}

describe('Manage Game Admin Component', function () {

    it('renders successfully', function () {
        Livewire::test(ManageGameAdminComponent::class)
            ->assertStatus(200);
    });

    it('opens create modal on event and loads data', function () {
        Storage::fake('public');
        $plataformas = Plataforma::factory(3)->create();
        $generos = Genero::factory(3)->create();
        $cleanupData = [
            'plataformaIds' => $plataformas->pluck('id')->toArray(),
            'generoIds' => $generos->pluck('id')->toArray()
        ];

        $component = Livewire::test(ManageGameAdminComponent::class)
            ->assertSet('modalOpen', false)
            ->assertSet('editMode', false)
            ->dispatch('openCreateModalEvent')
            ->assertSet('modalOpen', true)
            ->assertSet('editMode', false);

        $this->assertNotEmpty($component->get('allPlataformas'));
        $this->assertNotEmpty($component->get('allGeneros'));

        cleanupTestData($cleanupData);
    });

    it('opens edit modal on event and loads game data', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $plataforma = Plataforma::factory()->create();
        $genero = Genero::factory()->create();
        $videojuego = Videojuego::factory()
            ->hasAttached($plataforma)
            ->hasAttached($genero)
            ->create(['nombre' => 'Game To Edit']);
        $imagePath = UploadedFile::fake()->image('test.jpg')->store('videojuegos', 'public');
        $multimedia = Multimedia::factory()->create([
            'videojuego_id' => $videojuego->id,
            'tipo' => 'imagen',
            'url' => 'storage/' . $imagePath
        ]);
        $cleanupData = [
            'adminData' => $adminData,
            'plataformaIds' => [$plataforma->id],
            'generoIds' => [$genero->id],
            'gameIds' => [$videojuego->id],
            'mediaIds' => [$multimedia->id],
            'filePaths' => [$imagePath]
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->assertSet('modalOpen', false)
            ->dispatch('openEditModalEvent', id: $videojuego->id)
            ->assertSet('modalOpen', true)
            ->assertSet('editMode', true)
            ->assertSet('selectedId', $videojuego->id)
            ->assertSet('nombre', 'Game To Edit')
            ->assertSet('plataformas', [$plataforma->id])
            ->assertSet('generos', [$genero->id])
            ->assertSet('existingImageUrl', 'storage/' . $imagePath);

        cleanupTestData($cleanupData);
    });

    it('opens delete confirmation modal on event', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $videojuego = Videojuego::factory()->create();
        $cleanupData = [
            'adminData' => $adminData,
            'gameIds' => [$videojuego->id]
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->assertSet('confirmingDeletion', false)
            ->dispatch('confirmDeleteEvent', id: $videojuego->id)
            ->assertSet('confirmingDeletion', true)
            ->assertSet('gameIdToDelete', $videojuego->id)
            ->assertSet('modalOpen', false);

        cleanupTestData($cleanupData);
    });

    it('validates that name is required on save', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openCreateModalEvent')
            ->set('nombre', '')
            ->call('save')
            ->assertHasErrors(['nombre' => 'required']);

        cleanupTestData(['adminData' => $adminData]);
    });

    it('validates that image must be an image file', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $notAnImage = UploadedFile::fake()->create('document.pdf', 100);

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openCreateModalEvent')
            ->set('nombre', 'Valid Game Name')
            ->set('imagen', $notAnImage)
            ->call('save')
            ->assertHasErrors(['imagen' => 'image']);

        cleanupTestData(['adminData' => $adminData]);
    });

    it('validates that image must be below max size', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $tooLargeImage = UploadedFile::fake()->image('large.jpg')->size(3000);

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openCreateModalEvent')
            ->set('nombre', 'Valid Game Name')
            ->set('imagen', $tooLargeImage)
            ->call('save')
            ->assertHasErrors(['imagen' => 'max']);

        cleanupTestData(['adminData' => $adminData]);
    });

    it('allows admin to create a game with an image', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $fakeImage = UploadedFile::fake()->image('cover.jpg');
        $plataforma = Plataforma::factory()->create();
        $genero = Genero::factory()->create();
        $cleanupData = [
            'adminData' => $adminData,
            'plataformaIds' => [$plataforma->id],
            'generoIds' => [$genero->id],
            'gameIds' => [],
            'mediaIds' => [],
            'filePaths' => [],
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openCreateModalEvent')
            ->set('nombre', 'New Game With Image')
            ->set('plataformas', [$plataforma->id])
            ->set('generos', [$genero->id])
            ->set('imagen', $fakeImage)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('modalOpen', false)
            ->assertDispatched('gameSaved');

        $game = Videojuego::where('nombre', 'New Game With Image')->first();
        $cleanupData['gameIds'][] = $game->id;
        $this->assertDatabaseHas('multimedia', ['videojuego_id' => $game->id]);
        $media = Multimedia::where('videojuego_id', $game->id)->first();
        $cleanupData['mediaIds'][] = $media->id;
        $path = str_replace('storage/', '', $media->url);
        $cleanupData['filePaths'][] = $path;
        Storage::disk('public')->assertExists($path);

        cleanupTestData($cleanupData);
    });

    it('allows admin to update a game data', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $plataforma1 = Plataforma::factory()->create();
        $plataforma2 = Plataforma::factory()->create();
        $genero1 = Genero::factory()->create();
        $genero2 = Genero::factory()->create();
        $videojuego = Videojuego::factory()
            ->hasAttached($plataforma1)
            ->hasAttached($genero1)
            ->create(['nombre' => 'Game To Update']);
        $cleanupData = [
            'adminData' => $adminData,
            'plataformaIds' => [$plataforma1->id, $plataforma2->id],
            'generoIds' => [$genero1->id, $genero2->id],
            'gameIds' => [$videojuego->id]
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openEditModalEvent', id: $videojuego->id)
            ->set('nombre', 'Updated Game Name')
            ->set('descripcion', 'Updated Description')
            ->set('plataformas', [$plataforma2->id])
            ->set('generos', [$genero2->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('modalOpen', false)
            ->assertDispatched('gameSaved');

        $videojuego->refresh();
        expect($videojuego->nombre)->toBe('Updated Game Name');
        expect($videojuego->descripcion)->toBe('Updated Description');
        expect($videojuego->plataformas->pluck('id')->toArray())->toEqual([$plataforma2->id]);
        expect($videojuego->generos->pluck('id')->toArray())->toEqual([$genero2->id]);
        $this->assertDatabaseHas('videojuego_plataforma', ['videojuego_id' => $videojuego->id, 'plataforma_id' => $plataforma2->id]);
        $this->assertDatabaseMissing('videojuego_plataforma', ['videojuego_id' => $videojuego->id, 'plataforma_id' => $plataforma1->id]);

        cleanupTestData($cleanupData);
    });

    it('allows admin to update a game image', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $oldImagePath = UploadedFile::fake()->image('old_cover.jpg')->store('videojuegos', 'public');
        $videojuego = Videojuego::factory()->create(['nombre' => 'Game Image Update']);
        $oldMultimedia = Multimedia::factory()->create([
            'videojuego_id' => $videojuego->id,
            'tipo' => 'imagen',
            'url' => 'storage/' . $oldImagePath
        ]);
        $newFakeImage = UploadedFile::fake()->image('new_cover.png');
        $cleanupData = [
            'adminData' => $adminData,
            'gameIds' => [$videojuego->id],
            'mediaIds' => [],
            'filePaths' => []
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openEditModalEvent', id: $videojuego->id)
            ->assertSet('existingImageUrl', 'storage/' . $oldImagePath)
            ->set('imagen', $newFakeImage)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('gameSaved');

        $this->assertDatabaseMissing('multimedia', ['id' => $oldMultimedia->id]);
        Storage::disk('public')->assertMissing($oldImagePath);
        $this->assertDatabaseHas('multimedia', ['videojuego_id' => $videojuego->id]);
        $newMultimedia = Multimedia::where('videojuego_id', $videojuego->id)->where('tipo','imagen')->first();
        $cleanupData['mediaIds'][] = $newMultimedia->id;
        expect($newMultimedia->url)->not->toBe('storage/' . $oldImagePath);
        $newPath = str_replace('storage/', '', $newMultimedia->url);
        $cleanupData['filePaths'][] = $newPath;
        Storage::disk('public')->assertExists($newPath);

        cleanupTestData($cleanupData);
    });

    it('allows admin to delete a game', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $imagePath = UploadedFile::fake()->image('delete_me.jpg')->store('videojuegos', 'public');
        $plataforma = Plataforma::factory()->create();
        $genero = Genero::factory()->create();
        $videojuego = Videojuego::factory()
            ->hasAttached($plataforma)
            ->hasAttached($genero, [], 'generos')
            ->create(['nombre' => 'Game To Delete']);
        $multimedia = Multimedia::factory()->create([
            'videojuego_id' => $videojuego->id,
            'tipo' => 'imagen',
            'url' => 'storage/' . $imagePath
        ]);

        $gameIdToDelete = $videojuego->id;
        $mediaIdToDelete = $multimedia->id;
        $plataformaId = $plataforma->id;
        $generoId = $genero->id;

        $cleanupData = [
            'adminData' => $adminData,
            'plataformaIds' => [$plataforma->id],
            'generoIds' => [$genero->id],
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('confirmDeleteEvent', id: $videojuego->id)
            ->assertSet('confirmingDeletion', true)
            ->call('deleteConfirmed')
            ->assertHasNoErrors()
            ->assertSet('confirmingDeletion', false)
            ->assertDispatched('gameDeleted');

        $this->assertDatabaseMissing('videojuegos', ['id' => $gameIdToDelete]);
        $this->assertDatabaseMissing('multimedia', ['id' => $mediaIdToDelete]);
        $this->assertDatabaseMissing('videojuego_plataforma', ['videojuego_id' => $gameIdToDelete, 'plataforma_id' => $plataformaId]);
        $this->assertDatabaseMissing('videojuego_genero', ['videojuego_id' => $gameIdToDelete, 'genero_id' => $generoId]);
        Storage::disk('public')->assertMissing($imagePath);

        cleanupTestData($cleanupData);
    });

    it('closes confirmation modal on cancel delete', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('confirmDeleteEvent', id: 1)
            ->assertSet('confirmingDeletion', true)
            ->call('cancelDelete')
            ->assertSet('confirmingDeletion', false)
            ->assertSet('gameIdToDelete', null);

        cleanupTestData(['adminData' => $adminData]);
    });
});
