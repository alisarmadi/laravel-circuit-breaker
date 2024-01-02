<?php

namespace SnappDoctor\CircuitBreaker\Tests;

use SnappDoctor\CircuitBreaker\CircuitBreaker;
use SnappDoctor\CircuitBreaker\Config;
use Illuminate\Cache\Repository;

class CircuitBreakerTest extends TestCase
{
    /** @test */
    public function test_circuit_breaker_should_available_and_close(): void
    {
        dd('sss');
        $config = Config::fromArray([
            'store' => config('cache.default'),
            'time_window' => 6,
            'error_threshold' => 5,
            'error_timeout' => 30,
            'half_open_timeout' => 15,
            'success_threshold' => 3,
        ]);

        $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));

        $this->assertTrue($circuitBreaker->isClose());
        $this->assertTrue($circuitBreaker->isAvailable());
    }
}
