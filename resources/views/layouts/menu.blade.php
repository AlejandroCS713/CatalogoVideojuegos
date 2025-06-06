
<header id="header" class="alt">
    <a href="{{ auth()->check() && auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('welcome') }}" class="logo">
        <strong style="margin-top: 15px">GameQuest</strong>
    </a>
    <nav>
        @auth
            <div class="user-info">

                <span class="user-name">{{ Auth::user()->name }}</span>
                <div class="avatar-menu" style="margin-top: 16px">
               <a href="{{ route('profile') }}" class="settings-link"><img src="{{ asset('forty/images/avatars/' . Auth::user()->avatar) }}" alt="{{ __('Avatar of') }} {{ Auth::user()->name }}"></a>
                </div>
            </div>
        @endauth
        <a href="#menu">{{ __('Menu') }}</a>
    </nav>
</header>
<nav id="menu">
    <ul class="links">
        <li>
            @if(auth()->check() && auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}">{{ __('Home') }}</a>
            @else
                <a href="{{ route('welcome') }}">{{ __('Home') }}</a>
            @endif
        </li>
        <li><a href="{{ route('videojuegos.index') }}">{{ __('Video Games') }}</a></li>
        <li><a href="{{ route('foro.index') }}">{{ __('Foro') }}</a></li>
    </ul>
    <ul class="actions stacked">

        @guest
            <!-- Si el usuario no ha iniciado sesión-->
            <li><a href="{{ route('login') }}" class="button primary fit">{{ __('Log in') }}</a></li>
            <li><a href="{{ route('register') }}" class="button fit">{{ __('Create Account') }}</a></li>
        @else
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" onclick="markNotificationsAsRead()">
                🔔 {{ __('Notifications') }} <span id="notif-count" class="badge badge-danger">{{ Auth::user()->unreadNotifications->count() }}</span>
            </a>
                <div class="dropdown-menu" id="notification-list">
                    @foreach(Auth::user()->unreadNotifications as $notification)
                        @if(isset($notification->data['sender_id']))
                            <a class="dropdown-item2" href="{{ route('message.chat', $notification->data['sender_id']) }}">
                                {{ __('New Message') }} --> {{ \App\Models\users\User::find($notification->data['sender_id'])->name }}
                            </a>
                        @elseif(isset($notification->data['logro_nombre']))
                            <!-- Esto es para mostrar una notificación de logro -->
                            <a class="dropdown-item2" href="{{ route('logros.perfil') }}">
                                {{ __('You unlocked the achievement:') }} {{ $notification->data['logro_nombre'] }}!
                            </a>
                        @else
                            <!-- Aquí podrías manejar otros tipos de notificaciones -->
                            <a class="dropdown-item2" href="#">
                                {{ __('Unknown Notification') }}
                            </a>
                        @endif
                    @endforeach
                </div>
        </li>
        <!-- Si el usuario ha iniciado sesión -->
            <li><a href="{{ route('profile') }}" class="button primary fit">{{ __('My Profile') }}</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">{{ __('Logout') }}</button>
                </form>
            </li>
        @endguest
    </ul>
</nav>

