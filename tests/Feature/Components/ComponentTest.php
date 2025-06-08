<?php

use App\Models\games\Genero;
use Illuminate\Support\Facades\Blade;

describe('CheckboxComponent', function () {
    it('renders with correct attributes and is checked when specified', function () {
        $rendered = Blade::render('<x-form.checkbox id="agree" name="terms" :checked="true" />');

        expect($rendered)->toContain('type="checkbox"')
            ->toContain('id="agree"')
            ->toContain('name="terms"')
            ->toContain('checked');
    });

    it('does not have checked attribute when not specified', function () {
        $rendered = Blade::render('<x-form.checkbox id="agree" name="terms" />');

        expect($rendered)->not()->toContain('checked');
    });

    it('renders the Checkbox component as checked when specified', function () {
        $html = view('components.Form.checkbox', [
            'id' => 'agree',
            'name' => 'terms',
            'value' => 1,
            'checked' => true,
        ])->render();

        expect($html)->toContain('type="checkbox"')
            ->toContain('id="agree"')
            ->toContain('name="terms"')
            ->toContain('checked');
    });

    it('renders the Checkbox component unchecked by default', function () {
        $html = view('components.Form.checkbox', [
            'id' => 'agree',
            'name' => 'terms',
            'value' => 1,
            'checked' => false,
        ])->render();

        expect($html)->not->toContain('checked');
    });
});

describe('DateInputComponent', function () {
    it('renders correctly with id and wire:model', function () {
        $rendered = Blade::render('<x-form.date-input id="birthdate" wire:model="user.birthdate" />');

        expect($rendered)->toContain('type="date"')
            ->toContain('id="birthdate"')
            ->toContain('wire:model="user.birthdate"');
    });
    it('renders the DateInput component with id and wire:model', function () {
        $html = view('components.Form.date-input', [
            'id' => 'birthdate',
            'attributes' => new \Illuminate\View\ComponentAttributeBag(['wire:model' => 'user.birthdate']),
        ])->render();

        expect($html)->toContain('type="date"')
            ->toContain('id="birthdate"')
            ->toContain('wire:model="user.birthdate"');
    });
});

describe('LabelComponent', function () {
    it('renders label with correct for attribute and slot', function () {
        $rendered = Blade::render('<x-form.label for="email">Email Address</x-form.label>');

        expect($rendered)->toContain('for="email"')
            ->toContain('Email Address');
    });
    it('renders the Label component with text and for attribute', function () {
        $html = view('components.Form.label', [
            'for' => 'email',
            'slot' => 'Email Address',
        ])->render();

        expect($html)->toContain('for="email"')
            ->toContain('Email Address');
    });
});

describe('SelectComponent', function () {
    it('renders options and placeholder', function () {
        $options = Genero::factory()->count(2)->create();

        $rendered = Blade::render(
            '<x-form.select-input id="genres" wireModel="genre" :options="$options" placeholder="Choose genre" />',
            ['options' => $options]
        );

        expect($rendered)->toContain('<option value="" disabled selected>Choose genre</option>')
            ->toContain($options[0]->nombre)
            ->toContain($options[1]->nombre);
    });

    it('renders the Select component with options and placeholder', function () {
        $options = Genero::factory()->count(2)->create();

        $html = view('components.Form.select-input', [
            'id' => 'genres',
            'wireModel' => 'genre',
            'placeholder' => 'Choose genre',
            'options' => $options,
            'attributes' => new \Illuminate\View\ComponentAttributeBag(),
        ])->render();

        expect($html)->toContain('<option value="" disabled selected>Choose genre</option>')
            ->toContain($options[0]->nombre)
            ->toContain($options[1]->nombre);
    });
});

describe('TextAreaComponent', function () {
    it('renders with id, wire:model and placeholder', function () {
        $rendered = Blade::render(
            '<x-form.text-area id="description" wireModel="game.description" placeholder="Write here...">Initial text</x-form.text-area>'
        );

        expect($rendered)->toContain('id="description"')
            ->toContain('wire:model="game.description"')
            ->toContain('placeholder="Write here..."')
            ->toContain('Initial text');
    });

    it('renders the TextArea component with content and attributes', function () {
        $html = view('components.Form.text-area', [
            'id' => 'description',
            'wireModel' => 'game.description',
            'placeholder' => 'Write here...',
            'slot' => 'Initial text',
            'attributes' => new \Illuminate\View\ComponentAttributeBag(),
        ])->render();

        expect($html)->toContain('id="description"')
            ->toContain('wire:model="game.description"')
            ->toContain('placeholder="Write here..."')
            ->toContain('Initial text');
    });
});

describe('TextInputComponent', function () {
    it('renders with id, wire:model and placeholder', function () {
        $rendered = Blade::render('<x-form.text-input id="title" wireModel="game.title" placeholder="Enter title" />');

        expect($rendered)->toContain('id="title"')
            ->toContain('wire:model="game.title"')
            ->toContain('placeholder="Enter title"');
    });

    it('renders the TextInput component with id, wire:model and placeholder', function () {
        $html = view('components.Form.text-input', [
            'id' => 'title',
            'wireModel' => 'game.title',
            'placeholder' => 'Enter title',
            'attributes' => new \Illuminate\View\ComponentAttributeBag(),
        ])->render();

        expect($html)->toContain('id="title"')
            ->toContain('wire:model="game.title"')
            ->toContain('placeholder="Enter title"');
    });
});
