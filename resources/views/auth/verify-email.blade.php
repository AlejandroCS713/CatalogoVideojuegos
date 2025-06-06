@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-md shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
            {{ __('Verify Email') }}
        </h2>

        <div class="mb-6 text-lg text-gray-600 dark:text-gray-400">
            {{ __('Verify Email Description') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 font-medium text-lg text-green-600 dark:text-green-400">
                {{ __('Verification Link Sent') }}
            </div>
        @endif

        <div class="mt-6 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {{ __('Resend Verification') }}
                </button>
            </form>

            <div class="flex items-center space-x-4 ml-6">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="underline text-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
