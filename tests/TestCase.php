<?php

namespace SnappDoctor\CircuitBreaker\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SnappDoctor\CircuitBreaker\CircuitBreakerServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CircuitBreakerServiceProvider::class,
        ];
    }
}
