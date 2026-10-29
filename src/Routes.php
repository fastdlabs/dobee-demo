<?php

declare(strict_types=1);

namespace FastD\Demo;

use FastD\Demo\Controller\AiMockStreamController;
use FastD\Demo\Controller\CacheDemoController;
use FastD\Demo\Controller\DatabaseDemoController;
use FastD\Demo\Controller\SwooleDemoController;
use FastD\Demo\Controller\UploadDemoController;
use FastD\Demo\Controller\InjectionDemoController;
use FastD\Demo\Controller\MiddlewareDemoController;
use FastD\Demo\Controller\CacheManageController;
use FastD\Demo\Controller\ConfigDemoController;
use FastD\Demo\Controller\CorsDemoController;
use FastD\Demo\Controller\DatabaseTransactionDemoController;
use FastD\Demo\Controller\DownloadDemoController;
use FastD\Demo\Controller\CookieDemoController;
use FastD\Demo\Controller\ErrorDemoController;
use FastD\Demo\Controller\EventDemoController;
use FastD\Demo\Controller\PageController;
use FastD\Demo\Controller\ParallelDemoController;
use FastD\Demo\Controller\RequestDemoController;
use FastD\Demo\Controller\ResponseDemoController;
use FastD\Demo\Controller\SessionController;
use FastD\Demo\Controller\SessionSecurityController;
use FastD\Demo\Controller\SseStreamController;
use FastD\Demo\Middleware\CorsDemoMiddleware;
use FastD\Demo\Middleware\ExceptionDemoMiddleware;
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
            ['GET', '/session-security-demo', SessionSecurityController::class],
            ['POST', '/session-security-demo', SessionSecurityController::class],
            ['GET', '/request-demo', RequestDemoController::class],
            ['POST', '/request-demo', RequestDemoController::class],
            ['GET', '/cookie-demo', CookieDemoController::class],
            ['GET', '/event-demo', EventDemoController::class],
            ['GET', '/response-demo', ResponseDemoController::class],
            ['GET', '/error-demo', ErrorDemoController::class, [ExceptionDemoMiddleware::class]],
            ['GET', '/config-demo', ConfigDemoController::class],
            ['GET', '/cors-demo', CorsDemoController::class, [CorsDemoMiddleware::class]],
            ['OPTIONS', '/cors-demo', CorsDemoController::class, [CorsDemoMiddleware::class]],
            ['GET', '/download-demo', DownloadDemoController::class],
            ['GET', '/db-transaction-demo', DatabaseTransactionDemoController::class],
            ['POST', '/upload-demo', UploadDemoController::class],
            ['GET', '/di-demo', InjectionDemoController::class, [InjectionDemoMiddleware::class]],
            ['GET', '/middleware-demo', MiddlewareDemoController::class, [RequestIdMiddleware::class]],
            ['GET', '/sse', SseStreamController::class],
            ['POST', '/ai/mock-stream', AiMockStreamController::class],
            ['GET', '/cache-demo', CacheDemoController::class],
            ['GET', '/cache-manage-demo', CacheManageController::class],
            ['GET', '/db-demo', DatabaseDemoController::class],
            ['GET', '/swoole-demo', SwooleDemoController::class],
            ['GET', '/parallel-demo', ParallelDemoController::class],
            ['GET', '/demo/{page}', PageController::class],
        ];
    }
}
