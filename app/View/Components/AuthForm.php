<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthForm extends Component
{
    public string $title;
    public string $route;
    public string $buttonText;
    public array $fields;

    public function __construct(string $title, string $route, string $buttonText, array $fields)
    {
        $this->title = $title;
        $this->route = $route;
        $this->buttonText = $buttonText;
        $this->fields = $fields;
    }

    public function render()
    {
        return view('components.auth-form');
    }
}
