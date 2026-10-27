<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Http\Request\ServerRequest;
use FastD\Testing\TestCase;

class RequestDemoTest extends TestCase
{
    public function testRequestDataIsExposed(): void
    {
        $request = (new ServerRequest('POST', '/request-demo'))
            ->withQueryParams(['hello' => 'world'])
            ->withHeader('X-Demo', 'request-demo')
            ->withCookieParams(['demo' => 'cookie'])
            ->withParsedBody(['name' => 'fastd']);

        $response = container()->dispatch($request);
        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('POST', $body['method']);
        $this->assertSame('/request-demo', $body['path']);
        $this->assertSame(['hello' => 'world'], $body['query']);
        $this->assertSame('request-demo', $body['headers']['x-demo']);
        $this->assertSame(['demo' => 'cookie'], $body['cookies']);
        $this->assertSame(['name' => 'fastd'], $body['body']);
    }
}
