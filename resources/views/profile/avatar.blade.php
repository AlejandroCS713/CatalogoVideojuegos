@extends('layouts.app')
@section('title', __('Avatar Change'))
@section('body_class', 'is-preload')
@section('content')
    <div class="profile-container container mx-auto p-4 md:p-8 max-w-xl bg-white shadow-xl rounded-lg my-8 transform transition-all duration-300 ease-in-out">
        <h2 class="text-4xl font-extrabold mb-8 text-gray-800 text-center leading-tight">{{ __('Edit Your Profile') }}</h2>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6 shadow-md" role="alert">
                <span class="block sm:inline font-semibold">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-xl relative mb-6 shadow-md" role="alert">
                <span class="block sm:inline font-semibold">{{ session('info') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 shadow-md" role="alert">
                <strong class="font-bold">{{ __('Oops!') }}</strong>
                <span class="block sm:inline">{{ __('There were some problems with your submission.') }}</span>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="p-6 border border-gray-200 rounded-xl bg-gray-50 mb-8">
            <h3 class="text-2xl font-semibold mb-4 text-gray-700">{{ __('Current Avatar') }}:</h3>
            <div class="mb-6 text-center avatar-principal">
                <img src="{{ asset('forty/images/avatars/' . $user->avatar) }}" alt="{{ __('Current Avatar') }}" class="w-32 h-32 rounded-full object-cover mx-auto mb-4 border-4 border-blue-400 shadow-md">
            </div>

            <h3 class="text-2xl font-semibold mb-4 text-gray-700">{{ __('Select a new avatar') }}:</h3>
            <form action="{{ route('profile.update-avatar') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-4 mb-6 justify-items-center">
                    @foreach ($avatars as $avatarName)
                        <label class="avatar-option block cursor-pointer text-center relative p-2 rounded-xl transition-all duration-200 ease-in-out hover:bg-blue-100 hover:shadow-md">
                            <input type="radio" name="avatar" value="{{ $avatarName }}" class="hidden peer"
                                {{ $user->avatar == $avatarName ? 'checked' : '' }}>
                            <img src="{{ asset('forty/images/avatars/' . $avatarName) }}" alt="{{ __('Avatar') }}"
                                 class="w-full h-auto rounded-full object-cover border-2 border-gray-300 transition-all duration-200
                                        peer-checked:border-blue-600 peer-checked:ring-2 peer-checked:ring-blue-300 transform peer-checked:scale-105">
                        </label>
                    @endforeach
                </div>
                @error('avatar') <p class="text-red-600 text-sm italic mt-2 text-center">{{ $message }}</p> @enderror

                <div class="flex items-center justify-end pt-4">
                    <button type="submit" class="btn-primary-green bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200 shadow-md">
                        {{ __('Save Avatar') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="p-6 border border-gray-200 rounded-xl bg-gray-50">
            <h3 class="text-2xl font-semibold mb-4 text-gray-700" style="margin-top: 15px">{{ __('Update Personal Information') }}</h3>
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 10px">
                    <label for="name" class="block text-gray-800 text-base font-semibold mb-2">{{ __('Nickname') }}:</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                           class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-700">
                    @error('name') <p class="text-red-600 text-sm italic mt-1">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 10px">
                    <label for="password" class="block text-gray-800 text-base font-semibold mb-2">{{ __('New Password') }}:</label>
                    <input type="password" name="password" id="password"
                           class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-700">
                    @error('password') <p class="text-red-600 text-sm italic mt-1">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 10px">
                    <label for="password_confirmation" class="block text-gray-800 text-base font-semibold mb-2">{{ __('Confirm New Password') }}:</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-700">
                </div>

                <div class="flex items-center justify-end pt-4" style="margin-bottom: 10px">
                    <button type="submit" class="btn-primary-blue bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200 shadow-md">
                        {{ __('Update Profile') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
