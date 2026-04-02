<?php

declare(strict_types=1);

namespace App\Modules\Printing\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Printing\Permission\PrintingPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class PrintingPermissionService
{
    /** @throws AccessDeniedException */
    public function check(UserRole $currentUserRole, PrintingPermission $action): void
    {
        if (!$this->hasAccess($currentUserRole, $action)) {
            throw new AccessDeniedException();
        }
    }

    public function hasAccess(UserRole $currentUserRole, PrintingPermission $action): bool
    {
        return \in_array($currentUserRole, $this->getAllowedRolesForAction($action), true);
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
