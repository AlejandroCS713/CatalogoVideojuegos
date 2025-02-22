<div>
    <input type="text" wire:model="searchTerm" wire:keydown.debounce.100ms="search" placeholder="{{ __('Search users...') }}" class="form-control" style="color: black; margin-bottom: 10px;">

    @if($message)
        <div class="error-message" style="color: red; margin-top: 10px;">
            {{ $message }}
        </div>
    @else
        <div class="usuarios-group">
            <ul id="search-results">
                @foreach ($users as $user)
                    <li>
                        {{ $user->name }}
                        <button wire:click="sendFriendRequest({{ $user->id }})">{{ __('Add') }}</button>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
