<?php

namespace App\Http\Controllers;

use App\Models\games\Videojuego;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index()
    {
        $videojuegos = Videojuego::take(3)->get();

        return view('info', compact('videojuegos'));
    }
}
