<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use FastD\Http\Response\Text;
use FastD\Http\Stream\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 文件下载 / 流式响应示例。
 */
final class DownloadDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $stream = new Stream('php://temp', 'w+b');
        $stream->write("id,name\n1,fastd\n2,demo\n");
        $stream->rewind();

        $response = (new Text(200, ''))
            ->withHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->withHeader('Content-Disposition', 'attachment; filename="fastd-demo.csv"')
            ->withBody($stream);

        if (!$response instanceof ResponseInterface) {
            throw new \RuntimeException('Download response is not a PSR-7 response.');
        }

        return $response;
    }
}
