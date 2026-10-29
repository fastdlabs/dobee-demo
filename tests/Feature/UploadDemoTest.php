<?php

declare(strict_types=1);

namespace FastD\Demo\Tests\Feature;

use FastD\Http\Request\ServerRequest;
use FastD\Http\Request\UploadedFile;
use FastD\Testing\TestCase;

class UploadDemoTest extends TestCase
{
    public function testUploadedFileIsMovedToRuntimeDirectory(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'fastd-upload-');
        file_put_contents($tmp, 'hello upload');

        $upload = new UploadedFile(
            'demo.txt',
            'text/plain',
            $tmp,
            filesize($tmp),
            UPLOAD_ERR_OK,
        );

        $request = (new ServerRequest('POST', '/upload-demo'))
            ->withUploadedFiles(['file' => $upload]);

        $response = container()->dispatch($request);
        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('demo.txt', $body['name']);
        $this->assertSame('hello upload', file_get_contents(
            dirname(__DIR__, 2) . '/runtime/uploads/' . $body['saved_as']
        ));

        @unlink(dirname(__DIR__, 2) . '/runtime/uploads/' . $body['saved_as']);
    }

    public function testMissingFileReturnsBadRequest(): void
    {
        $response = $this->handleRequest('POST', '/upload-demo');

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testOversizedFileIsRejected(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'fastd-upload-large-');
        file_put_contents($tmp, 'x');

        $upload = new UploadedFile(
            'large.txt',
            'text/plain',
            $tmp,
            1_048_577,
            UPLOAD_ERR_OK,
        );

        $response = container()->dispatch(
            (new ServerRequest('POST', '/upload-demo'))->withUploadedFiles(['file' => $upload])
        );

        $this->assertSame(413, $response->getStatusCode());
        @unlink($tmp);
    }

    public function testUnsupportedMediaTypeIsRejected(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'fastd-upload-type-');
        file_put_contents($tmp, 'pdf');

        $upload = new UploadedFile(
            'demo.pdf',
            'application/pdf',
            $tmp,
            3,
            UPLOAD_ERR_OK,
        );

        $response = container()->dispatch(
            (new ServerRequest('POST', '/upload-demo'))->withUploadedFiles(['file' => $upload])
        );

        $this->assertSame(415, $response->getStatusCode());
        @unlink($tmp);
    }
}
