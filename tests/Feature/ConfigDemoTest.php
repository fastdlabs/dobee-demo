<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class ConfigDemoTest extends TestCase
{
    public function testRuntimeAndApplicationInfo(): void
    {
        $response = $this->handleRequest('GET', '/config-demo');
        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('testcase', $body['runtime']);
        $this->assertSame('fastd-dobee-demo-tests', $body['name']);
        $this->assertArrayHasKey('swoole_loaded', $body);
    }
}
