@extends('layouts.app')
@section('title', 'Create Forum')
@include('layouts.menu')

@section('content')
    <h1>Crear Foro</h1>
    <div class="game-container" style=" display: flex;justify-content: center; margin-bottom: 40px;">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('forum.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <p>Título</p>
                <input style="color: black; margin-bottom: 20px;" type="text" name="titulo" id="titulo"  class="form-control" value="{{ old('titulo') }}" required >
                @error('titulo')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <p>Descripción</p>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="5" required style="color: black; margin-bottom: 20px;">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" >Crear Foro</button>
        </form>
    </div>
@endsection
