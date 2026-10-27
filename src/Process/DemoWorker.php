<?php

declare(strict_types=1);

namespace FastD\Demo\Process;

use FastD\Swoole\Process\Worker;

/**
 * 进程示例：由 DemoServiceProvider 注册时写入 process 配置，Process 使用 new 实例化。
 */
final class DemoWorker extends Worker
{
    public function process(Worker $worker): void
    {
        echo sprintf("[demo] worker started: %s (pid=%d)\n", $worker->name, getmypid());

        \Swoole\Timer::tick(10_000, static function (): void {
            echo sprintf("[demo] worker heartbeat: %s\n", date('H:i:s'));
        });

        \Swoole\Event::wait();
    }
}
