<?php

declare(strict_types=1);

/**
 * 自动将 DemoServiceProvider 写入宿主的 config/service.php。
 *
 * 由宿主 Composer 的 post-install-cmd / post-update-cmd 调用。
 */

$project = getcwd() ?: dirname(__DIR__, 4);
$configFile = $project . '/config/service.php';
$provider = 'FastD\\Demo\\DemoServiceProvider';
$providerConstant = '\\FastD\\Demo\\DemoServiceProvider::class';

if (!is_file($configFile)) {
    fwrite(STDERR, "skip: config/service.php not found in {$project}\n");
    exit(0);
}

$services = include $configFile;

if (!is_array($services)) {
    fwrite(STDERR, "skip: config/service.php must return an array\n");
    exit(0);
}

if (in_array($provider, $services, true) || in_array($providerConstant, $services, true)) {
    exit(0);
}

$services[] = $provider;

$lines = [
    '<?php',
    '',
    '// 服务提供者配置。',
    '//',
    '// 由 fastd/dobee-demo 的安装脚本自动维护。',
    'return [',
];
foreach ($services as $service) {
    $lines[] = '    ' . ($service === $provider ? $providerConstant : var_export($service, true)) . ',';
}
$lines[] = '];';

file_put_contents($configFile, implode(PHP_EOL, $lines) . PHP_EOL);

fwrite(STDOUT, "added {$provider} to config/service.php\n");
