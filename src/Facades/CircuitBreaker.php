<?php

namespace SnappDoctor\CircuitBreaker\Facades;

use SnappDoctor\CircuitBreaker\CircuitBreakerManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see CircuitBreakerManager
* @method static CircuitBreaker service(string $service, array $config) Retrieve a circuit breaker service from the cache by service name.
 */
class CircuitBreaker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CircuitBreakerManager::class;
    }
}
