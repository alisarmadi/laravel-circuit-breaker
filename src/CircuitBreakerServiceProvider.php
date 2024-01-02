<?php

namespace SnappDoctor\CircuitBreaker;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Cache\CacheManager;

class CircuitBreakerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/circuit-breaker.php' => config_path('config.php'),
        ], 'circuit-breaker');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/circuit-breaker.php', 'circuit-breaker'
        );
    }

    public function register()
    {
        $this->app->bind(CircuitBreaker::class, function() {
            $circuitConfig = Config::fromArray(config('circuit-breaker'));
            $cacheManager = app(CacheManager::class);
            return new CircuitBreaker('default-service', $circuitConfig, $cacheManager->store($circuitConfig->getStore()));
        });
    }
}
