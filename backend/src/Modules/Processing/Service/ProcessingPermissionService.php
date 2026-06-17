<?php

declare(strict_types=1);

namespace App\Modules\Processing\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Processing\Permission\ProcessingPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class ProcessingPermissionService
{
    /** @throws AccessDeniedException */
    public function checkRole(UserRole $currentUserRole, ProcessingPermission $action): void
    {
        if (!\in_array($currentUserRole, $this->getAllowedRolesForAction($action), true)) {
            throw new AccessDeniedException();
        }
    }

    /** @return list<UserRole> */
    private function getAllowedRolesForAction(ProcessingPermission $action): array
    {
        $adminRoles = [
            UserRole::ADMIN,
        ];

        return match ($action) {
            ProcessingPermission::CREATE,
            ProcessingPermission::UPDATE,
            ProcessingPermission::DELETE => $adminRoles,
        };
    }
}
