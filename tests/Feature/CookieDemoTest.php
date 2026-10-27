<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class CookieDemoTest extends TestCase
{
    public function testCookieIsEmittedAndReadOnNextRequest(): void
    {
        $first = $this->handleRequest('GET', '/cookie-demo');
        $firstBody = json_decode((string) $first->getBody(), true);

        $this->assertSame(1, $firstBody['visits']);
        $this->assertSame('1', $first->getCookies()['demo_visits']->getValue());

        $second = $this->handleRequest('GET', '/cookie-demo', [], ['Cookie' => 'demo_visits=1']);
        $secondBody = json_decode((string) $second->getBody(), true);

        $this->assertSame(2, $secondBody['visits']);
        $this->assertSame('2', $second->getCookies()['demo_visits']->getValue());
    }
}
