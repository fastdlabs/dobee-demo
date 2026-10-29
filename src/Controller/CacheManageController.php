<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 缓存管理示例：写入、读取、删除和 miss 状态。
 */
final class CacheManageController implements MiddlewareInterface
{
    private const KEY = 'demo.cache.managed';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cache = cache('file');
        $action = (string) ($request->getQueryParams()['action'] ?? 'get');
        $item = $cache->getItem(self::KEY);

        if ($action === 'set') {
            $item->set('value-' . time());
            $item->expiresAfter(60);
            $cache->save($item);
            $item = $cache->getItem(self::KEY);
        } elseif ($action === 'delete') {
            $cache->deleteItem(self::KEY);
            $item = $cache->getItem(self::KEY);
        }

        return json([
            'action' => $action,
            'key' => self::KEY,
            'hit' => $item->isHit(),
            'value' => $item->isHit() ? $item->get() : null,
        ]);
    }
}
