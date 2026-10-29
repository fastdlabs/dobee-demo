<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Http\Request\ServerRequest;
use FastD\Testing\TestCase;

class CacheManageDemoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanupCache();
    }

    public function testSetGetDeleteAndMiss(): void
    {
        $set = $this->request('set');
        $this->assertTrue($set['hit']);

        $get = $this->request('get');
        $this->assertTrue($get['hit']);
        $this->assertSame($set['value'], $get['value']);

        $delete = $this->request('delete');
        $this->assertFalse($delete['hit']);
        $this->assertNull($delete['value']);
    }

    /**
     * @return array<string, mixed>
     */
    private function request(string $action): array
    {
        $request = (new ServerRequest('GET', '/cache-manage-demo'))
            ->withQueryParams(['action' => $action]);

        return json_decode((string) container()->dispatch($request)->getBody(), true);
    }

    private function cleanupCache(): void
    {
        $dir = dirname(__DIR__, 2) . '/runtime/cache';

        if (!is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        }
    }
}
