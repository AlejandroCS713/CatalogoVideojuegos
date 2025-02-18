<?php

namespace App\Http\Controllers\forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\ForoRequest;
use App\Models\Forum\Foro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ForoController extends Controller
{
    public function index()
    {
        $foros = Foro::paginate(10);
        return view('forum.index', compact('foros'));
    }

    public function show(Foro $foro)
    {
        $foro->load([
            'mensajes.usuario',
            'mensajes.respuestas.usuario',
            'videojuegos'
        ]);
        return view('forum.show', compact('foro'));
    }

    public function create()
    {
        return view('forum.create');
    }

    public function store(ForoRequest $request)
    {
        $foro = Foro::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'imagen' => $request->imagen,
            'usuario_id' => auth()->id(),
            'videojuego_id' => $request->videojuego_id,
        ]);

        if ($request->videojuego_id) {
            $foro->videojuegos()->attach($request->videojuego_id);
        }
        return redirect()->route('forum.index')->with('success', '¡Foro creado exitosamente!');
        //dd($request->all());
    }

    public function update(ForoRequest $request, Foro $foro)
    {
        $this->authorize('update', $foro);

        $foro->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'imagen' => $request->imagen,
        ]);

        if ($request->videojuegos) {
            $foro->videojuegos()->sync($request->videojuegos);
        }

        return response()->json(['message' => 'Foro actualizado', 'foro' => $foro]);
    }

    public function destroy(Foro $foro)
    {
        $this->authorize('delete', $foro);

        $foro->delete();

        return response()->json(['message' => 'Foro eliminado con éxito']);
    }
}
