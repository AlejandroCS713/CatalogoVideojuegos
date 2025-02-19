<?php
namespace App\Http\Controllers\games;
use App\Events\LogroDesbloqueado;
use App\Http\Controllers\Controller;
use App\Jobs\NotificarLogroDesbloqueado;
use App\Models\games\Genero;
use App\Models\games\Multimedia;
use App\Models\games\Plataforma;
use App\Models\games\Precio;
use App\Models\games\Reseña;
use App\Models\games\Videojuego;
use App\Models\users\Logro;
use Illuminate\Http\Request;

class VideojuegoController extends Controller
{
    public function mejoresValoraciones()
    {
        $videojuegos = Videojuego::with('multimedia')
        ->where('fecha_lanzamiento', '>=', '2020-01-01')
        ->orderBy('rating_usuario', 'desc')
        ->take(6)
        ->get();
        return view('welcome', compact('videojuegos'));
    }

    public function index()
    {
        $videojuegos = Videojuego::with('multimedia')->paginate(30);

        return view('videojuegos.index', compact('videojuegos'));
    }

    public function show($id)
    {
        $videojuego = Videojuego::with('multimedia')->findOrFail($id);

        return view('videojuegos.show', compact('videojuego'));
    }

    public function create()
    {
        $plataformas = Plataforma::all();
        $generos = Genero::all();
        return view('videojuegos.create', compact('plataformas', 'generos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:videojuegos,nombre',
            'descripcion' => 'nullable|string',
            'fecha_lanzamiento' => 'nullable|date',
            'rating_usuario' => 'nullable|numeric|min:0|max:10',
            'rating_criticas' => 'nullable|numeric|min:0|max:10',
            'desarrollador' => 'nullable|string|max:255',
            'publicador' => 'nullable|string|max:255',
            'plataformas' => 'required|array',
            'generos' => 'required|array',
        ]);

        $videojuego = Videojuego::create($request->except(['plataformas', 'generos']));

        // Asociar plataformas y géneros
        $videojuego->plataformas()->attach($request->plataformas);
        $videojuego->generos()->attach($request->generos);

        return redirect()->route('videojuegos.index')->with('success', 'Videojuego creado exitosamente.');
    }

    public function edit($id)
    {
        $videojuego = Videojuego::findOrFail($id);
        $plataformas = Plataforma::all();
        $generos = Genero::all();
        return view('videojuegos.edit', compact('videojuego', 'plataformas', 'generos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:videojuegos,nombre,' . $id,
            'descripcion' => 'nullable|string',
            'fecha_lanzamiento' => 'nullable|date',
            'rating_usuario' => 'nullable|numeric|min:0|max:10',
            'rating_criticas' => 'nullable|numeric|min:0|max:10',
            'desarrollador' => 'nullable|string|max:255',
            'publicador' => 'nullable|string|max:255',
            'plataformas' => 'required|array',
            'generos' => 'required|array',
        ]);

        $videojuego = Videojuego::findOrFail($id);
        $videojuego->update($request->except(['plataformas', 'generos']));

        // Sincronizar plataformas y géneros
        $videojuego->plataformas()->sync($request->plataformas);
        $videojuego->generos()->sync($request->generos);

        return redirect()->route('videojuegos.index')->with('success', 'Videojuego actualizado.');
    }

    public function destroy($id)
    {
        $videojuego = Videojuego::findOrFail($id);

        // Eliminar relaciones
        $videojuego->plataformas()->detach();
        $videojuego->generos()->detach();
        Reseña::where('videojuego_id', $id)->delete();
        Multimedia::where('videojuego_id', $id)->delete();
        Precio::where('videojuego_id', $id)->delete();

        $videojuego->delete();

        return redirect()->route('videojuegos.index')->with('success', 'Videojuego eliminado.');
    }

    public function comentar(Request $request, $id) {
        $request->validate(['contenido' => 'required|string']);

        $videojuego = Videojuego::findOrFail($id);
        $comentario = $videojuego->comentarios()->create([
            'user_id' => auth()->id(),
            'contenido' => $request->contenido,
        ]);

        $logro = Logro::where('nombre', 'Primer Comentario')->first();
        if ($logro && !auth()->user()->logros->contains($logro)) {
            event(new LogroDesbloqueado(auth()->user(), $logro));
            dispatch(new NotificarLogroDesbloqueado(auth()->user(), $logro));
        }

        return back()->with('success', 'Comentario añadido.');
    }
}
