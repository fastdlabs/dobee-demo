<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

/**
 * 错误响应示例：
 * - ?type=not_found → 404 JSON
 * - 默认 → 抛出异常，由 ExceptionDemoMiddleware 转为 500 JSON
 */
final class ErrorDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (($request->getQueryParams()['type'] ?? '') === 'not_found') {
            return json(['error' => 'not found'], 404);
        }

        throw new RuntimeException('demo exception');
    }
}
