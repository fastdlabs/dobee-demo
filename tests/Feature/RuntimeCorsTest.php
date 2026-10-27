<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class RuntimeCorsTest extends TestCase
{
    public function testOptionsPreflightForDemoApi(): void
    {
        $response = $this->handleRequest('OPTIONS', '/session', [], [
            'Origin' => 'http://127.0.0.1:8099',
            'Access-Control-Request-Method' => 'GET',
            'Access-Control-Request-Headers' => 'Content-Type',
        ]);

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('http://127.0.0.1:8099', $response->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertSame('true', $response->getHeaderLine('Access-Control-Allow-Credentials'));
        $this->assertSame('Content-Type', $response->getHeaderLine('Access-Control-Allow-Headers'));
    }

    public function testDemoApiResponseHasCorsHeaders(): void
    {
        $response = $this->handleRequest('GET', '/session', [], [
            'Origin' => 'http://127.0.0.1:8099',
        ]);

        $this->assertSame('http://127.0.0.1:8099', $response->getHeaderLine('Access-Control-Allow-Origin'));
    }
}
