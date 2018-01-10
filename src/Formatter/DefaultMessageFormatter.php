<?php
namespace EnderLab\Benchmark\Formatter;

class DefaultMessageFormatter implements MessageFormatter
{
    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    public function format(string $template, array $params): string
    {
        return strtr($template, $params);
    }
}