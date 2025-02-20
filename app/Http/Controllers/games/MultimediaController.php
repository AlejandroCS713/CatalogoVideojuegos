<?php

namespace App\Http\Controllers\games;

use App\Http\Controllers\Controller;
use App\Http\Requests\Games\StoreMultimediaRequest;
use App\Models\games\Multimedia;
use App\Models\games\Videojuego;
use Illuminate\Http\Request;

class MultimediaController extends Controller
{
    public function store(StoreMultimediaRequest $request, $videojuegoId)
    {
        $path = $request->file('url')->store('public/multimedia');

        $multimedia = new Multimedia([
            'type' => $request->type,
            'url' => basename($path),
        ]);

        $videojuego = Videojuego::findOrFail($videojuegoId);
        $videojuego->multimedia()->save($multimedia);

        return redirect()->route('videojuegos.show', $videojuegoId);
    }
}
