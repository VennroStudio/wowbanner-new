<?php

declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Product\Permission\ProductPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class ProductPermissionService
{
    /** @throws AccessDeniedException */
    public function check(UserRole $currentUserRole, ProductPermission $action): void
    {
        if (!$this->hasAccess($currentUserRole, $action)) {
            throw new AccessDeniedException();
        }
    }

    public function hasAccess(UserRole $currentUserRole, ProductPermission $action): bool
    {
        return \in_array($currentUserRole, $this->getAllowedRolesForAction($action), true);
    }

    /** @return list<UserRole> */
    private function getAllowedRolesForAction(ProductPermission $action): array
    {
        $adminRoles = [
            UserRole::ADMIN,
        ];

        return match ($action) {
            ProductPermission::CREATE,
            ProductPermission::UPDATE,
            ProductPermission::DELETE => $adminRoles,
        };
    }
}
