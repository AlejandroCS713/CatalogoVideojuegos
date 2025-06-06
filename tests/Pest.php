<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

uses(TestCase::class,LazilyRefreshDatabase::class)->in('Feature','Unit');


expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});


function something()
{

}
