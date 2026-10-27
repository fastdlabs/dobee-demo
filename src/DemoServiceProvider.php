<?php

declare(strict_types=1);

namespace FastD\Demo;

use FastD\Application;
use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;
use FastD\Demo\Support\InjectionDemoGreeting;
use FastD\Http\Session\Handler\NativeFileSessionHandler;
use FastD\Http\Session\Handler\SwooleTableSessionHandler;
use FastD\Http\Session\SessionMiddleware;

/**
 * Demo 包入口：绑定 Demo 服务，并为 Session 示例注册独立会话中间件。
 *
 * 宿主应用只需在 config/service.php 中条件注册本 Provider，
 * 不安装 Demo 包时不会引入任何路由或中间件。
 */
final class DemoServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->add(InjectionDemoGreeting::class, new InjectionDemoGreeting('Hello FastD'));

        $this->registerSessionMiddleware($container);
    }

    private function registerSessionMiddleware(Container $container): void
    {
        if ($container->has('session.middleware')) {
            return;
        }

        $runtime = method_exists($container, 'getRuntime') ? (string) $container->getRuntime() : 'cgi';
        $root = $container instanceof Application ? $container->getRootPath() : getcwd();
        $ttl = 1440;

        $handler = 'swoole' === $runtime
            ? new SwooleTableSessionHandler($ttl, 2048, 4096)
            : new NativeFileSessionHandler($root . '/runtime/session', $ttl);

        $container->add('session.handler', $handler);

        $middleware = new SessionMiddleware(
            $handler,
            'FASTD_SESSID',
            '/',
            '',
            false,
            true,
            'Lax',
            function (string $message, object $session) use ($container): void {
                if ($container->has('logger')) {
                    $container->get('logger')->error($message, [
                        'session_id' => method_exists($session, 'getId') ? $session->getId() : null,
                    ]);
                }
            },
            100,
        );

        $container->add('session.middleware', $middleware);
        $container->get('matcher')->push($middleware);
    }
}
