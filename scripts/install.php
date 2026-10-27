<?php

declare(strict_types=1);

/**
 * 自动同步 Demo 所需配置：
 * - config/service.php：写入 DemoServiceProvider
 * - config/swoole.php：写入 Demo 专用监听端口
 *
 * 由宿主 Composer 的 post-install-cmd / post-update-cmd 调用。
 */

$project = getcwd() ?: dirname(__DIR__, 4);

$provider = 'FastD\\Demo\\DemoServiceProvider';
$providerConstant = '\\FastD\\Demo\\DemoServiceProvider::class';

$serviceFile = $project . '/config/service.php';
if (is_file($serviceFile)) {
    $services = include $serviceFile;

    if (is_array($services)
        && !in_array($provider, $services, true)
        && !in_array($providerConstant, $services, true)
    ) {
        $services[] = $provider;

        $lines = [
            '<?php',
            '',
            '// 服务提供者配置。',
            '//',
            '// 由 fastd/dobee-demo 的安装脚本自动维护。',
            '$services = [',
        ];
        foreach ($services as $service) {
            $lines[] = '    ' . ($service === $provider ? $providerConstant : var_export($service, true)) . ',';
        }
        $lines[] = '];';
        $lines[] = '';
        $lines[] = 'return array_values(array_filter($services, static fn (mixed $service): bool => class_exists($service)));';

        file_put_contents($serviceFile, implode(PHP_EOL, $lines) . PHP_EOL);
        fwrite(STDOUT, "added {$provider} to config/service.php\n");
    }
}

$swooleFile = $project . '/config/swoole.php';
if (is_file($swooleFile)) {
    $content = (string) file_get_contents($swooleFile);
    $pattern = '/(\/\/ demo:swoole:start\n)(.*?)(\/\/ demo:swoole:end)/s';

    if (preg_match($pattern, $content) === 1) {
        $block = <<<'SWOOLE'
if (class_exists(\FastD\Demo\Listener\Server\DemoServerListener::class)) {
    $demoListens = [
        [
            'host' => '127.0.0.1',
            'port' => 9528,
            'worker' => \FastD\Swoole\Listener\Server\StatsListener::class,
        ],
        [
            'host' => '127.0.0.1',
            'port' => 9529,
            'worker' => \FastD\Demo\Listener\Server\DemoServerListener::class,
        ],
    ];
}
SWOOLE;

        $updated = preg_replace($pattern, '$1' . $block . "\n" . '$3', $content, 1);
        if (is_string($updated) && $updated !== $content) {
            file_put_contents($swooleFile, $updated);
            fwrite(STDOUT, "added demo listeners to config/swoole.php\n");
        }
    }
}
