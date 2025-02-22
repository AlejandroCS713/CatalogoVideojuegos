@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')
@section('title', 'Mis Logros')
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('content')
    <div class="game-container">
        <div>
            <h1 class="game-title">🎯 Mis Logros</h1>
            <p class="game-description">Aquí puedes ver todos los logros que has desbloqueado.</p>
        </div>

        <div class="game-info">
            @forelse ($logros as $logro)
                <div class="logro-item">
                    <h3>{{ $logro->nombre }}</h3>
                    <p>{{ $logro->descripcion }}</p>
                    <span class="game-price">+{{ $logro->puntos }} Puntos</span>
                </div>
            @empty
                <p>No has desbloqueado ningún logro aún. ¡Sigue jugando y desbloquea más logros!</p>
            @endforelse
        </div>
    </div>
@endsection
