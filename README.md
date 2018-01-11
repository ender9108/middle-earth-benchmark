# psr15-benchmark

[![Build Status](https://travis-ci.org/ender9108/middle-earth-benchmark.svg?branch=master)](https://travis-ci.org/ender9108/middle-earth-benchmark)
[![Coverage Status](https://coveralls.io/repos/github/ender9108/middle-earth-benchmark/badge.svg?branch=master)](https://coveralls.io/github/ender9108/middle-earth-benchmark?branch=master)
[![Latest Stable Version](https://poser.pugx.org/enderlab/middle-earth-benchmark/v/stable)](https://packagist.org/packages/enderlab/middle-earth-benchmark)
[![Total Downloads](https://poser.pugx.org/enderlab/middle-earth-benchmark/downloads)](https://packagist.org/packages/enderlab/middle-earth-benchmark)
[![License](https://poser.pugx.org/enderlab/middle-earth-benchmark/license)](https://packagist.org/packages/enderlab/middle-earth-benchmark)


## Get started

```php
<?php
use \EnderLab\BenchmarkMiddleware;

$app->pipe(BenchmarkMiddleware::getInstance(
    BenchmarkMiddleware::START_TAG,
    [
        'benchmark.time.format'     => [
            'format'                => TimeFormatter::SECOND,
            'precision'             => 2
        ],
        'benchmark.date.format'     => 'Y-m-d',
        'benchmark.datetime.format' => 'Y-m-d H:i:s',
        'benchmark.byte.precision'  => 2,
        'benchmark.logger'          => [
            'instance'              => new Logger('test'), /* LoggerInterface default null */
            'log_level'             => LogLevel::DEBUG     /* default LogLevel::DEBUG */
        ],
        'benchmark.formatter'       => [
            'instance'              => 'EnderLab\\Benchmark\\Formatter\\DefaultMessageFormatter',
            'template'              => '{{{DATETIME}}} - time : {{{TIME}}} - memory : {{{MEMORY}}} (peak {{{MEMORY_PEAK}}})'
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