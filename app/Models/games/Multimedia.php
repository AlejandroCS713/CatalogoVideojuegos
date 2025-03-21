<?php

namespace App\Models\games;

use Illuminate\Database\Eloquent\Model;

class Multimedia extends Model
{
    protected $fillable = ['videojuego_id', 'tipo', 'url'];

    public function videojuego()
    {
        return $this->belongsTo(Videojuego::class);
    }

}
