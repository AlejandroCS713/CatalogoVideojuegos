<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Http\Requests\users\StoreUserAdminRequest;
use App\Http\Requests\users\UpdateUserAdminRequest;
use App\Models\Foro\Foro;
use App\Models\games\Genero;
use App\Models\games\Multimedia;
use App\Models\games\Plataforma;
use App\Models\games\Precio;
use App\Models\games\Reseña;
use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendBulkEmailToUser;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Console\Command;




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

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('videojuegos', 'public');

            Multimedia::create([
                'videojuego_id' => $videojuego->id,
                'tipo' => 'imagen',
                'url' => str_replace('public/', 'storage/', $path)
            ]);
        }

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

        if ($request->hasFile('imagen')) {
            $imagenActual = Multimedia::where('videojuego_id', $id)
                ->where('tipo', 'imagen')
                ->first();

            if ($imagenActual) {
                Storage::delete(str_replace('storage/', 'public/', $imagenActual->url));
                $imagenActual->delete();
            }

            $path = $request->file('imagen')->store('videojuegos', 'public');

            Multimedia::create([
                'videojuego_id' => $videojuego->id,
                'tipo' => 'imagen',
                'url' => str_replace('public/', 'storage/', $path)
            ]);
        }
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

    public function sendBulkEmail(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:10',
        ]);

        $messageContent = $validated['message'];

        Log::info("Recibida petición web para ejecutar el comando bulk:email.");

        try {
            $outputBuffer = new BufferedOutput;

            $exitCode = Artisan::call('bulk:email', [
                'message' => $messageContent
            ], $outputBuffer);

            $commandOutput = $outputBuffer->fetch();
            Log::info("Salida del comando 'bulk:email' ejecutado desde la web:\n" . $commandOutput);

            if ($exitCode === Command::SUCCESS) {
                Log::info("Comando bulk:email ejecutado exitosamente desde la web.");
                return redirect()->route('admin.dashboard')
                    ->with('success', 'El comando para encolar correos se ha ejecutado. Los correos se enviarán en segundo plano.');
            } else {
                Log::error("El comando bulk:email ejecutado desde la web falló con código de salida: " . $exitCode);
                return redirect()->route('admin.dashboard')
                    ->with('error', 'El comando de envío de correos reportó un error durante su ejecución. Revisa los logs.');
            }

        } catch (\Exception $e) {
            Log::error("Excepción al intentar ejecutar Artisan::call('bulk:email') desde la web: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'Ocurrió un error inesperado al intentar iniciar el comando de envío. Revisa los logs.');
        }
    }
}
