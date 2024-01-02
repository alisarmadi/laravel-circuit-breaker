# Circuit Breaker Package

This package is a simple implementation of circuit breaker pattern. It protects your application from failures of its service dependencies.

Resources about the circuit breaker pattern:
* [http://martinfowler.com/bliki/CircuitBreaker.html](http://martinfowler.com/bliki/CircuitBreaker.html)

## Installation

- Add this line to require section of the composer.json

```bash
"snapp-doctor/circuit-breaker": "^1.0.5"
```

- And also add this section in the composer.json file:

```bash
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/alisarmadi/laravel-circuit-breaker.git"
    }
]
```

- On the end you should have something like this in your composer.json file:

```bash
...
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/alisarmadi/laravel-circuit-breaker.git"
    }
],
"require": {
    ...
    "snapp-doctor/circuit-breaker": "^1.0.5"
},
...
```

- You can publish the config file with:

```bash
php artisan vendor:publish --tag="circuit-breaker"
```

This is the contents of the published config file:

```php
return [
    // Here you may specify which of your cache stores you wish to use as your default store.
    'store' => config('cache.default'),

    // length of interval (in seconds) over which it calculates the error rate
    'time_window' => 60,

    // the number of errors to encounter within a given timespan before opening the circuit
    'error_threshold' => 10,

    // the amount of time until the circuit breaker will try to query the resource again
    'error_timeout' => 300,

    // the timeout for the circuit when it is in the half-open state
    'half_open_timeout' => 150,

    // the amount of consecutive successes for the circuit to close again
    'success_threshold' => 1,
];
```

## Usage

Your application may have multiple services, so you will have to get a circuit breaker for each service:
```php
use SnappDoctor\CircuitBreaker\CircuitBreaker;

$circuitBreaker = new CircuitBreaker('custom-service', Config::fromArray($configArray), app(Repository::class));

```

#### Three states of circuit breaker

<img src="https://user-images.githubusercontent.com/1885716/53690408-4a7f3d00-3dad-11e9-852c-0e082b7b9636.png" width="500">

You can then determine whether a service is available or not.

```php
// Check circuit status for service
if (! $circuit->isAvailable()) {
    // Service isn't available
}
```
Service is available if it's CLOSED or HALF_OPEN. Then, you should call your service, depending on the response. You can mark it as a success or failure to update the circuit status.

```php
try {
    callAPI();
    $circuit->markSuccess();
} catch (\Exception $e) {
    // If an error occurred, it must be recorded as failed.
    $circuit->markFailed();
}
```


## Testing

```bash
composer test
```
