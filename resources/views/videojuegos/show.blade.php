@extends('layouts.app')
@section('body_class', 'is-preload')

@section('title', $videojuego->nombre)

@section('content')
    @livewire('videojuegos.video-games-view-component', ['videojuegoId' => $videojuego->id])
@endsection
