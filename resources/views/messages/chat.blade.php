@extends('layouts.app')
@section('title', __('Chat'))
@section('body_class', 'is-preload')
@include('layouts.menu')
@section('content')
    <div class="chat-container">
        <h2>{{ __('Chat with') }} {{ $friend->name }}</h2>

        <div class="chat-box">
            @foreach($messages as $message)
                <div class="message {{ $message->sender_id == auth()->id() ? 'sent' : 'received' }}">
                    <p>{{ $message->message }}</p>
                </div>
            @endforeach
        </div>

        <form action="{{ route('message.send') }}" method="POST">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $friend->id }}">
            <textarea style="color: black" name="message" placeholder="{{ __('Type a message...') }}" required></textarea>
            <button type="submit">{{ __('Send') }}</button>
        </form>
    </div>
@endsection
