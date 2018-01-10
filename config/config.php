<?php
return [
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