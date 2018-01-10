<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 10/01/18
 * Time: 10:25
 */

namespace EnderLab\Benchmark\Formatter;


interface MessageFormatter
{
    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    public function format(string $template, array $params): string;
}