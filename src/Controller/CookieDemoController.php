<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use FastD\Http\Cookie;
use FastD\Http\Response\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Cookie 读写示例：首次写入，下一次请求累加。
 */
final class CookieDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $visits = (int) ($request->getCookieParams()['demo_visits'] ?? 0) + 1;

        $response = (new Json(200, ['visits' => $visits]))
            ->withCookie(Cookie::create('demo_visits', (string) $visits, 3600));

        if (!$response instanceof ResponseInterface) {
            return json(['visits' => $visits]);
        }

        return $response;
    }
}
