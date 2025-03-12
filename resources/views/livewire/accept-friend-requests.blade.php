<div  wire:init="loadRequests" wire:poll="loadRequests">
    <div class="profile-section">
        <h2>📩 {{ __('Friend Requests') }}</h2>
        <ul>
            @foreach ($friendRequests as $request)
                <li>{{ $request->user->name }}
                    <button wire:click="acceptRequest({{ $request->user->id }})">
                        {{ __('Accept') }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>

