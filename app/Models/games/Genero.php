<?php

namespace App\Models\games;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
         ];
    public function videojuegos()
    {
        return $this->belongsToMany(Videojuego::class);
    }

}
