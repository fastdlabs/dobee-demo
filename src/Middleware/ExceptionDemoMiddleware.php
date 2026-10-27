<?php

declare(strict_types=1);

namespace FastD\Demo\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * 异常捕获示例：把未处理异常转换为统一 JSON 错误响应。
 */
final class ExceptionDemoMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $throwable) {
            return json([
                'error' => 'demo exception',
                'type' => $throwable::class,
                'message' => $throwable->getMessage(),
            ], 500);
        }
    }
}
