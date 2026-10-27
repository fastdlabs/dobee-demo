<?php

declare(strict_types=1);

namespace FastD\Demo\Middleware;

use FastD\Http\Response\Text;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Demo API CORS：允许 CGI 页面请求 Swoole API。
 */
final class DemoCorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $origin = $request->getHeaderLine('Origin');
        $allowHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

        $response = $request->getMethod() === 'OPTIONS'
            ? new Text(204)
            : $handler->handle($request);

        $headers = [
            'Access-Control-Allow-Origin' => $origin !== '' ? $origin : '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => $allowHeaders !== '' ? $allowHeaders : 'Content-Type, X-Demo, X-Requested-With, X-Request-Id',
            'Vary' => 'Origin',
        ];

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        if (!$response instanceof ResponseInterface) {
            throw new \RuntimeException('CORS response is not a PSR-7 response.');
        }

        return $response;
    }
}
