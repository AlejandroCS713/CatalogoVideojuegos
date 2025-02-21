
<header id="header" class="alt">
    <a href="{{ route('welcome') }}" class="logo"><strong style="margin-top: 15px">GameQuest</strong></a>
    <nav>
        @auth
            <div class="user-info">

                <span class="user-name">{{ Auth::user()->name }}</span>
                <div class="avatar-menu">
               <a href="{{ route('profile') }}" class="settings-link"><img src="{{ asset('forty/images/avatars/' . Auth::user()->avatar) }}" alt="Avatar de {{ Auth::user()->name }}"></a>
                </div>
            </div>
        @endauth
        <a href="#menu">Menu</a>
    </nav>
</header>
<nav id="menu">
    <ul class="links">
        <li><a href="{{ route('welcome') }}">Inicio</a></li>
        <li><a href="{{ route('videojuegos.index') }}">Video juegos</a></li>
        <li><a href="{{ route('forum.index') }}">Foro</a></li>
    </ul>
    <ul class="actions stacked">

        @guest
            <!-- Si el usuario no ha iniciado sesión-->
            <li><a href="{{ route('login') }}" class="button primary fit">Logear</a></li>
            <li><a href="{{ route('register') }}" class="button fit">Crear Usuario</a></li>
        @else
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" onclick="markNotificationsAsRead()">
                🔔 Notificaciones <span id="notif-count" class="badge badge-danger">{{ Auth::user()->unreadNotifications->count() }}</span>
            </a>
            <div class="dropdown-menu" id="notification-list">
                @foreach(Auth::user()->unreadNotifications as $notification)
                    <a class="dropdown-item2" href="{{ route('message.chat', $notification->data['sender_id']) }}">
                            Nuevo Mensaje --> {{ \App\Models\users\User::find($notification->data['sender_id'])->name }}
                    </a>
                @endforeach
            </div>
        </li>
        <!-- Si el usuario ha iniciado sesión -->
            <li><a href="{{ route('profile') }}" class="button primary fit">Mi perfil</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Desconectar</button>
                </form>
            </li>
        @endguest
    </ul>
</nav>

