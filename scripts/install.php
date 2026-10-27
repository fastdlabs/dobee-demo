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
 * 读取并校验配置，然后只替换 marker 区块，最后原子写回。
 *
 * @param callable(array): bool|null $validate
 * @return bool true when changed
 */
$replaceBlock = static function (string $file, string $name, string $block, ?callable $validate = null): bool {
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

    // 先执行一次用户配置，确认它返回合法数组且结构可用。
    $bufferLevel = ob_get_level();
    ob_start();

    try {
        $config = include $file;
    } catch (Throwable $throwable) {
        ob_end_clean();
        fwrite(STDERR, "skip: failed to load {$file}: {$throwable->getMessage()}\n");
        return false;
    }

    while (ob_get_level() > $bufferLevel) {
        ob_end_clean();
    }

    if (!is_array($config) || ($validate !== null && !$validate($config))) {
        fwrite(STDERR, "skip: invalid config structure in {$file}\n");
        return false;
    }

    $updated = preg_replace($pattern, '$1' . $block . "\n" . '$3', $content, 1);

    if (!is_string($updated) || $updated === $content) {
        return false;
    }

    // 原子写回，避免写入中断损坏用户配置。
    $temporary = $file . '.tmp.' . bin2hex(random_bytes(6));
    if (file_put_contents($temporary, $updated) === false) {
        fwrite(STDERR, "skip: cannot write temporary config for {$file}\n");
        return false;
    }

    $permissions = fileperms($file);
    if ($permissions !== false) {
        @chmod($temporary, $permissions & 0777);
    }

    if (!@rename($temporary, $file)) {
        @unlink($temporary);
        fwrite(STDERR, "skip: cannot replace {$file}\n");
        return false;
    }

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

if ($replaceBlock(
    $configDir . '/service.php',
    'service',
    $serviceBlock,
    static fn (array $config): bool => is_array($config)
)) {
    fwrite(STDOUT, "updated config/service.php demo block\n");
}

if ($replaceBlock(
    $configDir . '/swoole.php',
    'swoole',
    $swooleBlock,
    static fn (array $config): bool => isset($config['servers']) && is_array($config['servers'])
)) {
    fwrite(STDOUT, "updated config/swoole.php demo block\n");
}
