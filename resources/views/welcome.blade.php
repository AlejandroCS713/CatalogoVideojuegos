@extends('layouts.app') <!-- Usa el layout base -->

@section('title', 'Forty by HTML5 UP') <!-- Título dinámico -->

@section('body_class', 'is-preload') <!-- Clase para el body -->

@section('content')
    <!-- Header -->
    <header id="header" class="alt">
        <a href="{{ route('welcome') }}" class="logo"><strong>Forty</strong> <span>by HTML5 UP</span></a>
        <nav>
            <a href="#menu">Menu</a>
        </nav>
    </header>

    <!-- Menu -->
    <nav id="menu">
        <ul class="links">
            <li><a href="{{ route('welcome') }}">Home</a></li>
            <li><a href="#one">Features</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <ul class="actions stacked">
            <li><a href="#" class="button primary fit">Get Started</a></li>
            <li><a href="#" class="button fit">Log In</a></li>
        </ul>
    </nav>

    <!-- Banner -->
    <section id="banner" class="major">
        <div class="inner">
            <header class="major">
                <h1>Hi, my name is Forty</h1>
            </header>
            <div class="content">
                <p>A responsive site template designed by HTML5 UP<br />
                    and released under the Creative Commons.</p>
                <ul class="actions">
                    <li><a href="#one" class="button next scrolly">Get Started</a></li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Main -->
    <div id="main">
        <!-- Section One -->
        <section id="one" class="tiles">
            @foreach ([1, 2, 3, 4, 5, 6] as $i)
                <article>
                    <span class="image">
                        <img src="{{ asset('forty/images/pic0'.$i.'.jpg') }}" alt="" />
                    </span>
                    <header class="major">
                        <h3><a href="#" class="link">Heading {{ $i }}</a></h3>
                        <p>Description {{ $i }}</p>
                    </header>
                </article>
            @endforeach
        </section>

        <!-- Section Two -->
        <section id="two">
            <div class="inner">
                <header class="major">
                    <h2>Massa libero</h2>
                </header>
                <p>Nullam et orci eu lorem consequat tincidunt vivamus et sagittis libero. Mauris aliquet magna magna sed
                    nunc rhoncus pharetra. Pellentesque condimentum sem. In efficitur ligula tate urna.</p>
                <ul class="actions">
                    <li><a href="#" class="button next">Get Started</a></li>
                </ul>
            </div>
        </section>
    </div>

    <!-- Contact -->
    <section id="contact">
        <div class="inner">
            <section>
                <form method="post" action="#">
                    <div class="fields">
                        <div class="field half">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" />
                        </div>
                        <div class="field half">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" />
                        </div>
                        <div class="field">
                            <label for="message">Message</label>
                            <textarea name="message" id="message" rows="6"></textarea>
                        </div>
                    </div>
                    <ul class="actions">
                        <li><input type="submit" value="Send Message" class="primary" /></li>
                        <li><input type="reset" value="Clear" /></li>
                    </ul>
                </form>
            </section>
            <section class="split">
                <section>
                    <div class="contact-method">
                        <span class="icon solid alt fa-envelope"></span>
                        <h3>Email</h3>
                        <a href="mailto:info@example.com">info@example.com</a>
                    </div>
                </section>
                <section>
                    <div class="contact-method">
                        <span class="icon solid alt fa-phone"></span>
                        <h3>Phone</h3>
                        <span>(000) 000-0000</span>
                    </div>
                </section>
                <section>
                    <div class="contact-method">
                        <span class="icon solid alt fa-home"></span>
                        <h3>Address</h3>
                        <span>123 Somewhere Road<br />
                            City, Country</span>
                    </div>
                </section>
            </section>
        </div>
    </section>
@endsection
