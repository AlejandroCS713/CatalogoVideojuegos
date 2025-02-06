@extends('layouts.app')
@section('title', 'GAME QUEST')
<!-- Header -->
<!-- Menu -->
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('title', 'Todos los Videojuegos')
    @section('content')
    <div class="videojuegos-container">
    <h1 class="text-center mb-5">Available Video Games</h1>
    <div class="videojuegos-grid">
        @foreach ($videojuegos as $videojuego)
            <div >
                <div>
                    <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"><img class="game-image" src="{{ asset($videojuego->multimedia->first()->url) }}" alt="Imagen de {{ $videojuego->nombre }}"/> </a>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Paginación -->
        <ul class="pagination">
            {{ $videojuegos->links() }}
        </ul>
    </div>
@endsection
