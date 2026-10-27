<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use FastD\Http\Response\Text;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 提供 demo 静态页面：/demo/index.html、/demo/sse.html 等。
 */
final class PageController implements MiddlewareInterface
{
    private const PAGES = ['index.html', 'sse.html', 'ai.html', 'di.html', 'middleware.html', 'cache.html', 'database.html', 'swoole.html', 'parallel.html', 'request.html', 'cookie.html', 'upload.html', 'event.html', 'response.html', 'error.html', 'process.html', 'config.html', 'http-client.html', 'redis-session.html'];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $page = (string) $request->getAttribute('page');

        if (!in_array($page, self::PAGES, true)) {
            return new Text(404, 'Demo page not found');
        }

        $file = dirname(__DIR__, 2) . '/resources/demo/' . $page;

        if (!is_file($file)) {
            return new Text(404, 'Demo page not found');
        }

        return (new Text(200, (string) file_get_contents($file)))
            ->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }
}
