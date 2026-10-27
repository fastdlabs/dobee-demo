<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class DatabaseTransactionDemoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        @unlink(dirname(__DIR__, 2) . '/runtime/demo.sqlite');
    }

    public function testTransactionRollsBack(): void
    {
        $response = $this->handleRequest('GET', '/db-transaction-demo');
        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($body['before'], $body['after']);
        $this->assertTrue($body['rolled_back']);
    }
}
