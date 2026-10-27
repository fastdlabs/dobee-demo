<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Demo\Event\DemoRequestEvent;
use FastD\Demo\Listener\App\DemoRequestEventListener;
use FastD\Testing\TestCase;

class EventDemoTest extends TestCase
{
    public function testCustomEventIsDispatchedToListener(): void
    {
        $response = $this->handleRequest('GET', '/event-demo');
        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(DemoRequestEvent::class, $body['event']);
        $this->assertSame('hello from controller', $body['message']);
        $this->assertSame([DemoRequestEventListener::class], $body['handlers']);
    }
}
