<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Videojuego;
use App\Models\Genero;
use App\Models\Plataforma;
class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientId = config('igdb.client_id');
        $accessToken = config('igdb.access_token');
        $baseUrl = config('igdb.base_url');

        $response = Http::withHeaders([
            'Client-ID' => $clientId,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post($baseUrl . 'games', [
            'fields' => 'name,summary,first_release_date,rating,aggregated_rating,involved_companies.company.name,genres.name,platforms.name',
            'limit' => 50, // Número de videojuegos a obtener
        ]);

        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                // Poblar la tabla `videojuegos`
                $videojuego = Videojuego::updateOrCreate(
                    ['nombre' => $game['name']],
                    [
                        'descripcion' => $game['summary'] ?? null,
                        'fecha_lanzamiento' => isset($game['first_release_date'])
                            ? date('Y-m-d', $game['first_release_date'])
                            : null,
                        'rating_usuario' => $game['rating'] ?? null,
                        'rating_criticas' => $game['aggregated_rating'] ?? null,
                        'desarrollador' => $game['involved_companies'][0]['company']['name'] ?? null,
                        'publicador' => $game['involved_companies'][1]['company']['name'] ?? null,
                    ]
                );

                // Poblar la tabla `generos`
                if (!empty($game['genres'])) {
                    foreach ($game['genres'] as $genre) {
                        $genero = Genero::firstOrCreate(['nombre' => $genre['name']]);
                        $videojuego->generos()->attach($genero->id);
                    }
                }

                // Poblar la tabla `plataformas`
                if (!empty($game['platforms'])) {
                    foreach ($game['platforms'] as $platform) {
                        $plataforma = Plataforma::firstOrCreate(['nombre' => $platform['name']]);
                        $videojuego->plataformas()->attach($plataforma->id);
                    }
                }
            }

            $this->command->info('Seeding completado con éxito.');
        } else {
            $this->command->error('Error al obtener datos de IGDB: ' . $response->body());
        }
    }
}
