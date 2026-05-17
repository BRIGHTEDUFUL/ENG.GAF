<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Disable CSRF verification in tests — Laravel 13 uses PreventRequestForgery
        $this->withoutMiddleware(PreventRequestForgery::class);
    }
}
