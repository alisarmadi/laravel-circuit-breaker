<?php

namespace SnappDoctor\CircuitBreaker\Tests;

use Carbon\Carbon;
use SnappDoctor\CircuitBreaker\CircuitBreaker;
use SnappDoctor\CircuitBreaker\Config;
use Illuminate\Cache\Repository;

class CircuitBreakerTest extends TestCase
{
    /**
     * @dataProvider configDataProvider
     */
    public function test_circuit_breaker_should_available_and_close(array $configArray): void
    {
        $config = Config::fromArray($configArray);
        $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));

        $this->assertTrue($circuitBreaker->isClose());
        $this->assertTrue($circuitBreaker->isAvailable());
    }

    /**
     * @dataProvider configDataProvider
     */
    public function test_circuit_breaker_can_increment_errors(array $configArray): void
    {
        $config = Config::fromArray($configArray);

        $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $this->assertEquals(2, $circuitBreaker->getErrorsCount());
    }

    /**
     * @dataProvider configDataProvider
     */
    public function test_circuit_breaker_should_restart_errors_after_mark_success_its_in_close_state(array $configArray): void
    {
        $config = Config::fromArray($configArray);

        $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markSuccess();
        $this->assertEquals(0, $circuitBreaker->getErrorsCount());
    }

    /**
     * @dataProvider configDataProvider
     */
    public function test_circuit_breaker_can_be_opened(array $configArray): void
    {
        $config = Config::fromArray($configArray);

        $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $this->assertEquals(5, $circuitBreaker->getErrorsCount());
        $this->assertFalse($circuitBreaker->isAvailable());
        $this->assertTrue($circuitBreaker->isOpen());
        $this->assertFalse($circuitBreaker->isClose());
        $this->assertFalse($circuitBreaker->isHalfOpen());
    }

    /**
     * @dataProvider configDataProvider
     */
    public function test_circuit_breaker_can_be_half_opened(array $configArray): void
    {
        $config = Config::fromArray($configArray);

        $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        Carbon::setTestNow(Carbon::now()->addSeconds(31));
        $this->assertEquals(0, $circuitBreaker->getErrorsCount());
        $this->assertTrue($circuitBreaker->isAvailable());
        $this->assertTrue($circuitBreaker->isHalfOpen());
        Carbon::setTestNow(null);
    }

    /**
     * @dataProvider configDataProvider
     */
    public function test_circuit_breaker_can_open_again_after_the_half_open_mark_failed(array $configArray): void
    {
        $config = Config::fromArray($configArray);

        $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        Carbon::setTestNow(Carbon::now()->addSeconds(31));
        $this->assertEquals(0, $circuitBreaker->getErrorsCount());
        $this->assertTrue($circuitBreaker->isAvailable());
        $this->assertTrue($circuitBreaker->isHalfOpen());
        $circuitBreaker->markFailed();
        $this->assertFalse($circuitBreaker->isAvailable());
        $this->assertFalse($circuitBreaker->isHalfOpen());
        $this->assertTrue($circuitBreaker->isOpen());
        Carbon::setTestNow(null);
    }

    /**
     * @dataProvider configDataProvider
     */
    public function test_circuit_breaker_can_close_after_half_open_mark_success(array $configArray): void
    {
        $config = Config::fromArray($configArray);

        $circuitBreaker = new CircuitBreaker('test', $config, app(Repository::class));
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        $circuitBreaker->markFailed();
        Carbon::setTestNow(Carbon::now()->addSeconds(31));
        $this->assertEquals(0, $circuitBreaker->getErrorsCount());
        $this->assertTrue($circuitBreaker->isAvailable());
        $this->assertTrue($circuitBreaker->isHalfOpen());
        $circuitBreaker->markFailed();
        $this->assertFalse($circuitBreaker->isAvailable());
        $this->assertFalse($circuitBreaker->isHalfOpen());
        $this->assertTrue($circuitBreaker->isOpen());
        $circuitBreaker->markSuccess();
        $circuitBreaker->markSuccess();
        $this->assertTrue($circuitBreaker->isAvailable());
        $this->assertTrue($circuitBreaker->isClose());
        $this->assertFalse($circuitBreaker->isHalfOpen());
        $this->assertFalse($circuitBreaker->isOpen());
        Carbon::setTestNow(null);
    }

    public function configDataProvider(): array
    {
        return [
            [
                'configArray' => [
                    'store' => 'array',
                    'time_window' => 6,
                    'error_threshold' => 5,
                    'error_timeout' => 30,
                    'half_open_timeout' => 15,
                    'success_threshold' => 3,
                ]
            ]
        ];
    }
}
