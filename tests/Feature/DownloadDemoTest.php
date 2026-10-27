<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class DownloadDemoTest extends TestCase
{
    public function testCsvDownloadHeadersAndBody(): void
    {
        $response = $this->handleRequest('GET', '/download-demo');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/csv; charset=UTF-8', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('attachment; filename="fastd-demo.csv"', $response->getHeaderLine('Content-Disposition'));
        $this->assertSame("id,name\n1,fastd\n2,demo\n", (string) $response->getBody());
    }
}
