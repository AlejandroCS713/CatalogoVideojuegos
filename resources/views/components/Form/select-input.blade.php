@props(['id', 'wireModel', 'options', 'placeholder' => null])

<select id="{{ $id }}"
        wire:model="{{ $wireModel }}"
        multiple
    {{ $attributes->merge(['class' => 'form-select w-full p-2 text-black border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200', 'style' => 'min-height: 100px;']) }}>
    @if($placeholder)
        <option value="" disabled selected>{{ $placeholder }}</option>
    @endif
    @foreach($options as $option)
        <option value="{{ $option->id }}">{{ $option->nombre }}</option>
    @endforeach
</select>
