<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use PHPUnit\Framework\TestCase;

class InstallerTest extends TestCase
{
    private string $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->project = sys_get_temp_dir() . '/fastd-demo-installer-' . bin2hex(random_bytes(6));
        mkdir($this->project . '/config', 0777, true);

        file_put_contents($this->project . '/config/service.php', <<<'PHP'
<?php

return [
    \App\ServiceProvider\UserServiceProvider::class,
];
PHP);

        file_put_contents($this->project . '/config/swoole.php', <<<'PHP'
<?php

return [
    'servers' => [
        'http' => [
            'host' => '127.0.0.1',
            'port' => 9527,
            'listener' => \FastD\Listener\Server\SwHttpListener::class,
        ],
        'user_http' => [
            'host' => '127.0.0.1',
            'port' => 9600,
            'listener' => \FastD\Listener\Server\SwHttpListener::class,
        ],
    ],
    'listeners' => [
        \FastD\Swoole\Listener\Server\WorkerListener::class,
    ],
    'options' => [
        'log_level' => 5,
    ],
];
PHP);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->project);
        parent::tearDown();
    }

    public function testInstallerInjectsDemoConfigAndPreservesUserConfig(): void
    {
        [$code, $output] = $this->runInstaller();

        $this->assertSame(0, $code, $output);
        $this->assertStringContainsString('updated config/service.php', $output);
        $this->assertStringContainsString('updated config/swoole.php', $output);

        $service = (string) file_get_contents($this->project . '/config/service.php');
        $swoole = (string) file_get_contents($this->project . '/config/swoole.php');

        $this->assertStringContainsString('demo: fastd/dobee-demo', $service);
        $this->assertStringContainsString('DemoServiceProvider', $service);
        $this->assertStringContainsString('\\App\\ServiceProvider\\UserServiceProvider::class', $service);

        $this->assertStringContainsString('demo: fastd/dobee-demo', $swoole);
        $this->assertStringContainsString("'stats'", $swoole);
        $this->assertStringContainsString("'demo'", $swoole);
        $this->assertStringContainsString("'user_http'", $swoole);
        $this->assertSame(1, substr_count($swoole, "'user_http'"));
        $this->assertFileDoesNotExist($this->project . '/config/service-demo.php');
        $this->assertFileDoesNotExist($this->project . '/config/swoole-demo.php');
    }

    public function testInstallerIsIdempotent(): void
    {
        [$firstCode] = $this->runInstaller();
        $this->assertSame(0, $firstCode);

        $before = hash_file('sha256', $this->project . '/config/swoole.php');
        [$secondCode, $secondOutput] = $this->runInstaller();
        $after = hash_file('sha256', $this->project . '/config/swoole.php');

        $this->assertSame(0, $secondCode);
        $this->assertStringContainsString('already contains demo config', $secondOutput);
        $this->assertSame($before, $after);
        $this->assertSame(1, substr_count((string) file_get_contents($this->project . '/config/swoole.php'), "'stats'"));
    }

    public function testInstallerDoesNotRewriteInvalidConfig(): void
    {
        $file = $this->project . '/config/swoole.php';
        $before = "<?php\n\nreturn [\n";
        file_put_contents($file, $before);

        [$code, $output] = $this->runInstaller();

        $this->assertSame(0, $code, $output);
        $this->assertStringContainsString('invalid config/swoole.php', $output);
        $this->assertSame($before, file_get_contents($file));
    }

    private function runInstaller(): array
    {
        $installer = dirname(__DIR__, 2) . '/scripts/install.php';
        $process = proc_open(
            [PHP_BINARY, $installer],
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $this->project
        );

        $this->assertIsResource($process);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [proc_close($process), (string) $stdout . (string) $stderr];
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        }

        @rmdir($directory);
    }
}
