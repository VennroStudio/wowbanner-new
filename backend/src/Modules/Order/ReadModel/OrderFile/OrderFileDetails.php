<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderFile;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\ReadModel\OrderFile\Interface\OrderFileModelInterface;
use Override;

final readonly class OrderFileDetails implements OrderFileModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $orderId,
        public string $diskPath,
        public string $fileName,
        public string $originalName,
        public string $createdAt,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'            => 'id',
            'order_id'      => 'order_id',
            'disk_path'     => 'disk_path',
            'file_name'     => 'file_name',
            'original_name' => 'original_name',
            'created_at'    => 'created_at',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     order_id: int,
     *     disk_path: string,
     *     file_name: string,
     *     original_name: string,
     *     created_at: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            orderId: (int)$row['order_id'],
            diskPath: $row['disk_path'],
            fileName: $row['file_name'],
            originalName: $row['original_name'],
            createdAt: $row['created_at'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'disk_path'     => $this->diskPath,
            'file_name'     => $this->fileName,
            'original_name' => $this->originalName,
            'created_at'    => $this->createdAt,
        ];
    }
}
