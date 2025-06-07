<?php

use App\Livewire\Videojuegos\ManageGameAdminComponent;
use App\Livewire\Videojuegos\VideoGamesViewComponent;
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

beforeEach(function () {
    Mockery::close();
});
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

    it('resets all fields when resetFields is called', function () {
        $component = Livewire::test(ManageGameAdminComponent::class)
            ->set('nombre', 'Some Name')
            ->set('descripcion', 'Some Description')
            ->set('selectedId', 1)
            ->set('editMode', true)
            ->set('confirmingDeletion', true)
            ->set('gameIdToDelete', 1);

        $component->call('resetFields')
            ->assertSet('nombre', null)
            ->assertSet('descripcion', null)
            ->assertSet('selectedId', null)
            ->Set('editMode', false)
            ->assertSet('confirmingDeletion', false)
            ->assertSet('gameIdToDelete', null);
    });

    it('deletes existing image from storage if not http and exists', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $oldImagePath = 'videojuegos/old_cover_to_delete.jpg';
        Storage::disk('public')->put($oldImagePath, 'dummy content');
        $videojuego = Videojuego::factory()->create();
        $oldMultimedia = Multimedia::factory()->create([
            'videojuego_id' => $videojuego->id,
            'tipo' => 'imagen',
            'url' => 'storage/' . $oldImagePath
        ]);
        $newFakeImage = UploadedFile::fake()->image('new_cover.png');
        $cleanupData = [
            'adminData' => $adminData,
            'gameIds' => [$videojuego->id],
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openEditModalEvent', id: $videojuego->id)
            ->set('imagen', $newFakeImage)
            ->call('save')
            ->assertHasNoErrors();

        Storage::disk('public')->assertMissing($oldImagePath);
        cleanupTestData($cleanupData);
    });

    it('does not attempt to delete non-existent local image from storage on delete confirmed', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $imagePath = 'videojuegos/non_existent_local_image.jpg';
        $videojuego = Videojuego::factory()->create(['nombre' => 'Game To Delete Non-Existent Local Image']);
        $multimedia = Multimedia::factory()->create([
            'videojuego_id' => $videojuego->id,
            'tipo' => 'imagen',
            'url' => 'storage/' . $imagePath
        ]);
        $cleanupData = [
            'adminData' => $adminData,
        ];

        Storage::shouldReceive('disk->exists')->with($imagePath)->andReturn(false);
        Storage::shouldNotReceive('disk->delete')->with($imagePath);

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('confirmDeleteEvent', id: $videojuego->id)
            ->call('deleteConfirmed')
            ->assertDispatched('gameDeleted');

        $this->assertDatabaseMissing('videojuegos', ['id' => $videojuego->id]);
        $this->assertDatabaseMissing('multimedia', ['id' => $multimedia->id]);
        cleanupTestData($cleanupData);
    });

    it('allows saving without image when not in edit mode and image is null', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $plataforma = Plataforma::factory()->create();
        $genero = Genero::factory()->create();
        $cleanupData = [
            'adminData' => $adminData,
            'plataformaIds' => [$plataforma->id],
            'generoIds' => [$genero->id],
            'gameIds' => [],
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openCreateModalEvent')
            ->set('nombre', 'Game No Image')
            ->set('plataformas', [$plataforma->id])
            ->set('generos', [$genero->id])
            ->set('imagen', null)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('modalOpen', false)
            ->assertDispatched('gameSaved');

        $game = Videojuego::where('nombre', 'Game No Image')->first();
        $cleanupData['gameIds'][] = $game->id;
        $this->assertDatabaseMissing('multimedia', ['videojuego_id' => $game->id, 'tipo' => 'imagen']);

        cleanupTestData($cleanupData);
    });

    it('allows saving without new image when in edit mode and image is null', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $videojuego = Videojuego::factory()->create(['nombre' => 'Game Edit No New Image']);
        $cleanupData = [
            'adminData' => $adminData,
            'gameIds' => [$videojuego->id],
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openEditModalEvent', id: $videojuego->id)
            ->set('nombre', 'Game Edit No New Image Updated')
            ->set('imagen', null)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('modalOpen', false)
            ->assertDispatched('gameSaved');

        $videojuego->refresh();
        expect($videojuego->nombre)->toBe('Game Edit No New Image Updated');
        $this->assertDatabaseMissing('multimedia', ['videojuego_id' => $videojuego->id, 'tipo' => 'imagen']);

        cleanupTestData($cleanupData);
    });

    it('resets all fields including collections when resetFields is called', function () {
        $plataforma = Plataforma::factory()->create();
        $genero = Genero::factory()->create();
        $component = Livewire::test(ManageGameAdminComponent::class)
            ->set('nombre', 'Test Game')
            ->set('descripcion', 'Test Desc')
            ->set('fecha_lanzamiento', '2023-01-01')
            ->set('desarrollador', 'Dev')
            ->set('publicador', 'Pub')
            ->set('plataformas', [$plataforma->id])
            ->set('generos', [$genero->id])
            ->set('imagen', UploadedFile::fake()->image('test.jpg'))
            ->set('selectedId', 1)
            ->set('editMode', true)
            ->set('existingImageUrl', 'http://example.com/image.jpg')
            ->set('confirmingDeletion', true)
            ->set('gameIdToDelete', 10);

        $component->call('resetFields')
            ->assertSet('nombre', null)
            ->assertSet('descripcion', null)
            ->assertSet('fecha_lanzamiento', null)
            ->assertSet('desarrollador', null)
            ->assertSet('publicador', null)
            ->assertSet('plataformas', [])
            ->assertSet('generos', [])
            ->assertSet('imagen', null)
            ->assertSet('selectedId', null)
            ->assertSet('editMode', false)
            ->assertSet('existingImageUrl', null)
            ->assertSet('confirmingDeletion', false)
            ->assertSet('gameIdToDelete', null);
    });

    it('sets modalOpen to false when closeModal is called', function () {
        $component = Livewire::test(ManageGameAdminComponent::class)
            ->set('modalOpen', true)
            ->call('closeModal')
            ->assertSet('modalOpen', false);
    });

    it('resets fields when closeModal is called', function () {
        $plataforma = Plataforma::factory()->create();
        $component = Livewire::test(ManageGameAdminComponent::class)
            ->set('nombre', 'Filled Name')
            ->set('plataformas', [$plataforma->id])
            ->set('editMode', true)
            ->call('closeModal')
            ->assertSet('nombre', null)
            ->assertSet('plataformas', [])
            ->assertSet('editMode', false);
    });

    it('does not delete multimedia if image is null during update', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $videojuego = Videojuego::factory()->create(['nombre' => 'Game Without Image']);
        $cleanupData = [
            'adminData' => $adminData,
            'gameIds' => [$videojuego->id],
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openEditModalEvent', id: $videojuego->id)
            ->set('imagen', null)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('multimedia', ['videojuego_id' => $videojuego->id]);
        cleanupTestData($cleanupData);
    });

    it('handles saving a game without platforms or genres', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $cleanupData = [
            'adminData' => $adminData,
            'gameIds' => [],
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openCreateModalEvent')
            ->set('nombre', 'Game No Relations')
            ->set('plataformas', [])
            ->set('generos', [])
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('modalOpen', false)
            ->assertDispatched('gameSaved');

        $game = Videojuego::where('nombre', 'Game No Relations')->first();
        $cleanupData['gameIds'][] = $game->id;
        $this->assertDatabaseMissing('videojuego_plataforma', ['videojuego_id' => $game->id]);
        $this->assertDatabaseMissing('videojuego_genero', ['videojuego_id' => $game->id]);

        cleanupTestData($cleanupData);
    });

    it('ensures loadModalData populates allPlataformas and allGeneros only once', function () {
        Plataforma::factory(1)->create();
        Genero::factory(1)->create();

        $component = Livewire::test(ManageGameAdminComponent::class);
        $component->call('loadModalData');

        $this->assertNotEmpty($component->get('allPlataformas'));
        $this->assertNotEmpty($component->get('allGeneros'));

        $component->set('allPlataformas', []);
        $component->set('allGeneros', []);
        $component->call('loadModalData');

        $this->assertNotEmpty($component->get('allPlataformas'));

    });

    it('validates imagen with mimes rule on updatedImagen', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $notAValidMimeType = UploadedFile::fake()->create('document.txt', 100);

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->set('imagen', $notAValidMimeType)
            ->assertHasErrors(['imagen' => 'mimes']);

        cleanupTestData(['adminData' => $adminData]);
    });

    it('validates imagen with image rule on updatedImagen', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $notAnImageFile = UploadedFile::fake()->create('archive.zip', 100);

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->set('imagen', $notAnImageFile)
            ->assertHasErrors(['imagen' => 'image']);

        cleanupTestData(['adminData' => $adminData]);
    });

    it('validates imagen with max size rule on updatedImagen', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $tooLargeImage = UploadedFile::fake()->image('large.jpg')->size(3000);

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->set('imagen', $tooLargeImage)
            ->assertHasErrors(['imagen' => 'max']);

        cleanupTestData(['adminData' => $adminData]);
    });

    it('does not delete image from storage if existingImageUrl is http-based', function () {
        Storage::fake('public');
        $adminData = createAdminUserWithPermissions();
        $videojuego = Videojuego::factory()->create();
        $externalMultimedia = Multimedia::factory()->create([
            'videojuego_id' => $videojuego->id,
            'tipo' => 'imagen',
            'url' => 'http://example.com/external/image.jpg',
        ]);
        $newFakeImage = UploadedFile::fake()->image('new.jpg');
        $cleanupData = [
            'adminData' => $adminData,
            'gameIds' => [$videojuego->id],
        ];

        Livewire::actingAs($adminData['user'])
            ->test(ManageGameAdminComponent::class)
            ->dispatch('openEditModalEvent', id: $videojuego->id)
            ->set('imagen', $newFakeImage)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('multimedia', ['id' => $externalMultimedia->id]);

        $newMultimedia = Multimedia::where('videojuego_id', $videojuego->id)->where('tipo', 'imagen')->first();
        if ($newMultimedia) {
            $cleanupData['mediaIds'][] = $newMultimedia->id;
            $cleanupData['filePaths'][] = str_replace('storage/', '', $newMultimedia->url);
        }
        cleanupTestData($cleanupData);
    });

    afterEach(function() {
        Mockery::close();
    });
});
describe('VideoGamesViewComponent', function () {

    it('renders successfully with default state when no game is selected', function () {
        Videojuego::factory(5)->create();
        Livewire::test(VideoGamesViewComponent::class)
            ->assertStatus(200)
            ->assertSet('videojuegoId', null)
            ->assertSet('currentGame', null)
            ->assertSet('sort', 'newest')
            ->assertSet('page', 1)
            ->assertViewHas('videojuegos', function ($videojuegos) {
                return $videojuegos->count() > 0 && $videojuegos->perPage() === 30;
            });
    });

    it('renders successfully and loads a specific game when videojuegoId is provided', function () {
        $videojuego = Videojuego::factory()->create();
        Livewire::test(VideoGamesViewComponent::class, ['videojuegoId' => $videojuego->id])
            ->assertStatus(200)
            ->assertSet('videojuegoId', $videojuego->id)
            ->assertSet('currentGame.id', $videojuego->id)
            ->assertViewHas('currentGame', function ($game) use ($videojuego) {
                return $game->id === $videojuego->id;
            })
            ->assertViewHas('videojuegos', null);
    });

    it('aborts with 404 if provided videojuegoId does not exist', function () {
        Livewire::test(VideoGamesViewComponent::class, ['videojuegoId' => 99999])
            ->assertStatus(404);
    });


    it('applies "oldest" sort order', function () {
        $oldestGame = Videojuego::factory()->create(['created_at' => now()->subDays(5)]);
        $newestGame = Videojuego::factory()->create(['created_at' => now()]);

        Livewire::test(VideoGamesViewComponent::class)
            ->set('sort', 'oldest')
            ->assertSeeInOrder([$oldestGame->nombre, $newestGame->nombre]);
    });

    it('applies "alphabetical" sort order', function () {
        $gameB = Videojuego::factory()->create(['nombre' => 'Beta Game']);
        $gameA = Videojuego::factory()->create(['nombre' => 'Alpha Game']);

        Livewire::test(VideoGamesViewComponent::class)
            ->set('sort', 'alphabetical')
            ->assertSeeInOrder([$gameA->nombre, $gameB->nombre]);
    });

    it('applies "reverse_alphabetical" sort order', function () {
        $gameB = Videojuego::factory()->create(['nombre' => 'Beta Game']);
        $gameA = Videojuego::factory()->create(['nombre' => 'Alpha Game']);

        Livewire::test(VideoGamesViewComponent::class)
            ->set('sort', 'reverse_alphabetical')
            ->assertSeeInOrder([$gameB->nombre, $gameA->nombre]);
    });

    it('applies "top_rated_aaa" sort order', function () {
        $game1 = Videojuego::factory()->create(['nombre' => 'AAA Game 1']);
        $game2 = Videojuego::factory()->create(['nombre' => 'AAA Game 2']);
        Livewire::test(VideoGamesViewComponent::class)
            ->set('sort', 'top_rated_aaa')
            ->assertStatus(200);
    });

    it('applies "exclusive_games" sort order', function () {
        $game1 = Videojuego::factory()->create(['nombre' => 'Exclusive Game 1']);
        $game2 = Videojuego::factory()->create(['nombre' => 'Exclusive Game 2']);
        Livewire::test(VideoGamesViewComponent::class)
            ->set('sort', 'exclusive_games')
            ->assertStatus(200);
    });


    it('refreshes current game data on "gameSaved" event when a game is selected', function () {
        $videojuego = Videojuego::factory()->create(['nombre' => 'Original Name']);
        $component = Livewire::test(VideoGamesViewComponent::class, ['videojuegoId' => $videojuego->id])
            ->assertSet('currentGame.nombre', 'Original Name');

        $videojuego->update(['nombre' => 'Updated Name']);

        $component->dispatch('gameSaved')
            ->assertSet('currentGame.nombre', 'Updated Name');
    });


    it('does not redirect or show flash message on "gameDeleted" if no specific game is selected', function () {
        Videojuego::factory(5)->create();
        $component = Livewire::test(VideoGamesViewComponent::class)
            ->assertSet('videojuegoId', null);

        $component->dispatch('gameDeleted')
            ->assertSessionMissing('message')
            ->assertNoRedirect();
    });

    it('loads game with all relationships for single view', function () {
        $videojuego = Videojuego::factory()->create();
        Multimedia::factory()->create(['videojuego_id' => $videojuego->id, 'tipo' => 'imagen']);
        Plataforma::factory(2)->create()->each(function ($plataforma) use ($videojuego) {
            $videojuego->plataformas()->attach($plataforma);
        });
        Genero::factory(2)->create()->each(function ($genero) use ($videojuego) {
            $videojuego->generos()->attach($genero);
        });

        Livewire::test(VideoGamesViewComponent::class, ['videojuegoId' => $videojuego->id])
            ->assertSet('currentGame.id', $videojuego->id)
            ->assertViewHas('currentGame', function ($game) {
                return $game->relationLoaded('multimedia') &&
                    $game->relationLoaded('generos') &&
                    $game->relationLoaded('plataformas') &&
                    $game->relationLoaded('precios');
            });
    });

    it('loads paginated games with multimedia relationship for index view', function () {
        Videojuego::factory(2)->create()->each(function ($game) {
            Multimedia::factory()->create(['videojuego_id' => $game->id, 'tipo' => 'imagen']);
        });

        Livewire::test(VideoGamesViewComponent::class)
            ->assertViewHas('videojuegos', function ($paginatedGames) {
                foreach ($paginatedGames as $game) {
                    if (!$game->relationLoaded('multimedia')) {
                        return false;
                    }
                }
                return true;
            });
    });

    it('applies "newest" sort order by default', function () {
        $oldestGame = Videojuego::factory()->create(['nombre' => 'Z-Game Oldest', 'fecha_lanzamiento' => '2020-01-01']);
        $middleGame = Videojuego::factory()->create(['nombre' => 'M-Game Middle', 'fecha_lanzamiento' => '2022-06-01']);
        $newestGame = Videojuego::factory()->create(['nombre' => 'A-Game Newest', 'fecha_lanzamiento' => '2024-05-15']);

        Livewire::test(VideoGamesViewComponent::class)
            ->assertSeeInOrder([
                $newestGame->nombre,
                $middleGame->nombre,
                $oldestGame->nombre,
            ]);
    });
});
