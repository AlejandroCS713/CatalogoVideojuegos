@extends('layouts.app')
@section('title', __('All Video Games'))
@section('body_class', 'is-preload videojuegos-bg')
    @section('content')

    <div class="videojuegos-container">
    <h1 class="text-center mb-5 title-games">{{ __('Available Video Games') }}</h1>
        <form method="GET" action="{{ route('videojuegos.index') }}" class="mb-4">
            <label for="sort">{{ __('Sort by:') }} </label>
            <select name="sort" id="sort" style="color: black;" onchange="this.form.submit()">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest') }}</option>
                <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>{{ __('A to Z') }}</option>
                <option value="reverse_alphabetical" {{ request('sort') == 'reverse_alphabetical' ? 'selected' : '' }}>{{ __('Z to A') }}</option>
                <option value="top_rated_aaa" {{ request('sort') == 'top_rated_aaa' ? 'selected' : '' }}>{{ __('Top Rated AAA Games') }}</option>
                <option value="exclusive_games" {{ request('sort') == 'exclusive_games' ? 'selected' : '' }}>{{ __('Exclusive Games') }}</option>
            </select>
        </form>
        @can('Crear Videojuegos')
            <a href="{{ route('admin.create') }}" class="button fit" style="width: 200px;">{{ __('Create Game') }}</a>

        @endcan
        <div class="videojuegos-grid">
            @foreach ($videojuegos as $videojuego)
                <div>
                    <div>
                        @php
                            $imageUrl = null;

                            if (isset($videojuego->multimedia->first()->url) && strpos($videojuego->multimedia->first()->url, 'http') === 0) {
                                $imageUrl = $videojuego->multimedia->first()->url;
                            }

                            elseif (isset($videojuego->multimedia->first()->url)) {
                                 $imageUrl = asset('storage/' . $videojuego->multimedia->first()->url);
                            }
                            else {
                                $imageUrl = asset('storage/default-image.jpg');
                            }
                        @endphp

                        <a style="background: none; border: none; cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}">
                            <img class="game-image" src="{{ $imageUrl }}" alt="{{ __('Image of ') }} {{ $videojuego->nombre }}"/>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <ul class="pagination">
            {{ $videojuegos->links('vendor.pagination.default') }}
        </ul>
    </div>
@endsection
