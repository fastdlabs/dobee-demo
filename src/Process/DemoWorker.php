<?php

declare(strict_types=1);

namespace FastD\Demo\Process;

use FastD\Demo\Support\InjectionDemoGreeting;
use FastD\Swoole\Process\Worker;

/**
 * 进程示例：由 DemoServiceProvider 注册到 ProcessRegistry，php bin/process 启动。
 */
final class DemoWorker extends Worker
{
    public function __construct(private InjectionDemoGreeting $greeting)
    {
        parent::__construct('demo');
    }

    public function process(Worker $worker): void
    {
        echo sprintf(
            "[demo] worker started: %s (pid=%d) %s\n",
            $worker->name,
            getmypid(),
            $this->greeting->message()
        );

        \Swoole\Timer::tick(10_000, static function (): void {
            echo sprintf("[demo] worker heartbeat: %s\n", date('H:i:s'));
        });

        \Swoole\Event::wait();
    }
}
