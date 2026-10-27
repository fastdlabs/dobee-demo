<?php

declare(strict_types=1);

namespace FastD\Demo\Command;

use FastD\Demo\Support\InjectionDemoGreeting;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 命令行示例：php bin/console demo:hello
 *
 * 命令由 DemoServiceProvider 注册到 CommandRegistry，并从容器解析。
 */
final class DemoCommand extends Command
{
    public function __construct(private InjectionDemoGreeting $greeting)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('demo:hello')
            ->setDescription('FastD demo console command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello from fastd/dobee-demo command: ' . $this->greeting->message());

        return Command::SUCCESS;
    }
}
