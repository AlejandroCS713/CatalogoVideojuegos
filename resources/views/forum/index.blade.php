@extends('layouts.app')
@section('title', 'Forum')
@include('layouts.menu')

@section('content')
    <div class="container">
        <h1 class="mb-4">Foros</h1>
        <li><a href="{{ route('forum.create') }}" class="button fit">Create forum</a></li>
        @foreach($foros as $foro)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $foro->titulo }}</h5>
                    <p class="card-text">{{ $foro->descripcion }}</p>
                    <a href="{{ route('forum.show', $foro->id) }}" class="btn btn-primary">Ver Foro</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
