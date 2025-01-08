<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reseña extends Model
{
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function videojuego()
    {
        return $this->belongsTo(Videojuego::class);
    }

}
