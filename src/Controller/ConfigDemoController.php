<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 应用配置 / 运行时信息示例。
 */
final class ConfigDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $application = container();

        return json([
            'name' => $application->getName(),
            'timezone' => $application->getTimezone(),
            'runtime' => $application->getRuntime(),
            'root' => $application->getRootPath(),
            'sapi' => PHP_SAPI,
            'swoole_loaded' => extension_loaded('swoole'),
        ]);
    }
}
