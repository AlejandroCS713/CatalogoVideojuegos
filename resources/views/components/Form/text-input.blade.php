
@props(['id', 'wireModel', 'placeholder' => null])

<input type="text"
       id="{{ $id }}"
       wire:model="{{ $wireModel }}"
       placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'form-input w-full p-2 text-black border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200']) }}>
