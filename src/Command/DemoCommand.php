<?php

declare(strict_types=1);

namespace FastD\Demo\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 命令行示例：php bin/console demo:hello
 */
final class DemoCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('demo:hello')
            ->setDescription('FastD demo console command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello from fastd/dobee-demo command');

        return Command::SUCCESS;
    }
}
