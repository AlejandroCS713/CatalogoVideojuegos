<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('My Achievements') }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1, h3 { color: #333; }
        .logro-item { border-bottom: 1px solid #ddd; padding: 10px 0; }
    </style>
</head>
<body>
<h1>{{ __('My Achievements') }}</h1>
<p>{{ __('Achievements of') }} {{ $user->name }}</p>

<ul>
    @forelse ($logros as $logro)
        <li class="logro-item">
            <h3>{{ $logro->nombre }}</h3>
            <p>{{ $logro->descripcion }}</p>
            <strong>+{{ $logro->puntos }} {{ __('Points') }}</strong>
        </li>
    @empty
        <p>{{ __('You have not unlocked any achievements yet.') }}</p>
    @endforelse
</ul>
</body>
</html>
