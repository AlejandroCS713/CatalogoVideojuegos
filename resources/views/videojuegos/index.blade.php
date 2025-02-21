@extends('layouts.app')
@section('title', 'GAME QUEST')
@section('body_class', 'is-preload videojuegos-bg')
@include('layouts.menu')

@section('title', 'Todos los Videojuegos')
    @section('content')

    <div class="videojuegos-container">
    <h1 class="text-center mb-5 title-games">Available Video Games</h1>
        <form method="GET" action="{{ route('videojuegos.index') }}" class="mb-4">
            <label for="sort">Ordenar por: </label>
            <select name="sort" id="sort" style="color: black;" onchange="this.form.submit()">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Más reciente</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antigua</option>
                <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>De la A a la Z</option>
                <option value="reverse_alphabetical" {{ request('sort') == 'reverse_alphabetical' ? 'selected' : '' }}>De la Z a la A</option>
            </select>
        </form>
        @can('Crear Videojuegos')
            <a href="{{ route('admin.create') }}" class="button fit" style="width: 200px;">Crear Juego</a>

        @endcan
    <div class="videojuegos-grid">
        @foreach ($videojuegos as $videojuego)
            <div>
                <div>
                    @if ($videojuego->multimedia->isNotEmpty())
                        <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"><img class="game-image" src="{{ asset($videojuego->multimedia->first()->url) }}" alt="Imagen de {{ $videojuego->nombre }}"/></a>
                    @else
                        <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"> {{ $videojuego->nombre }}</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
        <ul class="pagination">
            {{ $videojuegos->links('vendor.pagination.default') }}
        </ul>
    </div>
@endsection
