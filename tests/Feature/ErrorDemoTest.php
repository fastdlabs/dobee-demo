<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Http\Request\ServerRequest;
use FastD\Testing\TestCase;

class ErrorDemoTest extends TestCase
{
    public function testNotFoundResponse(): void
    {
        $response = container()->dispatch(
            (new ServerRequest('GET', '/error-demo'))->withQueryParams(['type' => 'not_found'])
        );

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('not found', json_decode((string) $response->getBody(), true)['error']);
    }

    public function testExceptionIsConvertedToJson500(): void
    {
        $response = $this->handleRequest('GET', '/error-demo');
        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('demo exception', $body['error']);
        $this->assertSame('demo exception', $body['message']);
    }
}
