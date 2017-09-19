<?php

namespace Tests\EnderLab;

use EnderLab\BenchmarkMiddleware;
use EnderLab\Dispatcher\Dispatcher;
use GuzzleHttp\Psr7\ServerRequest;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

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
                'logger'      => [
                    'instance'  => new Logger('test'),
                    'log_level' => LogLevel::DEBUG
                ]
            ]
        );
        $this->assertInstanceOf(BenchmarkMiddleware::class, $bench);
    }

    public function testProcessMiddleware()
    {
        $bench = BenchmarkMiddleware::getInstance(
            BenchmarkMiddleware::START_TAG,
            [
                'time'        => true,
                'memory'      => true,
                'memory_peak' => true,
                'logger'      => [
                    'instance'  => new Logger('test'),
                    'log_level' => LogLevel::DEBUG
                ]
            ]
        );

        $request = new ServerRequest('GET', '/');
        $delegate = new Dispatcher();
        $response = $bench->process($request, $delegate);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
