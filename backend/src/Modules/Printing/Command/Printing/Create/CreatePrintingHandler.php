<?php

declare(strict_types=1);

namespace App\Modules\Printing\Command\Printing\Create;

use App\Components\Exception\AccessDeniedException;
use App\Components\Flusher\FlusherInterface;
use App\Modules\Printing\Entity\Printing\Printing;
use App\Modules\Printing\Entity\Printing\PrintingRepository;
use App\Modules\Printing\Permission\PrintingPermission;
use App\Modules\Printing\Service\PrintingPermissionService;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class CreatePrintingHandler
{
    public function __construct(
        private PrintingRepository $printingRepository,
        private PrintingPermissionService $printingPermissionService,
        private FlusherInterface $flusher,
    ) {}

    /** @throws AccessDeniedException */
    public function handle(CreatePrintingCommand $command): void
    {
        $this->printingPermissionService->check(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: PrintingPermission::CREATE,
        );

        $printing = Printing::create(name: $command->name);

        $this->printingRepository->add($printing);
        $this->flusher->flush();
    }
}
