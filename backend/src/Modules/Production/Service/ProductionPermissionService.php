<?php

declare(strict_types=1);

namespace App\Modules\Production\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Production\Permission\ProductionPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class ProductionPermissionService
{
    /** @throws AccessDeniedException */
    public function check(UserRole $currentUserRole, ProductionPermission $action): void
    {
        if (!$this->hasAccess($currentUserRole, $action)) {
            throw new AccessDeniedException();
        }
    }

    public function hasAccess(UserRole $currentUserRole, ProductionPermission $action): bool
    {
        return \in_array($currentUserRole, $this->getAllowedRolesForAction($action), true);
    }

    /** @return list<UserRole> */
    private function getAllowedRolesForAction(ProductionPermission $action): array
    {
        $adminRoles = [
            UserRole::ADMIN,
        ];

        return match ($action) {
            ProductionPermission::CREATE,
            ProductionPermission::UPDATE,
            ProductionPermission::DELETE => $adminRoles,
        };
    }
}
