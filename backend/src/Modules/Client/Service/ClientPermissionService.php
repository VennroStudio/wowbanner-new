<?php

declare(strict_types=1);

namespace App\Modules\Client\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\Client\Permission\ClientPermission;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;

final readonly class ClientPermissionService
{
    /** @throws AccessDeniedException */
    public function checkRole(UserRole $currentUserRole, ClientPermission $action): void
    {
        if (!\in_array($currentUserRole, $this->getAllowedRolesForAction($action), true)) {
            throw new AccessDeniedException();
        }
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
