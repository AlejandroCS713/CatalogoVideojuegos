<?php
namespace Database\Seeders;

use App\Models\games\Genero;
use App\Models\games\Multimedia;
use App\Models\games\Plataforma;
use App\Models\games\Precio;
use App\Models\games\Videojuego;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class GamesTableSeeder extends Seeder
{
    public function run()
    {
        // Establecer el offset a 0, ya que solo vamos a hacer una única llamada
        $offset = 0;

        // Realizar la consulta a la API de IGDB
        $response = Http::withHeaders([
            'Client-ID' => 'c5ew8x39828zbf5ccvewq5qsifet9p',
            'Authorization' => 'Bearer ahyr96watr0hezc0vzhrn0owothq99',
            'Accept' => 'application/json',
        ])
            ->withBody("fields id,name,summary,first_release_date,rating,aggregated_rating,involved_companies.company.name,genres.id,genres.name,platforms.id,platforms.name,cover.url; limit 500; offset $offset;", 'application/x-www-form-urlencoded')
            ->post('https://api.igdb.com/v4/games');

        if ($response->failed()) {
            $this->command->error('Error al realizar la solicitud a la API.');
            $this->command->error('Código de respuesta: ' . $response->status()); // Para ver el código HTTP
            $this->command->error('Detalles del error: ' . $response->body());
            return;
        }

        $games = $response->json(); // Convertir la respuesta a un array

        // Si no hay juegos, salir del método
        if (empty($games)) {
            $this->command->info('No hay juegos disponibles.');
            return;
        }

        // Procesar cada juego recibido
        foreach ($games as $game) {
            // Validar datos esenciales
            if (!isset($game['id']) || !isset($game['name'])) {
                $this->command->warn('Saltando un juego por falta de datos obligatorios.');
                continue;
            }

            $desarrollador = null;
            $publicador = null;

            // Procesar las compañías involucradas
            if (isset($game['involved_companies'])) {
                foreach ($game['involved_companies'] as $involvedCompany) {
                    $companyName = $involvedCompany['company']['name'] ?? null;

                    if ($desarrollador === null) {
                        $desarrollador = $companyName;
                    } elseif ($publicador === null) {
                        $publicador = $companyName;
                        break; // No necesitamos más compañías
                    }
                }
            }

            // Crear o actualizar el videojuego
            $videojuego = Videojuego::updateOrCreate(
                ['id' => $game['id']],
                [
                    'nombre' => $game['name'] ?? 'Nombre desconocido',
                    'descripcion' => $game['summary'] ?? 'Descripción no disponible',
                    'fecha_lanzamiento' => isset($game['first_release_date']) ? date('Y-m-d', $game['first_release_date']) : null,
                    'rating_usuario' => $game['rating'] ?? 0,
                    'rating_criticas' => $game['aggregated_rating'] ?? 0,
                    'desarrollador' => $desarrollador ?? 'Desarrollador desconocido',
                    'publicador' => $publicador ?? 'Publicador desconocido',
                ]
            );

            // Procesar plataformas
            if (isset($game['platforms'])) {
                foreach ($game['platforms'] as $platform) {
                    $plataforma = Plataforma::updateOrCreate(
                        ['id' => $platform['id']],
                        ['nombre' => $platform['name']]
                    );
                    $videojuego->plataformas()->syncWithoutDetaching($plataforma->id);
                }
            }

            // Procesar géneros
            if (isset($game['genres'])) {
                foreach ($game['genres'] as $genre) {
                    $genero = Genero::updateOrCreate(
                        ['id' => $genre['id']],
                        ['nombre' => $genre['name']]
                    );
                    $videojuego->generos()->syncWithoutDetaching($genero->id);
                }
            }

            // Procesar portada (imagen)
            if (isset($game['cover']['url'])) {
                // URL base de la portada
                $imageUrl = 'https:' . $game['cover']['url'];

                // Intentar obtener la mejor calidad posible
                // Primero, buscamos si hay una versión de alta calidad (por ejemplo, 'cover_big', '1080p')
                if (strpos($imageUrl, 'thumb') !== false) {
                    // Si la URL tiene 'thumb', reemplazamos con una resolución más alta como 'cover_big' o '1080p'
                    $imageUrl = str_replace('thumb', 'cover_big', $imageUrl);  // 'cover_big' es la opción de mayor calidad disponible
                }

                // Si no se puede encontrar 'thumb', pero está disponible 'cover_medium', usamos eso
                if (strpos($imageUrl, 'cover_big') !== false && strpos($imageUrl, 'thumb') === false) {
                    $imageUrl = str_replace('cover_medium', 'cover_big', $imageUrl); // Si 'cover_big' está disponible, usaremos eso.
                }

                // Si la URL sigue siendo pequeña o con calidad baja, consideramos usar una opción de mayor resolución como '720p' o '1080p'
                if (strpos($imageUrl, '720p') === false && strpos($imageUrl, '1080p') === false) {
                    $imageUrl = str_replace('thumb', '1080p', $imageUrl); // Cambiar a la versión más alta disponible (por ejemplo, 1080p)
                }

                // Verificar que la URL contiene el tamaño adecuado
                if (strpos($imageUrl, 'cover_big') === false && strpos($imageUrl, '1080p') === false) {
                    // Si aún no tenemos una imagen grande, usamos 'cover_small' o la más alta que tengamos
                    $imageUrl = str_replace('thumb', 'cover_small', $imageUrl); // Usamos el tamaño más pequeño en caso de no encontrar opciones mejores
                }

                // Guardar la URL de la portada en la base de datos
                Multimedia::updateOrCreate(
                    ['videojuego_id' => $videojuego->id, 'tipo' => 'imagen'],
                    ['url' => $imageUrl]
                );
            }

            // Agregar precios (aleatorio por plataforma)
            if (isset($game['platforms'])) {
                foreach ($game['platforms'] as $platform) {
                    $precio = rand(10, 100);
                    Precio::updateOrCreate(
                        ['videojuego_id' => $videojuego->id, 'plataforma_id' => $platform['id']],
                        ['precio' => $precio]
                    );
                }
            }
        }

        $this->command->info("¡Se completó la importación de juegos!");
    }
}
