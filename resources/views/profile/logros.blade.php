@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')
@section('title', __('My Achievements'))
@section('body_class', 'is-preload')
@section('content')
    <div class="game-container">
        <div>
            <h1 class="game-title">🎯 {{ __('My Achievements') }}</h1>
            <p class="game-description">{{ __('Here you can see all the achievements you have unlocked.') }}</p>
        </div>

        <div class="game-info">
            @forelse ($logros as $logro)
                <div class="logro-item">
                    <h3>{{ $logro->nombre }}</h3>
                    <p>{{ $logro->descripcion }}</p>
                    <span class="game-price">+{{ $logro->puntos }} {{ __('Points') }}</span>
                </div>
            @empty
                <p>{{ __('You have not unlocked any achievements yet. Keep playing and unlock more achievements!') }}</p>
            @endforelse
        </div>
    </div>
@endsection
