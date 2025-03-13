<div wire:init="loadMessages" wire:poll="loadMessages">
    <div class="chat-container">
        <h2>💬 {{ __('Chat with') }} {{ $friend->name }}</h2>

        <div class="chat-box">
            @foreach($messages as $message)
                <div class="message {{ $message->sender_id == auth()->id() ? 'sent' : 'received' }}">
                    <p>{{ $message->message }}</p>
                </div>
            @endforeach
        </div>

        <div class="message-input">
            <input type="text" style="color: black" wire:model="newMessage" wire:keydown.enter="sendMessage" placeholder="Escribe un mensaje...">
            <button wire:click="sendMessage">Enviar</button>
        </div>

        @if(session()->has('error'))
            <p class="error">{{ session('error') }}</p>
        @endif
    </div>
</div>
