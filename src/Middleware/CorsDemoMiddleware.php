<?php

declare(strict_types=1);

namespace FastD\Demo\Middleware;

use FastD\Http\Response\Text;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * CORS / OPTIONS 预检示例。
 */
final class CorsDemoMiddleware implements MiddlewareInterface
{
    private const HEADERS = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, X-Demo',
        'Access-Control-Max-Age' => '3600',
    ];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $request->getMethod() === 'OPTIONS'
            ? new Text(204)
            : $handler->handle($request);

        foreach (self::HEADERS as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        if (!$response instanceof ResponseInterface) {
            throw new \RuntimeException('CORS response is not a PSR-7 response.');
        }

        return $response;
    }
}
