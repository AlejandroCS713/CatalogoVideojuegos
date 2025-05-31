<?php

namespace App\Models\Foro;

use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foro extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'descripcion', 'imagen', 'usuario_id'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function videojuegos()
    {
        return $this->belongsToMany(Videojuego::class, 'foro_videojuego', 'foro_id', 'videojuego_id')
            ->withPivot('rol_videojuego')
            ->withTimestamps();
    }

    public function mensajes()
    {
        return $this->hasMany(MensajeForo::class, 'foro_id');
    }
}
