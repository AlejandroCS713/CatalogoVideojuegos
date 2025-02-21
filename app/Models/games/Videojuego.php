<?php

namespace App\Models\games;

use App\Models\Forum\Foro;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videojuego extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_lanzamiento',
        'rating_usuario',
        'rating_criticas',
        'desarrollador',
        'publicador',
    ];

    public function scopeNewest($query)
    {
        return $query->orderBy('fecha_lanzamiento', 'desc');
    }

    public function scopeOldest($query)
    {
        return $query->orderBy('fecha_lanzamiento', 'asc');
    }

    public function scopeAlphabetically($query)
    {
        return $query->orderBy('nombre', 'asc');
    }

    public function scopeReverseAlphabetically($query)
    {
        return $query->orderBy('nombre', 'desc');
    }
    public function multimedia()
    {
        return $this->hasMany(Multimedia::class, 'videojuego_id');
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'videojuego_genero');
    }

    public function foros()
    {
        return $this->belongsToMany(Foro::class, 'foro_videojuego')->withTimestamps();
    }


    public function plataformas()
    {
        return $this->belongsToMany(Plataforma::class, 'videojuego_plataforma');
    }

    public function precios()
    {
        return $this->hasMany(Precio::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($videojuego) {
            $videojuego->generos()->detach();
            $videojuego->plataformas()->detach();
        });
    }
}
