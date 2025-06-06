<?php

namespace App\Http\Controllers\Foro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foro\ForoRequest;
use App\Models\Foro\Foro;
use App\Models\games\Videojuego;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Str;

class ForoController extends Controller
{
    public function index()
    {
        $foros = Foro::paginate(10);
        return view('foro.index', compact('foros'));
    }

    public function show(Foro $foro)
    {
        $foro->load([
            'mensajes.usuario',
            'mensajes.respuestas.usuario',
            'videojuegos'
        ]);
        return view('foro.show', compact('foro'));
    }

    public function generarPDF(Foro $foro)
    {
        $nombreUsuario = Str::slug($foro->usuario->name);
        $nombreArchivo = "Foro-{$nombreUsuario}.pdf";

        $pdf = Pdf::loadView('foro.pdf', compact('foro'));

        return $pdf->download($nombreArchivo);
    }
}
