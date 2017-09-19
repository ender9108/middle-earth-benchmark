<?php

namespace EnderLab;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
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
        'logger'      => null
    ];

    /**
     * @var \SplQueue
     */
    private $queue;

    private $previousTime;

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

        if (!$this->queue->isEmpty()) {
            $benchOptions = $this->queue->dequeue();

            if ($benchOptions['options']['logger'] instanceof LoggerInterface) {
                $logger = $benchOptions['options']['logger'];
                $message = $benchOptions['tag'] . ' - ';
                $previousTime = ( null === $this->previousTime ) ? microtime(true) : $this->previousTime;

                if (isset($benchOptions['options']['time']) && $benchOptions['options']['time']) {
                    $message .= 'time : ' . ($previousTime - $benchOptions['time']) . ' - ';
                }

                if (isset($benchOptions['options']['memory']) && $benchOptions['options']['memory']) {
                    $message .= 'memory : ' . $benchOptions['memory'] . ' - ';
                }

                if (isset($benchOptions['options']['memory_peak']) && $benchOptions['options']['memory_peak']) {
                    $message .= 'memory peak : ' . $benchOptions['memory_peak'] . ' - ';
                }

                $message = rtrim($message, ' - ');
                $this->previousTime = $benchOptions['time'];

                if ('' !== trim($message)) {
                    $logger->info($message);
                }
            }
        }

        return $response;
    }

    private function __construct(string $tag = self::START_TAG, array $options = [])
    {
        $this->queue = new \SplQueue();
        $options = $this->mergeOptions($options);

        $this->queue->enqueue([
            'tag'         => $tag,
            'options'     => $options,
            'time'        => microtime(true),
            'memory'      => memory_get_usage(),
            'memory_peak' => memory_get_peak_usage()
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
            if (array_key_exists($key, $mergedOptions)) {
                $mergedOptions[$key] = $value;
            }
        }

        return $mergedOptions;
    }
}
