<?php

use App\Models\users\User;
use Illuminate\Support\Facades\Hash;

it('can access the foroum index page', function () {
    $response = $this->get(route('forum.index'));
    $response->assertStatus(200);
});
