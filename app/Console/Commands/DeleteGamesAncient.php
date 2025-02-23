<?php
namespace App\Console\Commands;

use App\Models\games\Videojuego;
use Illuminate\Console\Command;

class DeleteGamesAncient extends Command
{
    protected $signature = 'games:delete-ancient';
    protected $description = 'Eliminar juegos cuya fecha de lanzamiento sea anterior a o posterior a una fecha especificada.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $fechaLimite = $this->ask('¿Cuál es la fecha límite para eliminar los juegos? (Formato: yyyy-mm-dd)');

        if (!$this->validateDate($fechaLimite)) {
            $this->error('La fecha ingresada no tiene el formato correcto (yyyy-mm-dd).');
            return 1;
        }

        $orden = $this->choice('¿En qué orden quieres eliminar los juegos?', ['Ascendente', 'Descendente'], 0);

        $orden = ($orden == 'Ascendente') ? 'asc' : 'desc';

        if ($orden == 'asc') {
            $operador = '>=';
        } else {
            $operador = '<=';
        }

        $videojuegos = Videojuego::with(['multimedia', 'generos', 'plataformas', 'precios'])
            ->where('fecha_lanzamiento', $operador, $fechaLimite)
            ->orderBy('fecha_lanzamiento', $orden)
            ->get();

        $cantidad = $videojuegos->count();

        if ($cantidad == 0) {
            $this->info('No hay juegos para eliminar.');
            return 0;
        }

        if (!$this->confirm("¿Estás seguro que deseas eliminar {$cantidad} juegos?", true)) {
            $this->info('Operación cancelada.');
            return 0;
        }

        foreach ($videojuegos as $videojuego) {
            $videojuego->generos()->detach();
            $videojuego->plataformas()->detach();
            $videojuego->precios()->delete();
            $videojuego->multimedia()->delete();
            $videojuego->delete();
        }

        $this->info("Se han eliminado {$cantidad} juegos.");

        return 0;
    }
    private function validateDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
