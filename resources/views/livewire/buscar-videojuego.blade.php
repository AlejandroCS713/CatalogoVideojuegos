<div>
    <input type="text" wire:model="query" placeholder="Buscar videojuego..." class="form-control" style="color: black; margin-bottom: 10px;">

    @if (!empty($videojuegos))
        <ul class="list-group">
            @foreach ($videojuegos as $videojuego)
                <li class="list-group-item" wire:click="seleccionarVideojuego({{ $videojuego->id }})">
                    {{ $videojuego->nombre }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
