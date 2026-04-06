<?php

declare(strict_types=1);

namespace App\Modules\Material\Command\MaterialImage\Update;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Material\Entity\MaterialImage\MaterialImageRepository;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\Material\Service\MaterialPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class UpdateMaterialImageHandler
{
    public function __construct(
        private MaterialImageRepository $materialImageRepository,
        private MaterialPermissionService $materialPermissionService,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(UpdateMaterialImageCommand $command): void
    {
        $this->materialPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: MaterialPermission::UPDATE,
        );

        $image = $this->materialImageRepository->getById($command->materialImageId);

        $image->edit(null, $command->alt);

        $this->flusher->flush();
    }
}
