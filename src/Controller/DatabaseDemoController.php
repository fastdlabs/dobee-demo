<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 数据库示例：使用 fastd/medoodb 的 SQLite 连接写入并统计访问次数。
 */
final class DatabaseDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $db = container()->get('medoodb')->getDatabase('default');

        $db->exec('CREATE TABLE IF NOT EXISTS demo_visits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at TEXT NOT NULL
        )');
        $db->insert('demo_visits', ['created_at' => date('c')]);

        return json([
            'driver' => 'sqlite',
            'table' => 'demo_visits',
            'visits' => (int) $db->count('demo_visits'),
        ]);
    }
}
