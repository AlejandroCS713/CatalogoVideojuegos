@extends('layouts.app')
@section('title', 'Foro')
@include('layouts.menu')

@section('content')
    <div class="game-container">
        <h1 class="mb-4">Foros</h1>
        @auth
            <div style=" display: flex;justify-content: center; margin-bottom: 40px;">
        <a href="{{ route('forum.create') }}" class="button fit" style="width: 250px;">Crear foro</a>
            </div>
                @endauth
        @foreach($foros as $foro)
            <div class="game-container">
                <div class="card-body">
                    <h5 class="card-title">{{ $foro->titulo }}</h5>
                    <p class="card-text">{{ $foro->descripcion }}</p>
                    <a href="{{ route('forum.show', $foro->id) }}" class="button fit" style="width: 200px">Ver Foro</a>
                </div>
            </div>
        @endforeach
        <ul class="pagination">
            {{ $foros->links('vendor.pagination.default') }}
        </ul>
    </div>
@endsection
