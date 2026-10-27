<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class PageControllerTest extends TestCase
{
    public function testDemoIndexPageIsServed(): void
    {
        $response = $this->handleRequest('GET', '/demo/index.html');

        $this->isSuccessful($response);
        $this->assertStringContainsString('FastD', (string) $response->getBody());
    }

    public function testUnknownPageReturns404(): void
    {
        $response = $this->handleRequest('GET', '/demo/unknown.html');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testDemoIndexExposesRuntimeSwitchAndStartupCommands(): void
    {
        $response = $this->handleRequest('GET', '/demo/index.html');
        $body = (string) $response->getBody();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('id="runtime-switch"', $body);
        $this->assertStringContainsString('php -S 127.0.0.1:8099 -t web', $body);
        $this->assertStringContainsString('php bin/server start', $body);
        $this->assertStringContainsString('Parallel 自动串行', $body);
        $this->assertStringContainsString('Parallel 自动并发', $body);
    }
}
