@props(['id', 'wireModel', 'placeholder' => null])

<textarea id="{{ $id }}"
          wire:model="{{ $wireModel }}"
          placeholder="{{ $placeholder }}"
          {{ $attributes->merge(['class' => 'form-textarea w-full p-2 text-black border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200', 'style' => 'min-height: 100px;']) }}>{{ $slot }}</textarea>
