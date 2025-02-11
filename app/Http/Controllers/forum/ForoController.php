<?php

namespace App\Http\Controllers\forum;

use App\Http\Controllers\Controller;

class ForoController extends Controller
{
    public function index()
    {
        return view('foro.index');
    }

    public function show($id)
    {
        return view('foro.show', compact('id'));
    }
}
