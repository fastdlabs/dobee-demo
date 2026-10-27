<?php

declare(strict_types=1);

/**
 * 在原配置中定点插入 Demo 配置。
 *
 * - 配置本身只保留 return 结构
 * - Demo 注释、配置只在执行本脚本时注入
 * - 用户已有配置和注释保持不变
 * - 原子写回
 */

$project = getcwd() ?: dirname(__DIR__, 4);
$configDir = $project . '/config';

/**
 * @return list<array{id: int|null, text: string, offset: int}>
 */
$tokenize = static function (string $source): array {
    $result = [];
    $offset = 0;

    foreach (token_get_all($source) as $token) {
        if (is_array($token)) {
            $result[] = ['id' => $token[0], 'text' => $token[1], 'offset' => $offset];
            $offset += strlen($token[1]);
            continue;
        }

        $result[] = ['id' => null, 'text' => $token, 'offset' => $offset];
        $offset += strlen($token);
    }

    return $result;
};

/**
 * @param list<array{id: int|null, text: string, offset: int}> $tokens
 */
$matchingBracket = static function (array $tokens, int $openIndex): ?int {
    $depth = 0;

    for ($i = $openIndex, $count = count($tokens); $i < $count; $i++) {
        if ($tokens[$i]['text'] === '[') {
            $depth++;
        } elseif ($tokens[$i]['text'] === ']') {
            $depth--;
            if ($depth === 0) {
                return $i;
            }
        }
    }

    return null;
};

$indentOf = static function (string $source, int $offset): string {
    $lineStart = strrpos(substr($source, 0, $offset), "\n");
    $lineStart = $lineStart === false ? 0 : $lineStart + 1;
    $line = substr($source, $lineStart, $offset - $lineStart);

    return preg_match('/^[ \t]*/', $line, $match) === 1 ? $match[0] : '';
};

/**
 * 在目标数组开头插入块，返回新源码；找不到插入点返回 null。
 */
$insertIntoArray = static function (
    string $source,
    callable $findOpenIndex,
    string $block,
): ?string {
    $tokens = $GLOBALS['tokenize']($source);
    $openIndex = $findOpenIndex($tokens);

    if ($openIndex === null) {
        return null;
    }

    $open = $tokens[$openIndex];
    $lineIndent = $GLOBALS['indentOf']($source, $open['offset']);
    $itemIndent = $lineIndent . '    ';
    $insertAt = $open['offset'] + 1;

    $block = str_replace("\n", "\n" . $itemIndent, $block);

    $injected = "\n"
        . $itemIndent . '// demo: fastd/dobee-demo' . "\n"
        . $itemIndent . $block . "\n"
        . $lineIndent;

    return substr($source, 0, $insertAt) . $injected . substr($source, $insertAt);
};

$readConfig = static function (string $file): ?array {
    $level = ob_get_level();
    ob_start();

    try {
        $config = include $file;
    } catch (Throwable $throwable) {
        ob_end_clean();
        fwrite(STDERR, "skip: failed to load {$file}: {$throwable->getMessage()}\n");
        return null;
    }

    while (ob_get_level() > $level) {
        ob_end_clean();
    }

    return is_array($config) ? $config : null;
};

$atomicWrite = static function (string $file, string $content): bool {
    $temporary = $file . '.tmp.' . bin2hex(random_bytes(6));

    if (file_put_contents($temporary, $content) === false) {
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

// 读取校验
$serviceFile = $configDir . '/service.php';
if (!is_file($serviceFile) || $readConfig($serviceFile) === null) {
    fwrite(STDERR, "skip: invalid config/service.php\n");
} else {
    $source = (string) file_get_contents($serviceFile);

    if (str_contains($source, 'demo: fastd/dobee-demo')) {
        fwrite(STDOUT, "skip: service.php already contains demo config\n");
    } else {
        $block = '...(class_exists(\\FastD\\Demo\\DemoServiceProvider::class) ? [\\FastD\\Demo\\DemoServiceProvider::class] : []),';
        $updated = $insertIntoArray(
            $source,
            static function (array $tokens): ?int {
                foreach ($tokens as $index => $token) {
                    if ($token['id'] === T_RETURN) {
                        for ($i = $index + 1, $count = count($tokens); $i < $count; $i++) {
                            if ($tokens[$i]['text'] === '[') {
                                return $i;
                            }
                            if ($tokens[$i]['id'] !== T_WHITESPACE) {
                                return null;
                            }
                        }
                    }
                }

                return null;
            },
            $block,
        );

        if ($updated === null) {
            fwrite(STDERR, "skip: cannot find return array in config/service.php\n");
        } elseif ($atomicWrite($serviceFile, $updated)) {
            fwrite(STDOUT, "updated config/service.php\n");
        }
    }
}

// Swoole 配置校验
$swooleFile = $configDir . '/swoole.php';
$swooleConfig = is_file($swooleFile) ? $readConfig($swooleFile) : null;

if (!is_array($swooleConfig) || !isset($swooleConfig['servers']) || !is_array($swooleConfig['servers'])) {
    fwrite(STDERR, "skip: invalid config/swoole.php\n");
} else {
    $source = (string) file_get_contents($swooleFile);

    if (str_contains($source, 'demo: fastd/dobee-demo')) {
        fwrite(STDOUT, "skip: swoole.php already contains demo config\n");
    } else {
        $block = <<<'PHP'
...(class_exists(\FastD\Demo\Listener\Server\DemoServerListener::class) ? [
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
] : []),
PHP;

        $updated = $insertIntoArray(
            $source,
            static function (array $tokens): ?int {
                foreach ($tokens as $index => $token) {
                    if ($token['id'] !== T_CONSTANT_ENCAPSED_STRING || trim($token['text'], "'\"") !== 'servers') {
                        continue;
                    }

                    for ($i = $index + 1, $count = count($tokens); $i < $count; $i++) {
                        if ($tokens[$i]['id'] === T_WHITESPACE) {
                            continue;
                        }
                        if ($tokens[$i]['id'] !== T_DOUBLE_ARROW) {
                            return null;
                        }

                        for ($j = $i + 1; $j < $count; $j++) {
                            if ($tokens[$j]['id'] === T_WHITESPACE) {
                                continue;
                            }
                            return $tokens[$j]['text'] === '[' ? $j : null;
                        }
                    }
                }

                return null;
            },
            $block,
        );

        if ($updated === null) {
            fwrite(STDERR, "skip: cannot find servers array in config/swoole.php\n");
        } elseif ($atomicWrite($swooleFile, $updated)) {
            fwrite(STDOUT, "updated config/swoole.php\n");
        }
    }
}
