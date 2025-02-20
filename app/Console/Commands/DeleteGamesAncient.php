<?php
namespace App\Console\Commands;

use App\Models\games\Videojuego;
use Illuminate\Console\Command;

class DeleteGamesAncient extends Command
{

    protected $signature = 'games:delet-ancient';

    protected $description = 'Eliminar juegos cuya fecha de lanzamiento sea anterior al 1 de enero de 1990.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $videojuegos = Videojuego::with(['multimedia', 'generos', 'plataformas', 'precios'])
            ->where('fecha_lanzamiento', '<=', '1990-01-01')
            ->get();

        $cantidad = $videojuegos->count();

        if ($cantidad == 0) {
            $this->info('No hay juegos para eliminar.');
            return 0;
        }

        foreach ($videojuegos as $videojuego) {
            $videojuego->generos()->detach();
            $videojuego->plataformas()->detach();

            $videojuego->precios()->delete();
            $videojuego->multimedia()->delete();

            $videojuego->delete();
        }
        $this->info("Se han eliminado {$cantidad} juegos de más de 1/1/1990.");

        return 0;
    }
}
