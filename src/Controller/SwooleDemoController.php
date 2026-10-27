<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Swoole 运行时示例：在 CGI/Swoole 下展示当前运行时信息。
 */
final class SwooleDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return json([
            'swoole_loaded' => extension_loaded('swoole'),
            'runtime' => container()->getRuntime(),
            'pid' => getmypid(),
            'sapi' => PHP_SAPI,
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? null,
        ]);
    }
}
