@extends('layouts.app')
@section('title', __('Chat'))
@section('body_class', 'is-preload')


@section('content')
    @livewire('chat-component')
@endsection
