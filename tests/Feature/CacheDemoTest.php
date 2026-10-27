<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class CacheDemoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (glob(dirname(__DIR__, 2) . '/runtime/cache/*') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    public function testCacheCounterIncreasesOnSecondRequest(): void
    {
        $first = json_decode((string) $this->handleRequest('GET', '/cache-demo')->getBody(), true);
        $second = json_decode((string) $this->handleRequest('GET', '/cache-demo')->getBody(), true);

        $this->assertSame(1, $first['count']);
        $this->assertSame(2, $second['count']);
        $this->assertTrue($second['hit']);
    }
}
