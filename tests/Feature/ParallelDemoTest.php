<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class ParallelDemoTest extends TestCase
{
    public function testParallelDemoUsesRuntimeAwareParallelApi(): void
    {
        $response = $this->handleRequest('GET', '/parallel-demo');
        $body = json_decode((string) $response->getBody(), true);

        if (!class_exists('FastD\\Swoole\\Concurrency\\Parallel')) {
            $this->assertSame(503, $response->getStatusCode());
            $this->assertArrayHasKey('error', $body);
            return;
        }

        $this->assertSame(200, $response->getStatusCode());
        $this->assertFalse($body['concurrent']);
        $this->assertArrayHasKey('elapsed_ms', $body);
        $this->assertSame(['user', 'orders', 'notifications'], array_keys($body['data']));
    }
}
