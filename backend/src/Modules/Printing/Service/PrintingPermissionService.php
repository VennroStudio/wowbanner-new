<?php

declare(strict_types=1);

namespace App\Modules\Printing\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Printing\Permission\PrintingPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class PrintingPermissionService
{
    /** @throws AccessDeniedException */
    public function checkRole(UserRole $currentUserRole, PrintingPermission $action): void
    {
        if (!\in_array($currentUserRole, $this->getAllowedRolesForAction($action), true)) {
            throw new AccessDeniedException();
        }
    }

    /** @return list<UserRole> */
    private function getAllowedRolesForAction(PrintingPermission $action): array
    {
        $adminRoles = [
            UserRole::ADMIN,
        ];

        return match ($action) {
            PrintingPermission::CREATE,
            PrintingPermission::UPDATE,
            PrintingPermission::DELETE => $adminRoles,
        };
    }
}
