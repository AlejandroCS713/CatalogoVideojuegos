<?php

namespace App\Models\users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logro extends Model {
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'puntos'];

    public function usuarios() {
        return $this->belongsToMany(User::class, 'logro_usuario')->withTimestamps();
    }
}
