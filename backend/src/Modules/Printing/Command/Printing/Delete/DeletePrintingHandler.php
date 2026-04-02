<?php

declare(strict_types=1);

namespace App\Modules\Printing\Command\Printing\Delete;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Printing\Entity\Printing\PrintingRepository;
use App\Modules\Printing\Permission\PrintingPermission;
use App\Modules\Printing\Service\PrintingPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class DeletePrintingHandler
{
    public function __construct(
        private PrintingRepository $printingRepository,
        private PrintingPermissionService $printingPermissionService,
        private FlusherInterface $flusher,
        private Cacher $cacher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(DeletePrintingCommand $command): void
    {
        $printing = $this->printingRepository->getById($command->printingId);

        $this->printingPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: PrintingPermission::DELETE,
        );

        $this->printingRepository->remove($printing);

        $this->cacher->delete('printing_by_id_' . $command->printingId);

        $this->flusher->flush();
    }
}
