<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Videojuego;
use App\Models\Genero;
use App\Models\Plataforma;
use App\Models\Multimedia;
use App\Models\Precio;

class GamesTableSeeder extends Seeder
{
    public function run()
    {
        // Inicializar el número total de iteraciones que deseas hacer
        $iterations = 1000; // Número de veces que deseas hacer la petición (esto puede cambiar dependiendo de tus necesidades)

        // Repetir el proceso 1000 veces
        for ($i = 0; $i < $iterations; $i++) {
            // Calcular el offset actual (500 * $i) para obtener diferentes bloques de juegos
            $offset = $i * 500;

            // Consulta a la API de IGDB
            $response = Http::withHeaders([
                'Client-ID' => 'c5ew8x39828zbf5ccvewq5qsifet9p', // Tu Client-ID real
                'Authorization' => 'Bearer lslp9plhz9orvhsj916zbik9eyuyau', // Tu Bearer token real
                'Accept' => 'application/json   ',
            ])
                ->withBody("fields id,name,summary,first_release_date,rating,aggregated_rating,involved_companies.company.name,genres.id,genres.name,platforms.id,platforms.name,cover.url; limit 500; offset $offset;", 'application/x-www-form-urlencoded')  // Enviamos el cuerpo como raw con el offset dinámico
                ->post('https://api.igdb.com/v4/games');

            if ($response->failed()) {
                $this->command->error('Error al realizar la solicitud a la API.');
                return;
            }

            $games = $response->json(); // Convertir la respuesta a un array

            // Si no hay juegos, salimos del bucle
            if (empty($games)) {
                $this->command->info('No hay más juegos disponibles.');
                break;
            }

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
                        $precio = rand(10, 100); // Precio aleatorio
                        Precio::updateOrCreate(
                            ['videojuego_id' => $videojuego->id, 'plataforma_id' => $platform['id']],
                            ['precio' => $precio]
                        );
                    }
                }
            }

            // Mensaje de progreso para saber cuántos bloques hemos procesado
            $this->command->info("Iteración $i completada, offset: $offset");

            // Puedes agregar un pequeño retraso entre solicitudes para evitar problemas con la API
            sleep(1); // 1 segundo de espera
        }
    }
}
