<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ForoResource;
use App\Models\Forum\Foro;

use Illuminate\Http\Request;

class ForoController extends Controller
{
    public function index()
    {
        $foros = Foro::paginate(10); // Puede cambiarse según lo que prefieras
        return ForoResource::collection($foros);
    }

    public function show($id)
    {
        $foro = Foro::with(['mensajes.usuario', 'mensajes.respuestas.usuario'])->findOrFail($id);
        return new ForoResource($foro);
    }
}
