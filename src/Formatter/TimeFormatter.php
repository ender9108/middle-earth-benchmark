<?php

namespace EnderLab\Benchmark\Formatter;

final class TimeFormatter
{
    const MILLISECOND = 0;
    const SECOND = 1;
    const MINUTE = 2;
    const HOUR = 3;

    /**
     * @param float $value
     * @param int   $type
     * @param int   $precision
     *
     * @return string
     */
    public static function format(float $value, int $type = self::SECOND, int $precision = 2): string
    {
        $formattedValue = '';

        switch ($type) {
            case self::MILLISECOND:
                $formattedValue = number_format(($value * 1000), $precision, '.', ' ');
                break;
            case self::SECOND:
                $formattedValue = number_format($value, $precision, '.', ' ');
                break;
            case self::MINUTE:
                $formattedValue = number_format(($value / 60), $precision, '.', ' ');
                break;
            case self::HOUR:
                $formattedValue = number_format(($value / 60 / 60), $precision, '.', ' ');
                break;
        }

        return $formattedValue;
    }
}
