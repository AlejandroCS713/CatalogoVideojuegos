<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Videojuego extends Model
{

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'videojuego_genero');
    }


    public function plataformas()
    {
        return $this->belongsToMany(Plataforma::class, 'videojuego_plataforma');
    }
}
