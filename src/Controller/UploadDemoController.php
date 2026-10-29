<?php

declare(strict_types=1);

namespace FastD\Demo\Controller;

use FastD\Http\Request\UploadedFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 文件上传示例：读取 multipart 文件并保存到 runtime/uploads。
 */
final class UploadDemoController implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $file = $request->getUploadedFiles()['file'] ?? null;

        if (!$file instanceof UploadedFile || $file->getError() !== UPLOAD_ERR_OK) {
            return json(['error' => 'file is required'], 400);
        }

        if ((int) $file->getSize() > 1_048_576) {
            return json(['error' => 'file exceeds 1MB limit'], 413);
        }

        $allowedTypes = ['text/plain', 'text/csv', 'image/png', 'image/jpeg'];
        if (!in_array((string) $file->getClientMediaType(), $allowedTypes, true)) {
            return json(['error' => 'unsupported media type'], 415);
        }

        $clientName = basename((string) $file->getClientFilename());
        $targetName = bin2hex(random_bytes(8)) . '-' . $clientName;
        $target = dirname(__DIR__, 2) . '/runtime/uploads/' . $targetName;

        $file->moveTo($target);

        return json([
            'name' => $clientName,
            'size' => $file->getSize(),
            'media_type' => $file->getClientMediaType(),
            'saved_as' => $targetName,
        ]);
    }
}
