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

    /** @return self[] */
    public static function extractList(ServerRequestInterface $request, string $name): array
    {
        $files = $request->getUploadedFiles()[$name] ?? [];

        if (!is_array($files)) {
            $file = self::extract($request, $name);
            return $file ? [$file] : [];
        }

        $result = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFileInterface && $file->getError() === UPLOAD_ERR_OK) {
                $result[] = new self($file);
            }
        }

        return $result;
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
