@extends('layouts.app')
@section('title',  __('View Forum'))
@section('body_class', 'is-preload')

@section('content')
    @livewire('foros.forum-index', ['foroId' => $foro->id])
@endsection
