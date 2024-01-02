<?php

namespace SnappDoctor\CircuitBreaker;

use Illuminate\Cache\CacheManager;
use Illuminate\Support\Arr;

class CircuitBreakerManager
{
    private CacheManager $manager;
    private array $config;

    /** @var array<CircuitBreaker> */
    protected array $services = [];

    public function __construct(CacheManager $manager, array $config) {
        $this->manager = $manager;
        $this->config = $config;
    }

    public function service(string $service, array $config = []): CircuitBreaker
    {
        return $this->findOrCreateCircuitBreaker($service, $config);
    }

    private function findOrCreateCircuitBreaker(string $service, array $config): CircuitBreaker
    {
        $config = Arr::only(array_merge($this->config, $config), Config::KEYS);
        $circuitConfig = Config::fromArray($config);
        $this->services[$service] ??= new CircuitBreaker($service, $circuitConfig, $this->manager->store($circuitConfig->store));

        return $this->services[$service];
    }
}
