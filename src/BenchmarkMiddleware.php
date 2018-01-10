<?php
namespace EnderLab\Benchmark;

use EnderLab\Formatter\ByteFormatter;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

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
        'time'          => true,
        'memory'        => true,
        'memory_peak'   => true,
        'logger'        => [
            'instance'  => null,
            'log_level' => LogLevel::DEBUG
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
    public static function getInstance(string $tag = self::START_TAG, array $options = []): BenchmarkMiddleware
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
     * @param ServerRequestInterface    $request
     * @param RequestHandlerInterface   $requestHandler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        $response = $requestHandler->process($request);

        if (!$this->queue->isEmpty()) {
            $options = $this->queue->dequeue();

            if (isset($options['options']['logger']['instance']) &&
                $options['options']['logger']['instance'] instanceof LoggerInterface
            ) {
                $logger = $options['options']['logger']['instance'];
                $message = $this->buildMessage($options);

                $logger->log(
                    (isset($options['options']['logger']['log_level']) ?
                    $options['options']['logger']['log_level'] :
                    LogLevel::DEBUG),
                    $message
                );
            }
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
            'tag'         => $tag,
            'options'     => $this->options,
            'time'        => microtime(true),
            'memory'      => memory_get_usage(),
            'memory_peak' => memory_get_peak_usage()
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
        $this->setFlagControl($tag, $options);
    }

    /**
     * @param array $options
     *
     * @return string
     */
    private function buildMessage(array $options): string
    {
        $message = $options['tag'] . ' - ';

        if (isset($options['options']['time']) && $options['options']['time']) {
            $message .= 'time : ' . (microtime(true) - $options['time']) . ' - ';
        }

        if (isset($options['options']['memory']) && $options['options']['memory']) {
            $message .= 'memory : ' . ByteFormatter::format($options['memory']) . ' - ';
        }

        if (isset($options['options']['memory_peak']) && $options['options']['memory_peak']) {
            $message .= 'memory peak : ' . ByteFormatter::format($options['memory_peak']) . ' - ';
        }

        $message = rtrim($message, ' - ');

        return $message;
    }

    /**
     * @param array $options
     *
     * @return void
     */
    private function mergeOptions(array $options): void
    {
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->defaultOptions)) {
                $this->options[$key] = $value;
            }
        }
    }
}
