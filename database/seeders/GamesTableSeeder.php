<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Videojuego;
use App\Models\Genero;
use App\Models\Plataforma;
use App\Models\Multimedia;
use App\Models\Precio;
use App\Models\Reseña;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la configuración de IGDB
        $clientId = config('igdb.client_id');
        $accessToken = config('igdb.access_token');
        $baseUrl = config('igdb.base_url');

        // Realizar la petición para obtener los juegos
        $response = Http::withHeaders([
            'Client-ID' => $clientId,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post($baseUrl . 'games', [
            'fields' => 'id,name,summary,first_release_date,rating,aggregated_rating,involved_companies.company.name,genres.name,platforms.name,cover.url',
            'limit' => 50,
        ]);

        // Verificar si la respuesta es exitosa
        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                // Crear o actualizar el videojuego
                $videojuego = Videojuego::updateOrCreate(
                    ['id' => $game['id']],
                    [
                        'nombre' => $game['name'],
                        'descripcion' => $game['summary'] ?? null,
                        'fecha_lanzamiento' => isset($game['first_release_date']) ? date('Y-m-d', $game['first_release_date']) : null,
                        'rating_usuario' => $game['rating'] ?? null,
                        'rating_criticas' => $game['aggregated_rating'] ?? null,
                        'portada_url' => $game['cover']['url'] ?? null,  // Asumiendo que `cover` tiene la URL de la imagen
                    ]
                );

                // Asociar géneros al videojuego
                if (!empty($game['genres'])) {
                    foreach ($game['genres'] as $genre) {
                        $genero = Genero::firstOrCreate(['nombre' => $genre['name']]);
                        $videojuego->generos()->attach($genero->id);
                    }
                }

                // Asociar plataformas al videojuego
                if (!empty($game['platforms'])) {
                    foreach ($game['platforms'] as $platform) {
                        $plataforma = Plataforma::firstOrCreate(['nombre' => $platform['name']]);
                        $videojuego->plataformas()->attach($plataforma->id);
                    }
                }

                // Crear multimedia (imágenes o videos)
                if (isset($game['cover']['url'])) {
                    Multimedia::create([
                        'videojuego_id' => $videojuego->id,
                        'tipo' => 'imagen',  // Asumiendo que la imagen de la portada es del tipo "imagen"
                        'url' => $game['cover']['url']
                    ]);
                }

                // Crear precios para cada plataforma (en este caso, simulamos un precio aleatorio)
                if (!empty($game['platforms'])) {
                    foreach ($game['platforms'] as $platform) {
                        // Si tienes precios específicos, puedes ajustarlo aquí. Este es solo un valor simulado
                        Precio::create([
                            'videojuego_id' => $videojuego->id,
                            'plataforma_id' => Plataforma::firstOrCreate(['nombre' => $platform['name']])->id,
                            'precio' => rand(10, 60), // Precio aleatorio entre 10 y 60
                        ]);
                    }
                }

                // Crear una reseña (simulada, puedes personalizarla con datos reales)
                Reseña::create([
                    'usuario_id' => 1,  // Asegúrate de que el usuario exista
                    'videojuego_id' => $videojuego->id,
                    'texto' => '¡Este videojuego es increíble!',
                    'calificacion' => rand(1, 10) // Calificación aleatoria entre 1 y 10
                ]);
            }

            $this->command->info('Seeding completado con éxito.');
        } else {
            $this->command->error('Error al obtener datos de IGDB: ' . $response->body());
        }
    }
}
