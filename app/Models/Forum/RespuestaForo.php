<?php

namespace App\Models\Forum;

use App\Models\users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaForo extends Model
{
    //use HasFactory;
    protected $table = 'respuestas_foro';
    protected $fillable = ['contenido', 'imagen', 'mensaje_id', 'usuario_id'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function mensaje()
    {
        return $this->belongsTo(MensajeForo::class, 'mensaje_id');
    }
}
