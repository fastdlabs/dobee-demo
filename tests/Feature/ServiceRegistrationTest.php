<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Demo\Command\DemoCommand;
use FastD\Demo\Listener\Process\DemoSignalListener;
use FastD\Demo\Process\DemoWorker;
use FastD\Testing\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ServiceRegistrationTest extends TestCase
{
    public function testCommandAndProcessAreAddedToConfig(): void
    {
        $commands = container()->config('command');
        $process = container()->config('process');

        $this->assertContains(DemoCommand::class, $commands);
        $this->assertSame(DemoWorker::class, $process['worker']['demo'] ?? null);
        $this->assertContains(DemoSignalListener::class, $process['listener']);
    }

    public function testCommandStillRunsViaNewInstance(): void
    {
        $tester = new CommandTester(new DemoCommand());
        $tester->execute([]);

        $this->assertStringContainsString('Hello from fastd/dobee-demo command', $tester->getDisplay());
    }
}
