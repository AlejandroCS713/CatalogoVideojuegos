<?php

namespace App\Http\Controllers\Foro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foro\ForoRequest;
use App\Models\Foro\Foro;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Str;

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

        if ($request->has('videojuegos') && is_array($request->videojuegos)) {
            $videojuegoData = [];
            foreach ($request->videojuegos as $videojuego_id) {
                $videojuegoData[$videojuego_id] = ['rol_videojuego' => $request->rol_videojuego ?? 'secundario'];
            }
            $foro->videojuegos()->attach($videojuegoData);
        }


        return redirect()->route('forum.index')->with('success', '¡Foro creado exitosamente!');
        //dd($request->all());
    }

    public function edit(Foro $foro)
    {
        return view('forum.edit', compact('foro'));
    }
    public function update(ForoRequest $request, Foro $foro)
    {
        $foro->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'imagen' => $request->imagen,
        ]);

        if ($request->has('videojuegos') && is_array($request->videojuegos)) {
            $videojuegoData = [];
            foreach ($request->videojuegos as $videojuego_id) {
                $videojuegoData[$videojuego_id] = ['rol_videojuego' => $request->rol_videojuego ?? 'secundario'];
            }
            $foro->videojuegos()->sync($videojuegoData);
        }

        return redirect()->route('forum.index')->with('success', 'Foro actualizado');
    }
    public function destroy(Foro $foro)
    {
        $foro->delete();

        return redirect()->route('forum.index')->with('success', '¡Foro eliminado exitosamente!');
    }


    public function generarPDF(Foro $foro)
    {
        $nombreUsuario = Str::slug($foro->usuario->name);
        $nombreArchivo = "Foro-{$nombreUsuario}.pdf";

        $pdf = Pdf::loadView('forum.pdf', compact('foro'));

        return $pdf->download($nombreArchivo);
    }
}
