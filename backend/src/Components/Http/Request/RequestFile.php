<?php

declare(strict_types=1);

namespace App\Components\Http\Request;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

final readonly class RequestFile
{
    private string $path;

    private function __construct(
        private UploadedFileInterface $file,
    ) {
        if ($this->file->getError() !== UPLOAD_ERR_OK) {
            throw new RuntimeException('File upload error code: ' . $this->file->getError());
        }

        $this->path = $this->buildTempPath();
        $this->file->moveTo($this->path);
    }

    public function __destruct()
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }

    public static function extract(ServerRequestInterface $request, string $name): ?self
    {
        $file = $request->getUploadedFiles()[$name] ?? null;

        if (!$file instanceof UploadedFileInterface) {
            return null;
        }

        return new self($file);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getOriginalFile(): UploadedFileInterface
    {
        return $this->file;
    }

    private function buildTempPath(): string
    {
        return sys_get_temp_dir() . '/' . uniqid('upload_', true);
    }
}
