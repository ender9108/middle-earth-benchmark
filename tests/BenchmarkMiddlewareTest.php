<?php

namespace Tests\EnderLab;

use EnderLab\BenchmarkMiddleware;
use PHPUnit\Framework\TestCase;

class BenchmarkMiddlewareTest extends TestCase
{
    public function testBuildInstance()
    {
        $bench = BenchmarkMiddleware::getInstance(
            BenchmarkMiddleware::START_TAG,
            [
                'time'        => true,
                'memory'      => true,
                'memory_peak' => true,
                'report_on'   => [
                    'type' => BenchmarkMiddleware::REPORT_ON_LOGGER,
                    'logger' => null
                ]
            ]
        );
        $this->assertInstanceOf(BenchmarkMiddleware::class, $bench);
    }
}
