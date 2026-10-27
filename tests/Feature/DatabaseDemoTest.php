<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class DatabaseDemoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        @unlink(dirname(__DIR__, 2) . '/runtime/demo.sqlite');
    }

    public function testDatabaseWritesAndCountsRows(): void
    {
        $first = json_decode((string) $this->handleRequest('GET', '/db-demo')->getBody(), true);
        $second = json_decode((string) $this->handleRequest('GET', '/db-demo')->getBody(), true);

        $this->assertSame('sqlite', $first['driver']);
        $this->assertSame(1, $first['visits']);
        $this->assertSame(2, $second['visits']);
    }
}
