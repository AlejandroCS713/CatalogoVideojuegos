<?php

use App\Models\users\User;
use Illuminate\Support\Facades\Hash;

it('can access the login page', function () {
    $response = $this->get('login');
    $response->assertStatus(200);
});
it('can access the registered page', function () {
    $response = $this->get('register');
    $response->assertStatus(200);
});

