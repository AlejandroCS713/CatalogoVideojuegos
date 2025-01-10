<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plataforma extends Model
{
    public function videojuegos()
    {
        return $this->belongsToMany(Videojuego::class);
    }
}
