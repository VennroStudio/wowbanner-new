<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Order\Permission\OrderPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class OrderPermissionService
{
    /** @throws AccessDeniedException */
    public function check(UserRole $currentUserRole, OrderPermission $action): void
    {
        if (!$this->hasAccess($currentUserRole, $action)) {
            throw new AccessDeniedException();
        }
    }

    public function hasAccess(UserRole $currentUserRole, OrderPermission $action): bool
    {
        return \in_array($currentUserRole, $this->getAllowedRolesForAction($action), true);
    }

    /** @return list<UserRole> */
    private function getAllowedRolesForAction(OrderPermission $action): array
    {
        $adminRoles = [UserRole::ADMIN];

        return match ($action) {
            OrderPermission::CREATE,
            OrderPermission::UPDATE,
            OrderPermission::DELETE => $adminRoles,
        };
    }
}
