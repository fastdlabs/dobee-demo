<?php

declare(strict_types=1);

namespace FastD\Demo\Middleware;

use FastD\Demo\Support\InjectionDemoGreeting;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class InjectionDemoMiddleware implements MiddlewareInterface
{
    public function __construct(private InjectionDemoGreeting $greeting)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request)->withHeader('X-DI-Greeting', $this->greeting->message());
    }
}
