@extends('layouts.app')
@section('title', __('All Video Games'))
@section('body_class', 'is-preload videojuegos-bg')

@section('content')
    @livewire('videojuegos.video-games-view-component')
@endsection
