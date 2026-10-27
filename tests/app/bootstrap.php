<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

return [
    'name' => 'fastd-dobee-demo-tests',
    'root' => $root,
    'timezone' => 'Asia/Shanghai',
    'log' => [
        'path' => $root . '/runtime/logs',
        'level' => \Monolog\Level::Debug,
    ],
    'route' => __DIR__ . '/routes.php',
    'service' => __DIR__ . '/service.php',
    'listener' => __DIR__ . '/listeners.php',
    'cache' => __DIR__ . '/cache.php',
    'database' => __DIR__ . '/database.php',
    'session' => __DIR__ . '/session.php',
];
