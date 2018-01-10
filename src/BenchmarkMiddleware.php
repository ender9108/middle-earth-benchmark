<?php

namespace EnderLab\Benchmark;

use EnderLab\Benchmark\Formatter\MessageFormatter;
use EnderLab\Benchmark\Formatter\TimeFormatter;
use EnderLab\Benchmark\Formatter\ByteFormatter;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class BenchmarkMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    const START_TAG = 'START';

    /**
     * @var string
     */
    const END_TAG = 'END';

    /**
     * @var array
     */
    private $defaultOptions = [
        'benchmark.time.format'     => [
            'format'                => TimeFormatter::SECOND,
            'precision'             => 2
        ],
        'benchmark.date.format'     => 'Y-m-d',
        'benchmark.datetime.format' => 'Y-m-d H:i:s',
        'benchmark.byte.precision'  => 2,
        'benchmark.logger'          => [
            'instance'              => null,
            'log_level'             => null
        ],
        'benchmark.formatter'       => [
            'instance'              => 'EnderLab\\Benchmark\\Formatter\\DefaultMessageFormatter',
            'template'              => '{{{DATETIME}}} - time : {{{TIME}}} - memory : {{{MEMORY}}} (peak {{{MEMORY_PEAK}}})'
        ]
    ];

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var \SplQueue
     */
    private $queue;

    /**
     * @var BenchmarkMiddleware
     */
    private static $instance;

    /**
     * @param string $tag
     * @param array  $options
     *
     * @return BenchmarkMiddleware
     */
    public static function getInstance(string $tag = self::START_TAG, array $options = []): self
    {
        if (null !== self::$instance) {
            self::$instance->setFlagControl($tag, $options);
        }

        if (null === self::$instance) {
            self::$instance = new self($tag, $options);
        }

        return self::$instance;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $requestHandler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        $response = $requestHandler->handle($request);

        if (!$this->queue->isEmpty()) {
            $options = $this->queue->dequeue();
            $formatter = $this->buildFormatterInstance($options['benchmark.formatter']);
            $message = $formatter->format(
                $options['benchmark.formatter']['template'],
                $options['values']
            );
            $logger = $this->buildLoggerInstance($options['benchmark.logger']);
            $logger->log(
                $options['benchmark.logger']['log_level'],
                $message
            );
        }

        return $response;
    }

    /**
     * @param string $tag
     * @param array  $options
     */
    public function setFlagControl(string $tag, array $options = []): void
    {
        $this->mergeOptions($options);

        $this->queue->enqueue([
            'tag'                   => $tag,
            'values'                => [
                '{{{DATE}}}'        => date($this->options['benchmark.date.format']),
                '{{{DATETIME}}}'    => date($this->options['benchmark.datetime.format']),
                '{{{TIME}}}'        => TimeFormatter::format(
                    microtime(true),
                    $this->options['benchmark.time.format']['format'],
                    $this->options['benchmark.time.format']['precision']
                ),
                '{{{MEMORY}}}'      => ByteFormatter::format(
                    memory_get_usage(),
                    $this->options['benchmark.byte.precision']
                ),
                '{{{MEMORY_PEAK}}}' => ByteFormatter::format(
                    memory_get_peak_usage(),
                    $this->options['benchmark.byte.precision']
                )
            ]
        ]);
    }

    /**
     * BenchmarkMiddleware constructor.
     *
     * @param string $tag
     * @param array  $options
     */
    private function __construct(string $tag = self::START_TAG, array $options = [])
    {
        $this->queue = new \SplQueue();
        $this->options = $this->defaultOptions;
        $this->setFlagControl($tag, $options);
    }

    /**
     * @param array $options
     */
    private function mergeOptions(array $options): void
    {
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->defaultOptions)) {
                $this->options[$key] = $value;
            }
        }
    }

    /**
     * @param array $options
     *
     * @return MessageFormatter
     */
    private function buildFormatterInstance(array $options): MessageFormatter
    {
        $formatter = $options['instance'];

        if (is_string($formatter) && false === class_exists($formatter)) {
            throw new \InvalidArgumentException('');
        }
        $formatter = new $formatter();

        if (!$formatter instanceof MessageFormatter) {
            throw new \InvalidArgumentException('Formatter must be implement MessageFormatter interface');
        }

        return $formatter;
    }

    /**
     * @param array $options
     *
     * @return LoggerInterface
     */
    private function buildLoggerInstance(array $options): LoggerInterface
    {
        $logger = $options['instance'];

        if (is_string($logger) && false === class_exists($logger)) {
            throw new \InvalidArgumentException('');
        }
        $logger = new $logger();

        if (!$logger instanceof LoggerInterface) {
            throw new \InvalidArgumentException('Logger must be implement LoggerInterface interface');
        }

        return $logger;
    }
}
