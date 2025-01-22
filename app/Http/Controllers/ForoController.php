<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
