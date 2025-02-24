<?php

use App\Models\Forum\Foro;

it('can fetch a single forum', function () {
    $foro = Foro::factory()->create();

    $response = $this->getJson("/api/foros/{$foro->id}");

    $response->assertStatus(200)
    ->assertJson([
        'data' => [
            'id' => $foro->id,
            'titulo' => $foro->titulo,
        ]
    ]);
});

it('can fetch all forums', function () {
    $initialCount = Foro::count();
    Foro::factory()->count(3)->create();

    $expectedCount = $initialCount + 3;

    $response = $this->getJson('/api/foros');

    $response->assertStatus(200)
        ->assertJson(fn ($json) =>
        $json->has('data')
        ->has('links')
        ->has('meta', fn ($meta) =>
        $meta->hasAll([
            'current_page', 'from', 'last_page', 'links',
            'path', 'per_page', 'to', 'total'
        ])
        ->where('total', fn ($total) => $total >= $expectedCount)
        )
        );
});


