<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

return [
    'file' => [
        'adapter' => ['class' => \Symfony\Component\Cache\Adapter\FilesystemAdapter::class],
        'namespace' => '',
        'lifetime' => 60,
        'directory' => $root . '/runtime/cache',
    ],
    'xmCache' => [
        'enable' => false,
    ],
];
