<?php

declare(strict_types=1);

namespace FastD\Demo\Event;

use FastD\Event\Event;

/**
 * 自定义事件：演示 PSR-14 事件分发。
 */
final class DemoRequestEvent extends Event
{
    /**
     * @param list<string> $handlers
     */
    public function __construct(
        private readonly string $message,
        private array $handlers = [],
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function addHandler(string $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * @return list<string>
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}
