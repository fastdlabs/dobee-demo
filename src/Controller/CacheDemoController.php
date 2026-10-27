<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 缓存示例：使用 fastd/cache 的 file adapter 记录访问次数。
 */
final class CacheDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cache = cache('file');
        $item = $cache->getItem('demo.cache.counter');
        $hit = $item->isHit();
        $count = $hit ? (int) $item->get() + 1 : 1;

        $item->set($count);
        $item->expiresAfter(60);
        $cache->save($item);

        return json([
            'adapter' => 'file',
            'key' => 'demo.cache.counter',
            'hit' => $hit,
            'count' => $count,
            'expires_after' => 60,
        ]);
    }
}
