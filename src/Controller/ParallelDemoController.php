<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Swoole 协程并发示例：CGI 串行、Swoole 并发，业务代码保持一致。
 */
final class ParallelDemoController implements MiddlewareInterface
{
    private const PARALLEL = 'FastD\\Swoole\\Concurrency\\Parallel';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!class_exists(self::PARALLEL)) {
            return json([
                'error' => 'Parallel requires fastd/swoole >= 8.2',
            ], 503);
        }

        $startedAt = microtime(true);

        $result = self::PARALLEL::all([
            'user' => fn (): array => $this->mock('user', 0.08),
            'orders' => fn (): array => $this->mock('orders', 0.12),
            'notifications' => fn (): array => $this->mock('notifications', 0.06),
        ], 0.5);

        return json([
            'concurrent' => self::PARALLEL::isConcurrent(),
            'elapsed_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            'data' => $result,
        ]);
    }

    private function mock(string $service, float $seconds): array
    {
        if (self::PARALLEL::isConcurrent()) {
            \Swoole\Coroutine::sleep($seconds);
        } else {
            usleep((int) ($seconds * 1_000_000));
        }

        return [
            'service' => $service,
            'status' => 'ok',
            'delay_ms' => (int) ($seconds * 1000),
        ];
    }
}
