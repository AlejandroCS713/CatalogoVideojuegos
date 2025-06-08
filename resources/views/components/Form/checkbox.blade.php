@props(['id' => null, 'name' => null, 'value' => 1, 'checked' => false])

<input
    type="checkbox"
    id="{{ $id }}"
    name="{{ $name }}"
    value="{{ $value }}"
    @if($checked) checked @endif
    class="custom-checkbox"
    {{ $attributes }}
>
