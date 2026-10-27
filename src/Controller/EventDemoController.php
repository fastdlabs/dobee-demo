<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use FastD\Demo\Event\DemoRequestEvent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 自定义事件示例：控制器触发事件，Listener 处理后回传。
 */
final class EventDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $event = new DemoRequestEvent('hello from controller');
        event()->dispatch($event);

        return json([
            'event' => $event::class,
            'message' => $event->getMessage(),
            'handlers' => $event->getHandlers(),
        ]);
    }
}
