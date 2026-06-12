<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use Random\RandomException;

final readonly class OrderFileNameGeneratorService
{
    /**
     * @throws RandomException
     */
    public function generate(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $extension = $extension !== '' ? '.' . mb_strtolower($extension) : '';

        return bin2hex(random_bytes(16)) . $extension;
    }
}
