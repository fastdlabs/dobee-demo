<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use FastD\Http\Session\SessionInterface;
use FastD\Http\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Session 安全示例：CSRF 校验与注销。
 */
final class SessionSecurityController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::ATTRIBUTE);
        $body = (array) ($request->getParsedBody() ?? []);
        $action = (string) ($body['action'] ?? $request->getQueryParams()['action'] ?? 'info');

        if ($action === 'logout') {
            $oldId = $session->getId();
            $session->invalidate();

            return json([
                'action' => 'logout',
                'invalidated' => true,
                'old_session_id' => $oldId,
            ]);
        }

        if ($action === 'verify') {
            $token = (string) ($body['_token'] ?? $request->getHeaderLine('X-CSRF-Token'));
            $valid = $session->isTokenValid($token);

            return json([
                'action' => 'verify',
                'valid' => $valid,
                'token_rotated' => $valid ? $session->regenerateToken() : null,
            ], $valid ? 200 : 403);
        }

        return json([
            'action' => 'info',
            'session_id' => $session->getId(),
            'token' => $session->getToken(),
        ]);
    }
}
