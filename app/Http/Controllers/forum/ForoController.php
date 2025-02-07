<?php

namespace App\Http\Controllers\forum;

use App\Http\Controllers\Controller;

class ForoController extends Controller
{
    public function index()
    {
        // Esta vista podría listar los temas del foro
        return view('foro.index');
    }

    public function show($id)
    {
        // Esta vista podría mostrar un tema específico
        return view('foro.show', compact('id'));
    }
}
