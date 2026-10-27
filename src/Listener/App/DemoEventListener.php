<?php

declare(strict_types=1);

namespace FastD\Demo\Listener\App;

use FastD\Demo\Support\InjectionDemoGreeting;
use FastD\Event\BootedEvent;
use FastD\Event\EventListener;
use Psr\Log\LoggerInterface;

/**
 * 应用事件监听 Demo：由 DemoServiceProvider 从容器解析并注册。
 */
final class DemoEventListener extends EventListener
{
    public function __construct(
        private LoggerInterface $logger,
        private InjectionDemoGreeting $greeting,
    ) {
    }

    public function listen(): iterable
    {
        return [BootedEvent::class];
    }

    public function process(object $event): void
    {
        $this->logger->debug('demo event listener', [
            'event' => $event::class,
            'message' => $this->greeting->message(),
        ]);
    }
}
