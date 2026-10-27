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
    $config = include $swooleFile;

    if (is_array($config)) {
        $servers = (array) ($config['servers'] ?? []);
        unset($servers['stats'], $servers['demo']);

        $export = static function (mixed $value, int $level = 0) use (&$export): string {
            if (is_array($value)) {
                if ($value === []) {
                    return '[]';
                }

                $isList = array_is_list($value);
                $indent = str_repeat('    ', $level);
                $childIndent = str_repeat('    ', $level + 1);
                $lines = ['['];

                foreach ($value as $key => $item) {
                    $prefix = $isList ? '' : var_export($key, true) . ' => ';
                    $lines[] = $childIndent . $prefix . $export($item, $level + 1) . ',';
                }

                $lines[] = $indent . ']';

                return implode("\n", $lines);
            }

            if (is_string($value) && str_contains($value, '\\')) {
                return '\\' . $value . '::class';
            }

            return var_export($value, true);
        };

        $content = "<?php\n\n";
        $content .= "// 由 fastd/dobee-demo 安装脚本自动维护。\n";
        $content .= '$servers = ' . $export($servers) . ";\n\n";
        $content .= "if (class_exists(\\FastD\\Demo\\Listener\\Server\\DemoServerListener::class)) {\n";
        $content .= "    \$servers['stats'] = [\n";
        $content .= "        'host' => '127.0.0.1',\n";
        $content .= "        'port' => 9528,\n";
        $content .= "        'handler' => \\FastD\\Swoole\\Listener\\Server\\StatsListener::class,\n";
        $content .= "    ];\n";
        $content .= "    \$servers['demo'] = [\n";
        $content .= "        'host' => '127.0.0.1',\n";
        $content .= "        'port' => 9529,\n";
        $content .= "        'handler' => \\FastD\\Demo\\Listener\\Server\\DemoServerListener::class,\n";
        $content .= "    ];\n";
        $content .= "}\n\n";
        $content .= "return [\n";
        $content .= "    'servers' => \$servers,\n";
        $content .= "    'events' => " . $export((array) ($config['events'] ?? []), 1) . ",\n";
        $content .= "    'options' => " . $export((array) ($config['options'] ?? []), 1) . ",\n";
        $content .= "];\n";

        if ($content !== (string) file_get_contents($swooleFile)) {
            file_put_contents($swooleFile, $content);
            fwrite(STDOUT, "synced demo listeners to config/swoole.php\n");
        }
    }
}
