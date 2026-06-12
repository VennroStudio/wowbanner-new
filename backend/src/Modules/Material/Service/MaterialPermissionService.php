<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class MaterialPermissionService
{
    /** @throws AccessDeniedException */
    public function checkRole(UserRole $currentUserRole, MaterialPermission $action): void
    {
        if (!\in_array($currentUserRole, $this->getAllowedRolesForAction($action), true)) {
            throw new AccessDeniedException();
        }
    }

    /** @return list<UserRole> */
    private function getAllowedRolesForAction(MaterialPermission $action): array
    {
        $adminRoles = [
            UserRole::ADMIN,
        ];

        return match ($action) {
            MaterialPermission::CREATE,
            MaterialPermission::UPDATE,
            MaterialPermission::DELETE => $adminRoles,
        };
    }
}
