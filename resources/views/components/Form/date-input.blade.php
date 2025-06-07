
@props(['id', 'wireModel'])

<input type="date"
       id="{{ $id }}"
       wire:model="{{ $wireModel }}"
    {{ $attributes->merge(['class' => 'form-input w-full p-2 text-black border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200']) }}>
