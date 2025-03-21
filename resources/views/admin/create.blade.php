@extends('layouts.app')
@section('title', __('Create Game'))
@section('body_class', 'is-preload videojuegos-bg')

@section('content')
    <h2 class="form-title">{{ __('Create New Game') }}</h2>
    <div class="form-container">
        <x-game-form :action="route('admin.store')" :plataformas="$plataformas" :generos="$generos" />
    </div>
@endsection
