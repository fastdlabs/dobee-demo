<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * HTTP 请求读取示例：query、header、cookie、parsed body。
 */
final class RequestDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return json([
            'method' => $request->getMethod(),
            'path' => $request->getUri()->getPath(),
            'query' => $request->getQueryParams(),
            'headers' => [
                'user-agent' => $request->getHeaderLine('User-Agent'),
                'x-demo' => $request->getHeaderLine('X-Demo'),
            ],
            'cookies' => $request->getCookieParams(),
            'body' => $request->getParsedBody(),
        ]);
    }
}
