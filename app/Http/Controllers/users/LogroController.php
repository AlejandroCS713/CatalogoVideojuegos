<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class LogroController extends Controller
{
    public function index()
    {
        $logros = Auth::user()->logros;

        return view('profile.logros', compact('logros'));
    }
    public function generarPDF()
    {
        $user = Auth::user();
        $logros = $user->logros;

        $nombreArchivo = "Logros-{$user->id}.pdf";

        $pdf = Pdf::loadView('profile.logros-pdf', compact('user', 'logros'));

        return $pdf->download($nombreArchivo);
    }
}
