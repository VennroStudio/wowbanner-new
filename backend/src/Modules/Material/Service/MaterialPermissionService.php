<?php

declare(strict_types=1);

namespace App\Modules\Material\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Material\Permission\MaterialPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class MaterialPermissionService
{
    /** @throws AccessDeniedException */
    public function check(UserRole $currentUserRole, MaterialPermission $action): void
    {
        if (!$this->hasAccess($currentUserRole, $action)) {
            throw new AccessDeniedException();
        }
    }

    public function hasAccess(UserRole $currentUserRole, MaterialPermission $action): bool
    {
        return \in_array($currentUserRole, $this->getAllowedRolesForAction($action), true);
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
