<?php

declare(strict_types=1);

namespace FastD\Demo\Listener\Process;

use FastD\Swoole\Listener\Process\SignalEventListener;

/**
 * 进程信号示例：由 config/process.php 的 listener 注册。
 */
final class DemoSignalListener extends SignalEventListener
{
    protected function onUserSignal(object $event): void
    {
        echo sprintf("[demo] signal=%s pid=%s\n", $event->event ?? 'unknown', $event->args['pid'] ?? '-');
    }
}
