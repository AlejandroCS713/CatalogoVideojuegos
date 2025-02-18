@extends('layouts.app')
@section('title', 'Forum')
@include('layouts.menu')

@section('content')
    <div class="game-container">
        <h1 class="mb-4">Foros</h1>
        @auth
            <div style=" display: flex;justify-content: center; margin-bottom: 40px;">
        <a href="{{ route('forum.create') }}" class="button fit" style="width: 200px;">Create forum</a>
            </div>
                @endauth
        @foreach($foros as $foro)
            <div class="game-container">
                <div class="card-body">
                    <h5 class="card-title">{{ $foro->titulo }}</h5>
                    <p class="card-text">{{ $foro->descripcion }}</p>
                    <a href="{{ route('forum.show', $foro->id) }}" class="button fit" style="width: 150px">Ver Foro</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
