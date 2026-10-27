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
    public function testCommandWorkerAndListenerAreRegistered(): void
    {
        $commands = container()->get('commands')->all();
        $processes = container()->get('processes');

        $this->assertContains(DemoCommand::class, $commands);
        $this->assertArrayHasKey('demo', $processes->workers());
        $this->assertContains($processes->workers()['demo'], [container()->get(DemoWorker::class)]);
        $this->assertContains(container()->get(DemoSignalListener::class), $processes->listeners());
    }

    public function testCommandIsConstructedFromContainer(): void
    {
        $tester = new CommandTester(container()->get(DemoCommand::class));
        $tester->execute([]);

        $this->assertStringContainsString('Hello FastD from constructor injection', $tester->getDisplay());
    }
}
