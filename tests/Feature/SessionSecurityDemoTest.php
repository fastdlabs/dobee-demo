<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Testing\TestCase;

class SessionSecurityDemoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(\FastD\Http\Session\SessionMiddleware::class)) {
            $this->markTestSkipped('fastd/http session support is required.');
        }

        $this->cleanupSessionFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanupSessionFiles();
        parent::tearDown();
    }

    public function testCsrfVerificationAndLogout(): void
    {
        $info = $this->handleRequest('GET', '/session-security-demo');
        $infoBody = json_decode((string) $info->getBody(), true);

        $this->assertSame(200, $info->getStatusCode());
        $this->assertSame('info', $infoBody['action']);
        $this->assertArrayHasKey('token', $infoBody);

        preg_match('/FASTD_SESSID=([a-f0-9]{32})/', $info->getHeaderLine('Set-Cookie'), $matches);
        $this->assertArrayHasKey(1, $matches);
        $cookie = 'FASTD_SESSID=' . $matches[1];

        $valid = $this->handleRequest('POST', '/session-security-demo', [
            'action' => 'verify',
            '_token' => $infoBody['token'],
        ], ['Cookie' => $cookie]);
        $validBody = json_decode((string) $valid->getBody(), true);

        $this->assertSame(200, $valid->getStatusCode());
        $this->assertTrue($validBody['valid']);
        $this->assertNotSame($infoBody['token'], $validBody['token_rotated']);

        $invalid = $this->handleRequest('POST', '/session-security-demo', [
            'action' => 'verify',
            '_token' => $infoBody['token'],
        ], ['Cookie' => $cookie]);
        $invalidBody = json_decode((string) $invalid->getBody(), true);

        $this->assertSame(403, $invalid->getStatusCode());
        $this->assertFalse($invalidBody['valid']);

        $logout = $this->handleRequest('POST', '/session-security-demo', [
            'action' => 'logout',
        ], ['Cookie' => $cookie]);
        $logoutBody = json_decode((string) $logout->getBody(), true);

        $this->assertSame(200, $logout->getStatusCode());
        $this->assertTrue($logoutBody['invalidated']);
        $this->assertSame($matches[1], $logoutBody['old_session_id']);
    }

    private function cleanupSessionFiles(): void
    {
        foreach (glob(dirname(__DIR__, 2) . '/runtime/session/*') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}
