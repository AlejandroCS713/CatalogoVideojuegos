
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
        <!-- Página de inicio -->
        <li><a href="{{ route('welcome') }}">Home</a></li>
        <!-- Nuevas opciones -->
        <li><a href="{{ route('videojuegos.index') }}">Videojuegos</a></li>
        <li><a href="{{ route('foro.index') }}">Foro</a></li>
    </ul>
    <ul class="actions stacked">

        @guest
            <!-- Si el usuario no ha iniciado sesión-->
            <li><a href="{{ route('login') }}" class="button primary fit">Iniciar sesión</a></li>
            <li><a href="{{ route('register') }}" class="button fit">Crear usuario</a></li>
        @else
            <!-- Si el usuario ha iniciado sesión -->
            <li><a href="{{ route('profile') }}" class="button primary fit">My profile</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </li>
        @endguest
    </ul>
</nav>
