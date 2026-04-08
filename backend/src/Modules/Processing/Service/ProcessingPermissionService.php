<?php

declare(strict_types=1);

namespace App\Modules\Processing\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Processing\Permission\ProcessingPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class ProcessingPermissionService
{
    /** @throws AccessDeniedException */
    public function check(UserRole $currentUserRole, ProcessingPermission $action): void
    {
        if (!$this->hasAccess($currentUserRole, $action)) {
            throw new AccessDeniedException();
        }
    }

    public function hasAccess(UserRole $currentUserRole, ProcessingPermission $action): bool
    {
        return \in_array($currentUserRole, $this->getAllowedRolesForAction($action), true);
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
