@props(['for'])

<label {{ $attributes->merge(['for' => $for, 'class' => 'block text-gray-700 text-sm font-bold mb-2 cursor-pointer']) }}>
    {{ $slot }}
</label>
