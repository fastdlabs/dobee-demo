<?php

declare(strict_types=1);

namespace FastD\Demo\Listener\Server;

use FastD\Http\Response\Json;
use FastD\Swoole\Listener\Server\RequestListener;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Swoole Server 示例：独立监听端口，直接返回 Swoole worker 信息。
 */
final class DemoServerListener extends RequestListener
{
    public function onRequest(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return new Json(200, [
            'message' => 'FastD Swoole server demo',
            'pid' => getmypid(),
            'path' => $serverRequest->getUri()->getPath(),
            'query' => $serverRequest->getQueryParams(),
        ]);
    }
}
