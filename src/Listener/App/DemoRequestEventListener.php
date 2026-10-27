<?php

declare(strict_types=1);

namespace FastD\Demo\Listener\App;

use FastD\Demo\Event\DemoRequestEvent;
use FastD\Event\EventListener;

final class DemoRequestEventListener extends EventListener
{
    public function listen(): iterable
    {
        return [DemoRequestEvent::class];
    }

    public function process(object $event): void
    {
        if ($event instanceof DemoRequestEvent) {
            $event->addHandler(self::class);
        }
    }
}
