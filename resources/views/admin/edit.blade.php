@extends('layouts.app')
@section('title', __('Update Game'))
@section('body_class', 'is-preload videojuegos-bg')
@section('content')
    <h2 class="form-title">{{ __('Edit Game') }}</h2>
    <div class="form-container">
        <x-game-form :action="route('admin.update', $videojuego->id)" method="PUT"
                     :videojuego="$videojuego" :plataformas="$plataformas" :generos="$generos" />
    </div>
@endsection
