<?php

declare(strict_types=1);

/**
 * 在原配置文件的 marker 区块内追加 Demo 配置。
 *
 * 不新增配置文件，不覆盖 marker 外的用户自定义内容。
 */

$project = getcwd() ?: dirname(__DIR__, 4);
$configDir = $project . '/config';

/**
 * @return bool true when changed
 */
$replaceBlock = static function (string $file, string $name, string $block): bool {
    if (!is_file($file)) {
        fwrite(STDERR, "skip: {$file} not found\n");
        return false;
    }

    $content = (string) file_get_contents($file);
    $pattern = '/(\/\/ demo:' . preg_quote($name, '/') . ':start\n)(.*?)(\/\/ demo:' . preg_quote($name, '/') . ':end)/s';

    if (preg_match($pattern, $content) !== 1) {
        fwrite(STDERR, "skip: marker demo:{$name} not found in {$file}\n");
        return false;
    }

    $updated = preg_replace($pattern, '$1' . $block . "\n" . '$3', $content, 1);

    if (!is_string($updated) || $updated === $content) {
        return false;
    }

    file_put_contents($file, $updated);

    return true;
};

$serviceBlock = <<<'PHP'
if (class_exists(\FastD\Demo\DemoServiceProvider::class)) {
    $services[] = \FastD\Demo\DemoServiceProvider::class;
}
PHP;

$swooleBlock = <<<'PHP'
if (class_exists(\FastD\Demo\Listener\Server\DemoServerListener::class)) {
    $servers['stats'] = [
        'host' => '127.0.0.1',
        'port' => 9528,
        'listener' => \FastD\Swoole\Listener\Server\StatsListener::class,
    ];
    $servers['demo'] = [
        'host' => '127.0.0.1',
        'port' => 9529,
        'listener' => \FastD\Demo\Listener\Server\DemoServerListener::class,
    ];
}
PHP;

if ($replaceBlock($configDir . '/service.php', 'service', $serviceBlock)) {
    fwrite(STDOUT, "updated config/service.php demo block\n");
}

if ($replaceBlock($configDir . '/swoole.php', 'swoole', $swooleBlock)) {
    fwrite(STDOUT, "updated config/swoole.php demo block\n");
}
