<?php

declare(strict_types=1);

namespace FastD\Demo;

use FastD\Demo\Controller\AiMockStreamController;
use FastD\Demo\Controller\InjectionDemoController;
use FastD\Demo\Controller\MiddlewareDemoController;
use FastD\Demo\Controller\PageController;
use FastD\Demo\Controller\SessionController;
use FastD\Demo\Controller\SseStreamController;
use FastD\Demo\Middleware\InjectionDemoMiddleware;
use FastD\Demo\Middleware\RequestIdMiddleware;

/**
 * Demo 路由定义，由宿主应用按需合并。
 */
final class Routes
{
    /**
     * @return array<int, array{0: string, 1: string, 2: string, 3?: array<int, string>}>
     */
    public static function all(): array
    {
        return [
            ['GET', '/session', SessionController::class],
            ['GET', '/di-demo', InjectionDemoController::class, [InjectionDemoMiddleware::class]],
            ['GET', '/middleware-demo', MiddlewareDemoController::class, [RequestIdMiddleware::class]],
            ['GET', '/sse', SseStreamController::class],
            ['POST', '/ai/mock-stream', AiMockStreamController::class],
            ['GET', '/demo/{page}', PageController::class],
        ];
    }
}
