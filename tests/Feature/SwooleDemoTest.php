<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class SwooleDemoTest extends TestCase
{
    public function testSwooleRuntimeInfoIsReturned(): void
    {
        $response = $this->handleRequest('GET', '/swoole-demo');
        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('testcase', $body['runtime']);
        $this->assertArrayHasKey('swoole_loaded', $body);
    }
}
