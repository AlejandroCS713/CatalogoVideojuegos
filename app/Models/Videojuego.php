<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Videojuego extends Model
{
    public function favoritos()
    {
        return $this->hasMany(Favorito::class);
    }

    public function reseñas()
    {
        return $this->hasMany(Reseña::class);
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'videojuego_genero');
    }

    public function launchers()
    {
        return $this->belongsToMany(Launcher::class, 'videojuego_launcher');
    }

    public function plataformas()
    {
        return $this->belongsToMany(Plataforma::class, 'videojuego_plataforma');
    }
}
