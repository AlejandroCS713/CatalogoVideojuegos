@extends('layouts.app')
@section('body_class', 'is-preload')

@section('title', $videojuego->nombre)

@section('content')
    @livewire('videojuegos.index-component', ['videojuegoId' => $videojuego->id])
@endsection
