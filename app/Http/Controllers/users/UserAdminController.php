<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Http\Requests\users\StoreUserAdminRequest;
use App\Http\Requests\users\UpdateUserAdminRequest;
use App\Models\games\Genero;
use App\Models\games\Multimedia;
use App\Models\games\Plataforma;
use App\Models\games\Precio;
use App\Models\games\Reseña;
use App\Models\games\Videojuego;
use App\Models\users\User;
use App\Models\Forum\Foro;


class UserAdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $forumCount = Foro::count();
        $gameCount = Videojuego::count();

        $users = User::all();
        $foros = Foro::all();
        $videojuegos = Videojuego::all();

        return view('admin.dashboard', compact('userCount', 'forumCount', 'gameCount', 'users', 'foros', 'videojuegos'));
    }
    public function create()
    {
        $plataformas = Plataforma::all();
        $generos = Genero::all();
        return view('admin.create', compact('plataformas', 'generos'));
    }

    public function store(StoreUserAdminRequest $request)
    {
        $videojuego = Videojuego::create($request->validated());

        $videojuego->plataformas()->attach($request->plataformas);
        $videojuego->generos()->attach($request->generos);

        return redirect()->route('videojuegos.index')->with('success', 'Videojuego creado exitosamente.');
    }

    public function edit($id)
    {
        $videojuego = Videojuego::findOrFail($id);
        $plataformas = Plataforma::all();
        $generos = Genero::all();
        return view('admin.edit', compact('videojuego', 'plataformas', 'generos'));
    }

    public function update(UpdateUserAdminRequest $request, $id)
    {
        $videojuego = Videojuego::findOrFail($id);
        $videojuego->update($request->validated());

        $videojuego->plataformas()->sync($request->plataformas);
        $videojuego->generos()->sync($request->generos);

        return redirect()->route('videojuegos.index')->with('success', 'Videojuego actualizado.');
    }

    public function destroy($id)
    {
        $videojuego = Videojuego::findOrFail($id);

        $videojuego->plataformas()->detach();
        $videojuego->generos()->detach();
        Reseña::where('videojuego_id', $id)->delete();
        Multimedia::where('videojuego_id', $id)->delete();
        Precio::where('videojuego_id', $id)->delete();

        $videojuego->delete();

        return redirect()->route('videojuegos.index')->with('success', 'Videojuego eliminado.');
    }
}
