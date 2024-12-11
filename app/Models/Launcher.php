<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Launcher extends Model
{
    public function videojuegos()
    {
        return $this->belongsToMany(Videojuego::class, 'videojuego_launcher');
    }
    
}
