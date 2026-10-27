<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class InjectionDemoTest extends TestCase
{
    public function testControllerAndMiddlewareAreResolvedWithConstructorInjection(): void
    {
        $response = $this->handleRequest('GET', '/di-demo');

        $this->isSuccessful($response);
        $this->assertSame('Hello FastD from constructor injection', $response->getHeaderLine('X-DI-Greeting'));

        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame('Hello FastD from constructor injection', $body['message']);
        $this->assertSame(\FastD\Demo\Controller\InjectionDemoController::class, $body['controller']);
        $this->assertSame(\FastD\Demo\Support\InjectionDemoGreeting::class, $body['dependency']);
    }
}
