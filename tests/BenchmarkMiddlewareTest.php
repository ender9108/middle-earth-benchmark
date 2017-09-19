<?php

namespace Tests\EnderLab;

use EnderLab\BenchmarkMiddleware;
use PHPUnit\Framework\TestCase;

class BenchmarkMiddlewareTest extends TestCase
{
    public function testBuildInstance()
    {
        $bench = BenchmarkMiddleware::getInstance();
        $this->assertInstanceOf(BenchmarkMiddleware::class, $bench);
    }
}
