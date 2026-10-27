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

    public function testDemoIndexEmbedsPreviewAndRuntimeConfig(): void
    {
        $response = $this->handleRequest('GET', '/demo/index.html');
        $body = (string) $response->getBody();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('id="demo-frame"', $body);
        $this->assertStringContainsString('id="runtime-switch"', $body);
        $this->assertStringContainsString('window.__DEMO_RUNTIME__', $body);
        $this->assertStringContainsString('/demo/runtime.js', $body);
        $this->assertStringContainsString('swoole_base', $body);
    }

    public function testRuntimeScriptIsServedAsJavaScript(): void
    {
        $response = $this->handleRequest('GET', '/demo/runtime.js');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('application/javascript', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('fastd.demo.runtime', (string) $response->getBody());
    }
}
