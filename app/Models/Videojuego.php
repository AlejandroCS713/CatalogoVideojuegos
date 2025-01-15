<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videojuego extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_lanzamiento',
        'rating_usuario',
        'rating_criticas',
        'desarrollador',
        'publicador',
    ];
    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'videojuego_genero');
    }


    public function plataformas()
    {
        return $this->belongsToMany(Plataforma::class, 'videojuego_plataforma');
    }
    protected static function boot()
    {
        parent::boot();

        // Aseguramos que las relaciones se eliminan al borrar el videojuego
        static::deleting(function ($videojuego) {
            $videojuego->generos()->detach(); // Eliminar relaciones con géneros
            $videojuego->plataformas()->detach(); // Eliminar relaciones con plataformas
        });
    }
}
