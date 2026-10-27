<?php

declare(strict_types=1);

namespace FastD\Demo;

use FastD\Cache\ServiceProvider\CacheServiceProvider;
use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;
use FastD\Demo\Command\DemoCommand;
use FastD\Demo\Listener\App\DemoEventListener;
use FastD\Demo\Listener\Process\DemoSignalListener;
use FastD\Demo\Process\DemoWorker;
use FastD\Demo\Support\InjectionDemoGreeting;
use FastD\MedooDB\ServiceProvider\DatabaseServiceProvider;
use FastD\ServiceProvider\SessionServiceProvider;
use Psr\Log\LoggerInterface;

/**
 * Demo 包入口。
 *
 * 宿主只需要条件注册本 Provider；Demo 路由、Session/Cache/Database 服务
 * 和构造器注入绑定都由这里统一装配。
 */
final class DemoServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $this->registerCoreProviders($container);
        $this->registerDemoServices($container);
        $this->registerDemoListener($container);
        $this->registerRoutes($container);
    }

    /**
     * 命令、进程 Worker / Listener 都注册到容器，由 Terminal 从容器解析。
     */
    private function registerDemoServices(Container $container): void
    {
        $container->add(InjectionDemoGreeting::class, new InjectionDemoGreeting('Hello FastD'));
        $container->add(DemoCommand::class, new DemoCommand());
        $container->add(DemoWorker::class, new DemoWorker('demo'));
        $container->add(DemoSignalListener::class, new DemoSignalListener());
    }

    private function registerDemoListener(Container $container): void
    {
        if (!$container->has('event') || !$container->has('logger') || !interface_exists(LoggerInterface::class)) {
            return;
        }

        if (!$container->has(LoggerInterface::class)) {
            $container->add(LoggerInterface::class, $container->get('logger'));
        }

        $container->get('event')->addListener($container->make(DemoEventListener::class));
    }

    private function registerCoreProviders(Container $container): void
    {
        if (!$container->has('session.middleware') && class_exists(SessionServiceProvider::class)) {
            (new SessionServiceProvider())->register($container);
        }

        if (!$container->has('cache') && class_exists(CacheServiceProvider::class)) {
            (new CacheServiceProvider())->register($container);
        }

        if (!$container->has('medoodb') && class_exists(DatabaseServiceProvider::class)) {
            (new DatabaseServiceProvider())->register($container);
        }
    }

    /**
     * Demo 路由随 Provider 自动注册，宿主无需修改 routes/web.php。
     */
    private function registerRoutes(Container $container): void
    {
        if (!$container->has('matcher')) {
            return;
        }

        $matcher = $container->get('matcher');

        if (!property_exists($matcher, 'routeCollection')) {
            return;
        }

        foreach (Routes::all() as $route) {
            $matcher->routeCollection->addRoute(...$route);
        }
    }
}
