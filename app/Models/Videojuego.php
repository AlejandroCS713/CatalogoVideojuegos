<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Videojuego extends Model
{
    public function favoritos()
    {
        return $this->hasMany(Favoritos::class);
    }

    public function reseñas()
    {
        return $this->hasMany(Reseñas::class);
    }
}
