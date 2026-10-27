<?php

declare(strict_types=1);

/**
 * 生成 Demo 专用配置，不修改用户已有的 service.php / swoole.php。
 *
 * 生成文件：
 * - config/service-demo.php
 * - config/swoole-demo.php
 */

$project = getcwd() ?: dirname(__DIR__, 4);

$configDir = $project . '/config';
if (!is_dir($configDir)) {
    fwrite(STDERR, "skip: config directory not found in {$project}\n");
    exit(0);
}

$serviceDemo = $configDir . '/service-demo.php';
$serviceContent = <<<'PHP'
<?php

// 由 fastd/dobee-demo 安装脚本生成，请勿手动修改。
return [
    \FastD\Demo\DemoServiceProvider::class,
];
PHP;

if (@file_get_contents($serviceDemo) !== $serviceContent . PHP_EOL) {
    file_put_contents($serviceDemo, $serviceContent . PHP_EOL);
    fwrite(STDOUT, "generated config/service-demo.php\n");
}

$swooleDemo = $configDir . '/swoole-demo.php';
$swooleContent = <<<'PHP'
<?php

// 由 fastd/dobee-demo 安装脚本生成，请勿手动修改。
if (!class_exists(\FastD\Demo\Listener\Server\DemoServerListener::class)) {
    return [];
}

return [
    'stats' => [
        'host' => '127.0.0.1',
        'port' => 9528,
        'listener' => \FastD\Swoole\Listener\Server\StatsListener::class,
    ],
    'demo' => [
        'host' => '127.0.0.1',
        'port' => 9529,
        'listener' => \FastD\Demo\Listener\Server\DemoServerListener::class,
    ],
];
PHP;

if (@file_get_contents($swooleDemo) !== $swooleContent . PHP_EOL) {
    file_put_contents($swooleDemo, $swooleContent . PHP_EOL);
    fwrite(STDOUT, "generated config/swoole-demo.php\n");
}
