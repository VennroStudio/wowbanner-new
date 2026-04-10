<?php

declare(strict_types=1);

namespace App\Modules\Client\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Client\Permission\ClientPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class ClientPermissionService
{
    /** @throws AccessDeniedException */
    public function check(UserRole $currentUserRole, ClientPermission $action): void
    {
        if (!$this->hasAccess($currentUserRole, $action)) {
            throw new AccessDeniedException();
        }
    }

    public function hasAccess(UserRole $currentUserRole, ClientPermission $action): bool
    {
        return \in_array($currentUserRole, $this->getAllowedRolesForAction($action), true);
    }

    /** @return list<UserRole> */
    private function getAllowedRolesForAction(ClientPermission $action): array
    {
        $adminRoles = [
            UserRole::ADMIN,
        ];

        return match ($action) {
            ClientPermission::CREATE,
            ClientPermission::UPDATE,
            ClientPermission::DELETE => $adminRoles,
        };
    }
}
