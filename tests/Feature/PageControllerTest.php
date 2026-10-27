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
}
