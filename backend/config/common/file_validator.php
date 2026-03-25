<?php

declare(strict_types=1);

use App\Components\Storage\PhotoFileValidator;
use App\Components\Storage\VideoFileValidator;

return [
    PhotoFileValidator::class => static fn (): PhotoFileValidator => new PhotoFileValidator(
        allowedMimeTypes: [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            'image/heic' => 'heic',
            'image/heif' => 'heif',
        ],
        maxFileSize: 30 * 1024 * 1024,
    ),

    VideoFileValidator::class => static fn (): VideoFileValidator => new VideoFileValidator(
        allowedMimeTypes: [
            'video/mp4'       => 'mp4',
            'video/webm'      => 'webm',
            'video/quicktime' => 'mov',
        ],
        maxFileSize: 500 * 1024 * 1024,
    ),
];
