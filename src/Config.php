<?php

namespace SnappDoctor\CircuitBreaker;

class Config
{
    private string $store;
    private int $timeWindow;
    private int $errorThreshold;
    private int $errorTimeout;
    private int $halfOpenTimeout;
    private int $successThreshold;

    public const KEYS = [
        'store',
        'time_window',
        'error_threshold',
        'error_timeout',
        'half_open_timeout',
        'success_threshold',
    ];

    public function __construct(
        string $store,
        int $timeWindow,
        int $errorThreshold,
        int $errorTimeout,
        int $halfOpenTimeout,
        int $successThreshold
    ) {
        $this->store = $store;
        $this->timeWindow = $timeWindow;
        $this->errorThreshold = $errorThreshold;
        $this->errorTimeout = $errorTimeout;
        $this->halfOpenTimeout = $halfOpenTimeout;
        $this->successThreshold = $successThreshold;
    }

    public static function fromArray(array $config)
    {
        return new self(
            data_get($config, 'store'),
            data_get($config, 'time_window'),
            data_get($config, 'error_threshold'),
            data_get($config, 'error_timeout'),
            data_get($config, 'half_open_timeout'),
            data_get($config, 'success_threshold'),
        );
    }

    /**
     * @return string
     */
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * @return int
     */
    public function getTimeWindow(): int
    {
        return $this->timeWindow;
    }

    /**
     * @return int
     */
    public function getErrorThreshold(): int
    {
        return $this->errorThreshold;
    }

    /**
     * @return int
     */
    public function getErrorTimeout(): int
    {
        return $this->errorTimeout;
    }

    /**
     * @return int
     */
    public function getHalfOpenTimeout(): int
    {
        return $this->halfOpenTimeout;
    }

    /**
     * @return int
     */
    public function getSuccessThreshold(): int
    {
        return $this->successThreshold;
    }
}
