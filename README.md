# psr15-benchmark

[![Build Status](https://travis-ci.org/ender9108/middle-earth-benchmark.svg?branch=master)](https://travis-ci.org/ender9108/middle-earth-benchmark)
[![Coverage Status](https://coveralls.io/repos/github/ender9108/middle-earth-benchmark/badge.svg?branch=master)](https://coveralls.io/github/ender9108/middle-earth-benchmark?branch=master)

Benchmark for middleware app

## Get started

```php
<?php
use \EnderLab\BenchmarkMiddleware;

$app->pipe(BenchmarkMiddleware::getInstance(
    BenchmarkMiddleware::START_TAG,
    [
        'time'        => true, /* default true */ 
        'memory'      => true, /* default true */
        'memory_peak' => true, /* default true */
        'logger'      => [
            'instance'  => new Logger('test'), /* LoggerInterface default null */
            'log_level' => LogLevel::DEBUG     /* default LogLevel::DEBUG */
        ]
    ]
));
$app->pipe(/* Other middleware */);
$app->pipe(BenchmarkMiddleware::getInstance('FLAG1'));
$app->pipe(/* Other middleware */);
$app->pipe(/* Other middleware */);
$app->pipe(BenchmarkMiddleware::getInstance(
    BenchmarkMiddleware::END_TAG
));
```