<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use FastD\Http\Response\Text;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * 提供 demo 静态页面与运行时辅助脚本。
 */
final class PageController implements MiddlewareInterface
{
    private const PAGES = ['index.html', 'sse.html', 'ai.html', 'di.html', 'middleware.html', 'cache.html', 'database.html', 'swoole.html', 'parallel.html', 'request.html', 'cookie.html', 'upload.html', 'event.html', 'response.html', 'error.html', 'process.html', 'config.html', 'http-client.html', 'redis-session.html', 'cors.html', 'download.html', 'db-transaction.html', 'session-security.html', 'cache-manage.html'];

    private const ASSETS = ['runtime.js'];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $page = (string) $request->getAttribute('page');
        $base = dirname(__DIR__, 2) . '/resources/demo/';

        if (in_array($page, self::ASSETS, true)) {
            $file = $base . $page;

            if (!is_file($file)) {
                return new Text(404, 'Demo asset not found');
            }

            return (new Text(200, (string) file_get_contents($file)))
                ->withHeader('Content-Type', 'application/javascript; charset=UTF-8');
        }

        if (!in_array($page, self::PAGES, true)) {
            return new Text(404, 'Demo page not found');
        }

        $file = $base . $page;

        if (!is_file($file)) {
            return new Text(404, 'Demo page not found');
        }

        $html = (string) file_get_contents($file);
        $runtime = json_encode($this->runtimeConfig(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $injection = '<script>window.__DEMO_RUNTIME__ = ' . $runtime . ';</script>'
            . '<script src="/demo/runtime.js"></script>';

        $html = preg_replace('/<head>/i', '<head>' . $injection, $html, 1, $count);

        if ($count === 0) {
            $html = $injection . $html;
        }

        return (new Text(200, $html))
            ->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * @return array{current: string, swoole_base: string}
     */
    private function runtimeConfig(): array
    {
        $host = '127.0.0.1';
        $port = 9527;

        try {
            $swoole = container()->config('swoole');
            $http = $swoole['servers']['http'] ?? [];
            $host = (string) ($http['host'] ?? $host);
            $port = (int) ($http['port'] ?? $port);
        } catch (Throwable) {
            // Keep defaults when swoole config is not part of the host bootstrap.
        }

        if ($host === '0.0.0.0' || $host === '::') {
            $host = '127.0.0.1';
        }

        return [
            'current' => container()->getRuntime(),
            'swoole_base' => sprintf('http://%s:%d', $host, $port),
        ];
    }
}
