<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

return [
    'default' => [
        'type' => 'sqlite',
        'database' => $root . '/runtime/demo.sqlite',
    ],
];
