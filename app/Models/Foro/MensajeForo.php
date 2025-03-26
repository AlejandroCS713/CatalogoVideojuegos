<?php

namespace App\Models\Foro;

use App\Models\users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MensajeForo extends Model
{
    use HasFactory;

    protected $fillable = ['contenido', 'imagen', 'foro_id', 'usuario_id'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function foro()
    {
        return $this->belongsTo(Foro::class, 'foro_id');
    }

    public function respuestas()
    {
        return $this->hasMany(RespuestaForo::class, 'mensaje_id');
    }
}
