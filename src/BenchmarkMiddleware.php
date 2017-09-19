<?php

namespace EnderLab;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * @var string
     */
    const REPORT_ON_LOGGER = 'LOG';

    /**
     * @var array
     */
    private $options = [
        'time'        => true,
        'memory'      => true,
        'memory_peak' => true,
        'report_on'   => []
    ];

    /**
     * @var \SplQueue
     */
    private $queue;

    private static $instance;

    public static function getInstance(string $tag = self::START_TAG, array $options = [])
    {
        if (null === self::$instance) {
            self::$instance = new self($tag, $options);
        }

        return self::$instance;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $response = $delegate->process($request);

        return $response;
    }

    private function __construct(string $tag = self::START_TAG, array $options = [])
    {
        $this->queue = new \SplQueue();
        $options = $this->mergeOptions($options);

        $this->queue->enqueue([
            'tag'     => $tag,
            'options' => $options
        ]);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function mergeOptions(array $options): array
    {
        $mergedOptions = $this->options;

        foreach ($options as $key => $value) {
            if (array_key_exists($mergedOptions[$key])) {
                $mergedOptions[$key] = $value;
            }
        }

        return $mergedOptions;
    }
}
