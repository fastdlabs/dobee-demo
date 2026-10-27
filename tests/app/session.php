<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

return [
    'enabled' => true,
    'handler' => 'auto',
    'ttl' => 60,
    'file' => ['path' => $root . '/runtime/session'],
    'swoole_table' => ['size' => 64, 'max_data_size' => 1024],
    'gc_probability' => 100,
];
