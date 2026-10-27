<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use FastD\Http\Response\Redirect;
use FastD\Http\Response\Text;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 响应状态示例：200 / 201 / 204 / 302。
 */
final class ResponseDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return match ((string) ($request->getQueryParams()['type'] ?? 'ok')) {
            'created' => new Text(201, 'created'),
            'empty' => new Text(204),
            'redirect' => new Redirect('/demo/index.html'),
            default => json([
                'message' => 'default response',
                'status' => 200,
            ]),
        };
    }
}
