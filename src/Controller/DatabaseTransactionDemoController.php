<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

/**
 * 数据库事务示例：写入后主动异常，验证回滚。
 */
final class DatabaseTransactionDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $db = container()->get('medoodb')->getDatabase('default');

        $db->exec('CREATE TABLE IF NOT EXISTS demo_transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            note TEXT NOT NULL,
            created_at TEXT NOT NULL
        )');

        $before = (int) $db->count('demo_transactions');

        try {
            $db->action(function ($db): void {
                $db->insert('demo_transactions', [
                    'note' => 'should be rolled back',
                    'created_at' => date('c'),
                ]);

                throw new RuntimeException('rollback demo');
            });
        } catch (RuntimeException) {
            // expected
        }

        $after = (int) $db->count('demo_transactions');

        return json([
            'before' => $before,
            'after' => $after,
            'rolled_back' => $before === $after,
        ]);
    }
}
