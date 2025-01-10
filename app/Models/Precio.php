<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $fillable = ['videojuego_id', 'plataforma_id', 'precio'];

    public function videojuego()
    {
        return $this->belongsTo(Videojuego::class);
    }

    public function plataforma()
    {
        return $this->belongsTo(Plataforma::class);
    }
}
