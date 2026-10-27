<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Http\Request\ServerRequest;
use FastD\Testing\TestCase;

class ResponseDemoTest extends TestCase
{
    public function testDefaultJsonResponse(): void
    {
        $response = $this->handleRequest('GET', '/response-demo');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('default response', json_decode((string) $response->getBody(), true)['message']);
    }

    public function testCreatedAndRedirectResponses(): void
    {
        $created = container()->dispatch(
            (new ServerRequest('GET', '/response-demo'))->withQueryParams(['type' => 'created'])
        );
        $this->assertSame(201, $created->getStatusCode());

        $redirect = container()->dispatch(
            (new ServerRequest('GET', '/response-demo'))->withQueryParams(['type' => 'redirect'])
        );
        $this->assertSame(302, $redirect->getStatusCode());
        $this->assertSame('/demo/index.html', $redirect->getHeaderLine('Location'));
    }
}
