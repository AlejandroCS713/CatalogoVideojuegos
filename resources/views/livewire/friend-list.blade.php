@php use Illuminate\Support\Facades\Auth; @endphp
<div wire:init="loadFriends" wire:poll="loadFriends">
    <div class="profile-section">
        <h2>👥 {{ __('Friends') }}</h2>

            <ul class="friends-list">
                @foreach ($friends as $friend)

                    <li class="friend-item">
                            <img src="{{ asset('forty/images/avatars/' . ($friend->avatar ?? 'default-avatar.png')) }}"
                                 alt="{{ __('Avatar of') }}{{ $friend->name }}" class="friend-avatar">

                            <span class="friend-name">{{ $friend->name }}</span>

                            <div class="dropdown">
                                <button class="dropdown-toggle">⋮</button>
                                <div class="dropdown-menu">
                                    <form action="{{ route('message.chat', $friend->id) }}" method="GET">
                                        <button type="submit" class="dropdown-item chat-btn">
                                            💬 {{ __('Chat') }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('friends.remove', $friend->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">{{ __('Remove Friend') }}</button>
                                    </form>
                                </div>
                            </div>
                    </li>
                @endforeach
            </ul>
    </div>
</div>
