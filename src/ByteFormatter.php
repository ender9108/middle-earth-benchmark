<?php
namespace EnderLab\Benchmark;

final class ByteFormatter {
    public static function format(int $size, int $precision = 2): string {
        $base = log($size, 1024);
        $suffixes = ['', 'K', 'M', 'G', 'T'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}