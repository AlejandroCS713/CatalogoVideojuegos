<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogroController extends Controller
{
    public function index()
    {
        $logros = Auth::user()->logros;

        return view('profile.logros', compact('logros'));
    }
}
