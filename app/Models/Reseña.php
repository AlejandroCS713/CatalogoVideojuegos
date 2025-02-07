<?php

namespace App\Models;

use App\Models\users\User;
use Illuminate\Database\Eloquent\Model;

class Reseña extends Model
{
    protected $fillable = ['usuario_id', 'videojuego_id', 'texto', 'calificacion'];
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function videojuego()
    {
        return $this->belongsTo(Videojuego::class);
    }

}
