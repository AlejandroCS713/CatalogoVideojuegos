<?php

namespace App\Models\games;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Multimedia extends Model
{
    use HasFactory;
    protected $fillable = ['videojuego_id', 'tipo', 'url'];

    public function videojuego()
    {
        return $this->belongsTo(Videojuego::class);
    }

}
