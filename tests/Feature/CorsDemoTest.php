<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class CorsDemoTest extends TestCase
{
    public function testOptionsPreflight(): void
    {
        $response = $this->handleRequest('OPTIONS', '/cors-demo', [], [
            'Origin' => 'https://demo.example',
            'Access-Control-Request-Method' => 'GET',
        ]);

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('*', $response->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertSame('GET, OPTIONS', $response->getHeaderLine('Access-Control-Allow-Methods'));
    }

    public function testGetResponseHasCorsHeaders(): void
    {
        $response = $this->handleRequest('GET', '/cors-demo', [], [
            'Origin' => 'https://demo.example',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('*', $response->getHeaderLine('Access-Control-Allow-Origin'));
    }
}
